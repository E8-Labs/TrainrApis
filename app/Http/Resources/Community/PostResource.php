<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Profile;
use App\Models\Community\PostComment;
use App\Models\Community\PostLike;
use App\Http\Resources\UserProfileLiteResource;
use App\Models\Community\PostTaggedUsers;
use Illuminate\Support\Facades\Auth;


class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $profile = Profile::where('user_id', $this->user_id)->first();
        $userids = PostTaggedUsers::where('post_id', $this->id)->pluck('user_id')->toArray();
        $users = Profile::whereIn('user_id', $userids)->get();

        $likesCount = PostLike::where('post_id', $this->id)->count('id');
        $commentsCount = PostComment::where('post_id', $this->id)->count('id');

        $isLiked = false;
        $liked = PostLike::where('post_id', $this->id)->where('user_id', Auth::user()->id)->first();
        if($liked){
            $isLiked = true;
        }
        else{

        }

        return [
            "id" => $this->id,
            // "name" => $this->name,
            "post_description" => $this->post_description,
            'profile' => new UserProfileLiteResource($profile),
            'post_media' => \Config::get('constants.base_url').$this->post_image,
            "privacy" => (int)$this->post_privacy,
            "image_height" => $this->image_height,
            "image_width" => $this->image_width,
            'total_comments' => $commentsCount,
            'total_likes' => $likesCount,
            'tagged_users' => UserProfileLiteResource::collection($users),
            'is_liked_by_me' => $isLiked,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
