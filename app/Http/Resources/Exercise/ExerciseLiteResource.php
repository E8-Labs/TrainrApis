<?php

namespace App\Http\Resources\Exercise;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\ExerciseType;
use App\Models\MuscleGroup;

use App\Http\Resources\Exercise\ExerciseTypeResource;

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

                $type = ExerciseType::where('id', $this->exercise_type)->first();
                $group = MuscleGroup::where('id', $this->muscle_group)->first();
        return [
            "id" => $this->id,
            "exercise_title" => $this->exercise_title,
            "cover_image"=> \Config::get('constants.base_url') . $this->cover_image,
            "youtube_url"=> $this->youtube_url,
            "set_count" => $this->set_count,
            "sets"      => $sets,
            "difficulty"=> $this->difficulty,
            "muscle_group"=> $group,
            "exercise_type"=> $type,
            "user_id"=> $this->user_id,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at

        ];
    }
}
