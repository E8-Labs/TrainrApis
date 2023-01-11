<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserProfileLiteResource;
use Illuminate\Support\Facades\DB;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $profileFrom = DB::table('profiles')
        ->join('invoices', 'profiles.user_id', '=', 'invoices.from_id')
        ->where('invoices.id', '=', $this->id)
        ->select("*")
        ->first();

        $profileTo = DB::table('profiles')
        ->join('invoices', 'profiles.user_id', '=', 'invoices.to_id')
        ->where('invoices.id', '=', $this->id)
        ->select("*")
        ->first();

        return [
            "id" => (int)$this->id,
            "title" => $this->title,
            "invoice_description" => $this->invoice_description,
            'invoice_status' => (int)$this->invoice_status,
            "price" => $this->price,
            "fromUser" => new UserProfileLiteResource($profileFrom),
            "toUser" => new UserProfileLiteResource($profileTo),
            // "seed" => $seed,
            // 'ids' => $profiles,
        ];
    }
}
