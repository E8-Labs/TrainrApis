<?php

namespace App\Http\Resources\Exercise;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\WorkoutExercise;
use App\Models\Exercise\Workout;
use App\Models\Exercise;
use App\Models\Profile;
use App\Http\Resources\UserProfileLiteResource;

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

        $wout = Workout::where('id', $this->id)->first();
        $trainr = Profile::where('user_id', $wout->trainr_id)->first();
        $client = Profile::where('user_id', $wout->client_id)->first();

        $workTimes = WorkoutTime::where('workout_id', $this->id)->get();
        if($workTimes){
            foreach($workTimes as $time){
                $exs = Exercise::join('workout_exercises', 'exercises.id', '=', 'workout_exercises.exercise_id')->where('worktime_id', $time->id)->select('exercises.*')->get();
                $time->exercises = ExerciseLiteResource::collection($exs);
            }
        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "descrition" => $this->description,
            'trainr' => new UserProfileLiteResource($trainr),
            'client' => new UserProfileLiteResource($client),
            "daily_exercies" => $workTimes,
            "percentage" => (double)$this->percentage,
            "total_reps" => (int)$this->total_reps,
            "total_reps_performed" => (int)$this->total_reps_performed,
            'completed_workout_ids' => $this->completed_workout_ids,
        ];
    }
}
