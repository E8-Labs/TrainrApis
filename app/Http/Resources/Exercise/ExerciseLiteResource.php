<?php

namespace App\Http\Resources\Exercise;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\ExerciseType;
use App\Models\MuscleGroup;

class ExerciseLiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $sets = ExerciseSet::where('exercise_id', $this->id)
                ->get();
        return [
            "id" => $this->id,
            "exercise_title" => $this->exercise_title,
            "cover_image"=> $this->cover_image,
            "youtube_url"=> $this->youtube_url,
            "set_count" => $this->set_count,
            "sets"      => $sets,
            "difficulty"=> $this->difficulty,
            "muscle_group"=> $this->muscle_group,
            "exercise_type"=> $this->exercise_type,
            "user_id"=> $this->user_id,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at

        ];
    }
}
