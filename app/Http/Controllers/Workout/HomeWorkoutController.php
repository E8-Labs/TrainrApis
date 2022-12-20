<?php

namespace App\Http\Controllers\Workout;

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
// use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\CompletedWorkouts;
use App\Models\Exercise\CompletedWorkoutExercise;
use App\Models\ExerciseType;
use App\Models\MuscleGroup;

use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\WorkoutExercise;
use App\Models\Exercise\Workout;

use App\Http\Resources\Exercise\ExerciseLiteResource;
use App\Http\Resources\Exercise\ExerciseTypeResource;
use App\Http\Resources\Exercise\WorkoutFullResource;

use Carbon\Carbon;

class HomeWorkoutController extends Controller
{
    function getClientDashboardData(Request $request){
    	$user = Auth::user();

    	$meal_percentage = 0;
    	$wout_percentage = 0;

    	if($user){
    		$dateString = $request->date;
    		$date = Carbon::createFromFormat('m-d-Y', $dateString);
			$day_name = $date->format('l');// get the day name

			//check if this workout is supposed to happen this day
			$query = Workout::join('workout_times', 'workouts.id', '=', 'workout_times.workout_id')
			->where('workout_times.day', $day_name)
			->where('workouts.client_id', $user->id);
			$woutIds = $query->pluck('workouts.id')->toArray();
			$workouts = $query->select('workouts.*')->get();

			$wtimeIds = WorkoutTime::whereIn('workout_id', $woutIds)->pluck('id')->toArray();

			// return $woutIds;
			//get total number of reps on this day added by trainr
			$ex_ids = WorkoutExercise::whereIn('worktime_id', $wtimeIds)->pluck('exercise_id')->toArray();
			// return $ex_ids;
    		$totalRepsOnThisDay = ExerciseSet::whereIn('exercise_id', $ex_ids)->sum('rep_count');

    		//Get the total number of reps completed by client on this day
    		$completed_wout_ids = CompletedWorkouts::where('completed_date', $dateString)->where('user_id', $user->id)->pluck('id')->toArray();
    		$totalRepsUserPerformed = CompletedWorkoutExercise::whereIn('completed_workout_id', $completed_wout_ids)->get()->sum(function($t){ 
    			return $t->reps;// * $t->sets; 
			});

    		if($totalRepsOnThisDay > 0){
    		    $wout_percentage = 100 * $totalRepsUserPerformed / $totalRepsOnThisDay;
    		}
    		else{
    		    $wout_percentage = 0;
    		}

            foreach($workouts as $w){
                $w_work_time_ds = WorkoutTime::where('workout_id', $w->id)->pluck('id')->toArray();
                $w_ex_ids = WorkoutExercise::whereIn('worktime_id', $w_work_time_ds)->pluck('exercise_id')->toArray();
                // return $ex_ids;
                $totalReps = ExerciseSet::whereIn('exercise_id', $w_ex_ids)->sum('rep_count');
                $w->total_reps = $totalReps;

                $completed_wout_ids = CompletedWorkouts::where('completed_date', $dateString)
                ->where('user_id', $user->id)
                ->where('workout_id', $w->id)
                ->pluck('id')->toArray();
                $totalRepsUserPerformedForThisWorkout = CompletedWorkoutExercise::whereIn('completed_workout_id', $completed_wout_ids)->get()->sum(function($t){ 
                    return $t->reps;// * $t->sets; 
                });
                if($totalReps > 0){
                    $w->total_reps_performed = $totalRepsUserPerformedForThisWorkout;
                    $w->percentage = 100 * $totalRepsUserPerformedForThisWorkout / $totalReps;
                }
                else{
                    $w->total_reps_performed = $totalRepsUserPerformedForThisWorkout;
                    $w->percentage = 0;
                }
                
            }

			// $exDay = WorkoutTime::where('day', $day_name)->where('workout_id', $request->workout_id)->first();
			if(count($woutIds) > 0){
				//workout happens on this day
				return response()->json(["status" => true, "message" => "Dashboard data obtained", 'data' => ['workouts'=> WorkoutFullResource::collection($workouts), 'meal_percentage' => $meal_percentage, 'workout_percentage'=> $wout_percentage, "day" => $day_name, "date_string" => $request->date, "date" => $date]]);
			}
			else{
				return response()->json(['status' => false, 'data' => null, 'message' => 'No workout is created for ' . $day_name . 's']);
			}
    	}
    	else{
    		return response()->json(['data'=> null, 'message' => 'Unauthorized access', 'status' => false]);
    	}
    }


}
