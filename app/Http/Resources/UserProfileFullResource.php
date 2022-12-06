<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\User\UserExpertise;
use App\Models\User\AllExpertise;

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

        $expertise = AllExpertise::join('user_expertises', 'user_expertises.expertise_id', '=', 'all_expertises.id')
        ->where('user_expertises.user_id', $user->id)->select(['user_expertises.id', 'name', 'icon_image'])->get();
        // $expertise = UserExpertise::where('user_id', $user->id)->get();
        return [
            "id" => $this->user_id,
            "email" => $user->email,
            "name" => $this->full_name,
            "username" => $this->username,
            "profile_image" => \Config::get('constants.base_url').$this->image_url,
            "authProvider" => $p,
            'user_expertise' => $expertise,
            'city' => $this->city,
            "state" => $this->state,
            'lat' => $this->lat,
            'lang' => $this->lang,
             'role' => $user->role,
             'bio' => $this->bio,

        ];
    }
}
