<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserProfileLiteResource;
use Illuminate\Support\Facades\DB;

class SubscriptionPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $profile = DB::table('profiles')
        ->join('subscription_packages', 'profiles.user_id', '=', 'subscription_packages.user_id')
        ->where('subscription_packages.id', '=', $this->id)
        ->select("*")
        ->first();
        return [
            "id" => (int)$this->id,
            "plan_name" => $this->package_name,
            "plan_description" => $this->package_description,
            'occurrence' => (int)$this->occurrence,
            "price" => (int)$this->price,
            "profile" => new UserProfileLiteResource($profile),
            // "seed" => $seed,
            // 'ids' => $profiles,
        ];
    }
}
