<?php

namespace App\Http\Controllers\Trainr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\WorkoutExercise;
use App\Models\Exercise\Workout;

use App\Http\Resources\Exercise\WorkoutFullResource;
use App\Http\Resources\Exercise\ExerciseTypeResource;

class TrainrWorkoutProgramController extends Controller
{
    //

    function AddWorkout(Request $request){
    	$user = Auth::user();
    	if($user){
    		$data = $request->json()->all();
    		DB::beginTransaction();
    		$workout = new Workout;
    		$workout->name = $data["name"];
    		$workout->description = $data["description"];
    		$workout->trainr_id = $user->id;
    		$workout->client_id = $data["client"];
    		$saved = $workout->save();
    		if($saved){
    			$workoutTimes = $data["workouts"];
    			// echo json_encode($workoutTimes);
    			// DB::rollBack();
    			foreach($workoutTimes as $time){
    				
    				
    				$wTime = new WorkoutTime;
    				$wTime->time = $time["time"];
    				$wTime->day = $time["day"];
    				$wTime->workout_id = $workout->id;
    				$workTimeSaved = $wTime->save();

    				$exercise_ids = $time["exercise_ids"];
    				foreach($exercise_ids as $id){
    					$wex = new WorkoutExercise;
    					$wex->exercise_id = $id;
    					$wex->worktime_id = $wTime->id;
    					$wexSaved = $wex->save();
    				}
    			}
    			DB::commit();

    			return response()->json(['data'=> new WorkoutFullResource($workout), 'message' => 'Workout program created', 'status' => true]);

    		}
    		else{
    			return response()->json(['data'=> null, 'message' => 'Error saving workout', 'status' => false]);
    		}

    	}
    	else{
    		return response()->json(['data'=> null, 'message' => 'Unauthorized access', 'status' => false]);
    	}
    }
}
