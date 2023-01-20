<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserProfileLiteResource;
use App\Models\Community\PostTaggedUsers;
use App\Models\Profile;

class PostLikeResource extends JsonResource
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
        
        return [
            "id" => $this->id,
            'profile' => new UserProfileLiteResource($profile),
            'post_id' => $this->post_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
