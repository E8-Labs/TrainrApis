<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\User\UserExpertise;
use App\Models\User\AllExpertise;
use App\Models\UserTrainr;
use App\Models\Profile;

use App\Models\Exercise\Goal;
use App\Models\Exercise\HealthCondition;
use App\Models\Exercise\ClientHealthConditionsModel;
use App\Models\Exercise\WorkoutFrequency;
use App\Models\User\InstagramProfileModel;

// use App\Http\Resources\UserProfileFullResource;
use App\Http\Resources\UserProfileLiteResource;


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
        $goals = Goal::where("user_id", $user->id)->get();
        $trainr = Profile::join('user_trainrs', 'profiles.user_id', '=', 'user_trainrs.trainr_id')
        ->where('client_id', $user->id)->first();

        $expertise = AllExpertise::join('user_expertises', 'user_expertises.expertise_id', '=', 'all_expertises.id')
        ->where('user_expertises.user_id', $user->id)->select(['user_expertises.id', 'name', 'icon_image'])->get();

        $insta = InstagramProfileModel::where('user_id', $user->id)->first();
        // $goals = Goal::where('user_id', $user->id)->get();
        // $expertise = UserExpertise::where('user_id', $user->id)->get();
        return [
            "id" => (int)$this->user_id,
            "user_id" => (int)$this->user_id,
            "comment" => "User_id and id will be same because id => this->user_id",
            "email" => $user->email,
            "name" => $this->full_name,
            "username" => $this->username,
            "profile_image" => \Config::get('constants.base_url').$this->image_url,
            "authProvider" => $p,
            'user_expertise' => $expertise,
            'city' => $this->city,
            "state" => $this->state,
            "goals" => $goals,
            'trainr' => new UserProfileLiteResource($trainr),
            'lat' => $this->lat,
            'lang' => $this->lang,
             'role' => (int)$this->role,
             'bio' => $this->bio,
             'weight' => (int)$this->weight,
             'workout_frequency' => (int)$this->workout_frequency,
             'sleep_hours' => (int)$this->sleep_hours,
             "height" => [
                "height_inches" => (int)$this->height_inches,
                "height_feet" => (int)$this->height_feet,
             ],
             "instagram_profile" => $insta

        ];
    }
}
