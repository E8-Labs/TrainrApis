<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ChatLiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $profiles = DB::table('profiles')
        ->join('chat_users', 'profiles.user_id', '=', 'chat_users.user_id')
        ->where('chat_users.chat_id', '=', $this->chat_id)
        ->select("*")
        ->get();

        return [
            "chat_id" => (int)$this->chat_id,
            "last_message" => $this->lastmessage,
            "chat_type" => (int)$this->chat_type,

            "last_message_date" => $this->last_message_date,
            "users" => ChatProfileResource::collection($profiles),
            // "seed" => $seed,
            // 'ids' => $profiles,
        ];
    }
}
