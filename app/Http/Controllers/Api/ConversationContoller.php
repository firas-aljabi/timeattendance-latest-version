<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageCreated;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use App\Statuses\UserTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConversationContoller extends Controller
{
    public function getHrsList()
    {
        $hrs = User::where('type', UserTypes::HR)->where('company_id', auth()->user()->company_id)->get();
        return $hrs;
    }
    public function index()
    {
        $user = Auth::user();
        return $user->conversations()->with([
            'lastMessage',
            'participants' => function ($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },
        ])->get();
    }

    public function show($id)
    {
        $user = Auth::user();
        return $user->conversations()->with([
            'lastMessage',
            'participants' => function ($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },
        ])->findOrFail($id);
    }

    public function show_conversation_messages($id)
    {
        $user = Auth::user();
        $conversation = $user->conversations()
            ->with(['participants' => function ($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            }])
            ->findOrFail($id);

        $messages = $conversation->messages()
            ->with('user')
            ->where(function ($query) use ($user) {
                $query
                    ->where(function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->whereNull('deleted_at');
                    })
                    ->orWhereRaw('id IN (
                        SELECT message_id FROM recipients
                        WHERE recipients.message_id = messages.id
                        AND recipients.user_id = ?
                        AND recipients.deleted_at IS NULL
                    )', [$user->id]);
            })
            ->latest()
            ->get();

        return [
            'conversation' => $conversation,
            'messages' => $messages,
        ];
    }
    public function store(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string'],
            'conversation_id' => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->input('user_id');
                }),
                'integer',
                'exists:conversations,id'
            ],
            'user_id' => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->input('conversation_id');
                }),
                'integer',
                'exists:users,id'
            ]
        ]);
        $user = Auth::user();
        $conversation_id = $request->post('conversation_id');
        $user_id = $request->post('user_id');

        Db::beginTransaction();
        try {

            if ($conversation_id) {
                $conversation = $user->conversations()->findOrFail($conversation_id);
            } else {
                $conversation = Conversation::where('type', 'peer')->whereHas('participants', function ($builder)  use ($user_id, $user) {
                    $builder->join('participants as participants2', 'participants2.conversation_id', 'participants.conversation_id')
                        ->where('participants.user_id', $user_id)
                        ->where('participants2.user_id', $user->id);
                })->first();
                if (!$conversation) {
                    $conversation = Conversation::create([
                        'user_id' => $user->id,
                        'type' => 'peer'
                    ]);
                    $conversation->participants()->attach([
                        $user->id => ['joined_at' => now()],
                        $user_id => ['joined_at' => now()],
                    ]);
                }
            }

            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body' => $request->post('message')
            ]);


            DB::statement('
            INSERT INTO recipients (user_id, message_id)
            SELECT user_id, ? FROM participants
            WHERE conversation_id = ?
            AND user_id <> ?
        ', [$message->id, $conversation->id, $user->id]);

            $conversation->update(['last_message_id' => $message->id]);


            DB::commit();

            $message->load('user');

            broadcast(new MessageCreated($message));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return $message;
    }
}
