<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Http\Resources\Chat\ChatProfileResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Payment\InvoiceResource;
use App\Models\Payment\Invoice;

class ChatMessageResource extends JsonResource
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
        $url = null;

        $debug = env('APP_DEBUG');

        $base = \Config::get('constants.base_url');
        // if($debug === true){
        //     $base = \Config::get('constants.profile_images_clone');
        // }

        if($this->image_url){
            $url = $base . $this->image_url;
        }

        $invoice = Invoice::where('id', $this->invoice_id)->first();

        return [
            "id" => $this->id,
            "message" => $this->message,
            "image_url" => $url,
            'chat_id' => (int)$this->chat_id,
            "image_width" => $this->image_width,
            'image_height' => $this->image_height,
            "user" => new ChatProfileResource($profile),
            "created_at" => $this->created_at,
            'invoice' => new InvoiceResource($invoice)
            // 'ids' => $profiles,
        ];
    }
}
