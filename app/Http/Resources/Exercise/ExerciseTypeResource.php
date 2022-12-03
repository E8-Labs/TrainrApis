<?php

namespace App\Http\Resources\Exercise;

use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'name', $this->name,
            'icon_image' => \Config.get('constants.base_url') . $this->icon_image;
        ];
    }
}
