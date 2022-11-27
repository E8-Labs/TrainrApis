<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class UserProfileFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = User::where('id', $this->user_id)->first();
        // $url = $this->image_url;
        $p = $user->provider_name;
        if($p === NULL){
            $p = "email";
        }
        return [
            "id" => $this->user_id,
            "email" => $user->email,
            "name" => $this->full_name,
            "profile_image" => \Config::get('constants.profile_images').$this->image_url,
            "authProvider" => $p,
            'city' => $this->city,
            "state" => $this->state,
            'lat' => $this->lat,
            'lang' => $this->lang,
            "last_seen" => $user->last_seen,
             "user_id" => $user->id,
             'role' => $user->role,
             "map_visibility" => (int)$this->map_visibility,
             
            
            

            // "unread_notifications" => $count,
            // "unread_messages" => $unread_messages,

        ];
    }
}
