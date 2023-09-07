<?php

namespace App\Http\Resources\Posts;


use App\Http\Resources\Admin\EmployeeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'likes_count' => $this->likes_count,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'image' => $this->user->image,
                    'position' => $this->user->position,
                ];
            }),
            'post_id' => $this->whenLoaded('post', function () {
                return $this->post->id;
            }),
            'likes' => LikeResource::collection($this->whenLoaded('likes')),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null
        ];
    }
}
