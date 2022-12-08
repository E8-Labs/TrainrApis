<?php

namespace App\Http\Resources\Exercise;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\WorkoutExercise;
use App\Models\Exercise\Workout;
use App\Models\Exercise;

use App\Http\Resources\Exercise\ExerciseLiteResource;
use App\Http\Resources\Exercise\ExerciseTypeResource;

class WorkoutFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $workTimes = WorkoutTime::where('workout_id', $this->id)->get();
        if($workTimes){
            foreach($workTimes as $time){
                $exs = Exercise::join('workout_exercises', 'exercises.id', '=', 'workout_exercises.exercise_id')->select('exercises.*')->get();
                $time->exercises = ExerciseLiteResource::collection($exs);
            }
        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "descrition" => $this->description,
            "daily_exercies" => $workTimes,
        ];
    }
}
