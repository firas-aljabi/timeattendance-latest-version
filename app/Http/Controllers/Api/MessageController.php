<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Events\PrivateMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminResource;
use App\Http\Resources\PaginationResource;
use App\Models\Message;
use App\Models\User;
use App\Services\Admin\AdminService;
use Illuminate\Http\Request;

/**
 * @group Messages
 *
 * @authenticated
 *
 * APIs for managing Messages
 */

class MessageController extends Controller
{
    public function __construct(private AdminService $adminService)
    {
    }
    /**
     * Get Hr List
     *
     * This endpoint is used to Get Hr List in Company and Admin Or Hr Or Employee Can Access To This Api.
     *
     *@response 201 scenario="Hr List"{
     *"data": [
     *   {
     *  "id": 4,
     *  "name": "hamza Fawaz",
     *  "email": "hamzafawaz123@gmail.com",
     *  "type": 3,
     *  "image": null
     *},
     * {
     *     "id": 5,
     *    "name": "mariam kabani",
     *    "email": "mariamkabani123@gmail.com",
     *    "type": 3,
     *    "image": null
     *}
     *]
     *}
     */
    public function getHrsList()
    {
        $data = $this->adminService->getHrsList();

        $returnData = AdminResource::collection($data);

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }
    /**
     * Get Private Messages
     *
     * This endpoint is used to Get Private Messages.
     *
     *  @urlParam user_id int required The user ID, must be in the Users table.
     * @response 200 scenario="Get Private Messages"{
     *"messages": [
     * {
     * "id": 1,
     * "user_id": 3,
     * "receiver_id": 4,
     *"message": "please tell me more",
     *"created_at": "2023-08-27T19:24:36.000000Z",
     *"updated_at": "2023-08-27T19:24:36.000000Z",
     *"user": {
     * "id": 3,
     * "name": "mouaz alkhateeb",
     * "image": "employees/2023-08-27-Employee-8.jpg"
     * }
     *},
     *{
     * "id": 2,
     * "user_id": 3,
     * "receiver_id": 4,
     *"message": "yesss",
     * "created_at": "2023-08-27T19:43:08.000000Z",
     *"updated_at": "2023-08-27T19:43:08.000000Z",
     * "user": {
     * "id": 3,
     * "name": "mouaz alkhateeb",
     *"image": "employees/2023-08-27-Employee-8.jpg"
     * }
     *}
     *]
     *}
     */
    public function privateMessages(User $user)
    {
        $privateCommunication = Message::with(['user' => function ($query) {
            $query->select('id', 'name', 'image');
        }])
            ->where(['user_id' => auth()->id(), 'receiver_id' => $user->id])
            ->orWhere(function ($query) use ($user) {
                $query->where(['user_id' => $user->id, 'receiver_id' => auth()->id()]);
            })
            ->get();
        return response(['messages' => $privateCommunication]);
    }
    /**
     * Send Private Message
     *
     * This endpoint is used to send a private message.
     *
     * @urlParam user_id int required The user ID, must be in the Users table.
     * @bodyParam message string required The message content. Example: please tell me more
     *
     * @response 200 scenario="Create New Alert For Employee"{
     *     "status": "Message private sent successfully",
     *          "message": {
     *          "message": "please tell me more",
     *          "receiver_id": 4,
     *          "user_id": 3,
     *          "updated_at": "2023-08-27T19:24:36.000000Z",
     *          "created_at": "2023-08-27T19:24:36.000000Z",
     *          "id": 1,
     *          "user": {
     *              "id": 3,
     *              "name": "mouaz alkhateeb",
     *              "image": "employees/2023-08-27-Employee-8.jpg"
     *          }
     *      }
     * }
     */
    public function sendPrivateMessage(Request $request, User $user)
    {
        $input = $request->all();
        $input['receiver_id'] = $user->id;
        $message = auth()->user()->messages()->create($input);


        broadcast(new PrivateMessageEvent($message->load('user:id,name,image')))->toOthers();

        return response(['status' => 'Message private sent successfully', 'message' => $message]);
    }
}
