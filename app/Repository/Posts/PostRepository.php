<?php

namespace App\Repository\Posts;

use App\Events\AddCommentEvent;
use App\Events\AddLikeEvent;
use App\Events\AddLikeToCommentEvent;
use App\Http\Trait\UploadImage;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Share;
use App\Repository\BaseRepositoryImplementation;
use App\Services\Posts\PostService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PostRepository extends BaseRepositoryImplementation
{
    use UploadImage;
    public function getFilterItems($filter)
    {
        $records = Post::query()->with(['user', 'comments.user', 'comments.post', 'likes.user', 'shares.user', 'comments.likes']);

        $records->when(isset($filter->orderBy), function ($records) use ($filter) {
            $records->orderBy($filter->getOrderBy(), $filter->getOrder());
        });

        return $records->paginate($filter->per_page);
    }

    public function getMyPosts()
    {
        $records = Post::query()->where('user_id', Auth::id());

        return $records->with(['user', 'comments.user', 'comments.post', 'likes.user', 'shares.user', 'comments.likes'])->paginate();
    }

    public function create_post($data)
    {
        DB::beginTransaction();

        try {

            $post = new Post();
            $post->user_id = Auth::id();
            $post->content = $data['content'];
            if (Arr::has($data, 'image')) {
                $file = Arr::get($data, 'image');
                $file_name = $this->uploadPostsAttachment($file);
                $post->image = $file_name;
            }
            $post->save();


            DB::commit();

            return $post->load(['user', 'comments', 'likes', 'shares']);
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function create_comment($data)
    {
        DB::beginTransaction();

        try {

            $comment = new Comment();
            $comment->user_id = Auth::id();
            $comment->post_id = $data['post_id'];
            $comment->content = $data['content'];
            $comment->save();



            $user = Auth::user();
            $post = Post::findOrFail($comment->post_id);
            $notifier = $post->user;

            event(new AddCommentEvent($notifier, $post, $user));

            DB::commit();

            return $comment->load('user', 'post');
        } catch (\Exception $e) {
            DB::rollback();
        }
    }


    public function add_like($data)
    {
        DB::beginTransaction();
        $like = null;
        try {
            $existingLike = Like::where('user_id', Auth::id())
                ->where('post_id', $data['post_id'])
                ->first();

            if ($existingLike) {
                $existingLike->delete();

                PostService::decreasePostLikesCount($data['post_id']);
            } else {
                $like = new Like();
                $like->user_id = Auth::id();
                $like->post_id = $data['post_id'];
                $like->save();

                PostService::increasePostLikesCount($like->post_id);

                $user = Auth::user();
                $post = Post::findOrFail($like->post_id);
                $notifier = $post->user;

                event(new AddLikeEvent($notifier, $post, $user));
            }

            DB::commit();

            if ($like != null) {
                return $like->load(['user']);
            } else {
                return $like;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function add_like_comment($data)
    {
        DB::beginTransaction();
        $like = null;
        try {
            $existingLike = Like::where('user_id', Auth::id())
                ->where('comment_id', $data['comment_id'])
                ->first();

            if ($existingLike) {
                $existingLike->delete();

                PostService::decreaseCommentLikesCount($data['comment_id']);
            } else {
                $like = new Like();
                $like->user_id = Auth::id();
                $like->comment_id = $data['comment_id'];
                $like->save();

                PostService::increaseCommentLikesCount($like->comment_id);

                $user = Auth::user();
                $comment = Comment::findOrFail($like->comment_id);
                $notifier = $comment->user;

                event(new AddLikeToCommentEvent($notifier, $comment, $user));
            }

            DB::commit();

            if ($like != null) {
                return $like->load(['user']);
            } else {
                return $like;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function share_post($data)
    {
        DB::beginTransaction();
        $share = null;
        try {
            $existingShare = Share::where('user_id', Auth::id())
                ->where('post_id', $data['post_id'])
                ->first();

            if ($existingShare) {
                $existingShare->delete();

                PostService::decreasePostSharesCount($data['post_id']);
            } else {
                $share = new Share();
                $share->user_id = Auth::id();
                $share->post_id = $data['post_id'];
                $share->save();

                PostService::increasePostSharesCount($share->post_id);
            }

            DB::commit();

            if ($share != null) {
                return $share->load(['user', 'post.user']);
            } else {
                return $share;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return Post::class;
    }
}
