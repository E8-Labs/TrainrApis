<?php

namespace App\Http\Controllers\Exercise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Exercise;
use App\Models\ExerciseSet;
// use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\CompletedWorkouts;
use App\Models\Exercise\CompletedWorkoutExercise;
use App\Models\ExerciseType;
use App\Models\MuscleGroup;

use App\Models\UserTrainrs;
use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\WorkoutExercise;
use App\Models\Exercise\Workout;

use App\Http\Resources\Exercise\ExerciseLiteResource;
use App\Http\Resources\Exercise\ExerciseTypeResource;
use App\Http\Resources\Exercise\WorkoutFullResource;

use Carbon\Carbon;


class ExerciseController extends Controller
{
    //

	function AddExercise(Request $request){
		$user = Auth::user();
		if($user){
			
			$validator = Validator::make($request->all(), [
			'title' => 'required|string|max:255',
            'cover_image' => 'required',
            // 'youtube_url' => 'required|string|min:6',
            'set_count' => 'required',
            'sets' => 'required',
            'difficulty' => 'required',
            'muscle_group' => 'required',
            'exercise_type' => 'required',
				]);

			if($validator->fails()){
				return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null, 
					'validation_errors'=> $validator->errors()]);
			}

			try{
			    DB::beginTransaction();
			$ex = new Exercise;
			$ex->exercise_title = $request->title;
			$ex->set_count = $request->set_count;
			$ex->difficulty = $request->difficulty;
			$ex->muscle_group = $request->muscle_group;
			$ex->exercise_type = $request->exercise_type;

			$ex->user_id = $user->id;
			if($request->has('youtube_url')){
				$ex->youtube_url = $request->youtube_url;
			}

			$data=$request->file('cover_image')->store(\Config::get('constants.exercise_images_save'));
			$ex->cover_image = $data;
			$id = $ex->save();

			$sets = $this->AddExerciseSets($request, $ex->id);
			if($sets){
				DB::commit();
				return response()->json(['data'=> new ExerciseLiteResource($ex), 'message' => 'Exercis Saved', 'status' => true]);
			}
			else{
				DB::rollBack();
				return response()->json(['data'=> null, 'message' => 'Error Saving Exercise', 'status' => false]);
			}
			}
			catch(\Exception $e){
			    \Log::info("Exception adding exercise start");
			    \Log::info($e);
			    \Log::info($request->all());
			    \Log::info("Exception adding exercise end");
			    DB::rollBack();
				return response()->json(['data'=> null, 'message' => 'Error Saving Exercise', 'status' => false]);
			}
			//add sets

		}
		else{
			return response()->json(['data'=> null, 'message' => 'Unauthorized access', 'status' => false]);
		}
	}

	private function AddExerciseSets(Request $request, $id){
		$sets = $request->sets;
		// echo json_encode($sets);
		// return false;
		foreach($sets as $setData){
			// echo json_encode($setData);
			$set = new ExerciseSet;
			$set->rep_count = $setData['rep_count'];
			$set->weight = $setData['weight'];
			$set->exercise_id = $id;
			$saved = $set->save();
			if($saved){

			}
			else{
				return false;
			}

		}
		return true;
	}

	function GetExerciseListForUser(Request $request){
		$user = Auth::user();
		$off_set = 0;
		if($request->has('off_set')){
			$off_set = $request->off_set;
		}
		if($user){
			$profile = Profile::where('user_id', $user->id)->first();
			$list = Exercise::where('user_id', $user->id)
			->when($request->has('difficulty'), function ($query) use($request) {
                   // echo 'has difficulty '. $request->difficulty;
					$diff = $request->difficulty;
                   if($diff != 0){
                   		$query->where('difficulty', $diff);
                   }
            })
            ->when($request->has('muscle_group'), function ($query) use($request) {
                   
                   $query->where('muscle_group','=', $request->muscle_group);
            })
			->skip($off_set)->take(20)->get();

			if($profile->role == Role::RoleClient){
				$user_trainr_id = UserTrainrs::where('client_id', $profile->user_id)->pluck('trainr_id')->first();
				$list = Exercise::where('user_id', $user_trainr_id)
				->when($request->has('difficulty'), function ($query) use($request) {
            	       // echo 'has difficulty '. $request->difficulty;
					$diff = $request->difficulty;
            	       if($diff != 0){
            	       		$query->where('difficulty', $diff);
            	       }
            	})
            	->when($request->has('muscle_group'), function ($query) use($request) {
            	       
            	       $query->where('muscle_group','=', $request->muscle_group);
            	})
				->skip($off_set)->take(20)->get();
			}
			return response()->json(['data'=> ExerciseLiteResource::collection($list), 'message' => 'Exercises', 'status' => true]);
		}
		else{
			return response()->json(['data'=> null, 'message' => 'Unauthorized access', 'status' => false]);
		}
	}


    function GetExerciseTypes(Request $request){
    	$user = Auth::user();
    	if($user){
    		$list = ExerciseType::get();
    		return response()->json(['status' => true, 'message' => 'Exercise Types', 'data' => ExerciseTypeResource::collection($list)]);
    	}
    	else{
    		return response()->json(['status' => false, 'data' => null, 'message' => 'Unauthorized access']);
    	}
    }

    function GetMuscleGroups(Request $request){
    	$user = Auth::user();
    	if($user){
    		$list = MuscleGroup::get();
    		return response()->json(['status' => true, 'message' => 'Musch Group', 'data' => $list]);
    	}
    	else{
    		return response()->json(['status' => false, 'data' => null, 'message' => 'Unauthorized access']);
    	}

    }


    function CompleteExercise(Request $request){
    	$user = Auth::user();

    	if($user){
    		//check if completed workout table is populated or not
    		$cworkout = CompletedWorkouts::where('completed_date', $request->completed_date)->where('workout_id', $request->workout_id)->first();

			$dateString = $request->completed_date;
			$date = Carbon::createFromFormat('m-d-Y', $dateString);
			$day_name = $date->format('l');// get the day name

			//check if this workout is supposed to happen this day
			$exDay = WorkoutTime::where('day', $day_name)->where('workout_id', $request->workout_id)->first();
			if($exDay){
				//workout happens on this day
			}
			else{
				return response()->json(['status' => false, 'data' => null, 'message' => 'Workout does not happen on ' . $day_name . 's']);
			}

    		if($cworkout){

    		}
    		else{
    			//dont have already added the workout
    			//add the workout
    			$cworkout = new CompletedWorkouts;
    			$cworkout->workout_id = $request->workout_id;
    			$cworkout->completed_date = $request->completed_date;
    			$cworkout->user_id = $user->id;
    			$saved = $cworkout->save();
    			if(!$saved){
    				return response()->json(['status' => false, 'data' => null, 'message' => 'Error completing workout']);
    			}
    		}

    		$cwExercise = new CompletedWorkoutExercise;
    		$cwExercise->completed_workout_id = $cworkout->id;

    		$ex = Exercise::where('id', $request->exercise_id)->first();
    		$sets = ExerciseSet::where('exercise_id', $ex->id)->get();

    		$ex_ids = WorkoutExercise::where('worktime_id', $exDay->id)->pluck('exercise_id')->toArray();

    		$totalRepsOnThisDay = ExerciseSet::whereIn('exercise_id', $ex_ids)->sum('rep_count'); // reps that are required for a complete workout

    		


    		$cwExercise->exercise_id = $ex->id;

    		if($request->has('reps')){
    			$cwExercise->reps = $request->reps;
    		}
    		else{
    			$reps = ExerciseSet::where('exercise_id', $ex->id)->sum('rep_count');
    			// echo json_encode(["rep" => $reps]);
    			$cwExercise->reps = $reps;
    		}

    		if($request->has('set_id')){
    			$cwExercise->set_id = $request->set_id;
    		}
    		// else{
    		// 	$sets = ExerciseSet::where('exercise_id', $ex->id)->count();
    		// 	// echo json_encode(["sets" => $sets]);
    		// 	$cwExercise->sets = $sets;
    		// }

    		$saved = $cwExercise->save();
    		if($saved){
    			// $completedWorkoutIds = CompletedWorkouts::where('workout_id', $request->workout_id)->whre('completed_date')->pluch('id')->toArray();

    			$totalRepsUserPerformed = CompletedWorkoutExercise::where('completed_workout_id', $cworkout->id)->get()->sum(function($t){ 
    					return $t->reps ;//* $t->sets; 
				});

    			$per = 100 * $totalRepsUserPerformed / $totalRepsOnThisDay;
    			$cworkout->percentage = $per;
    			$cworkout->save();
    			// return $per;
    			
    			$workout = Workout::where('id', $request->workout_id)->first();
    			return response()->json(['status' => true, 'data' => new WorkoutFullResource($workout), 'message' => 'Workout completed']);
    		}
    		else{
    			return response()->json(['status' => false, 'data' => null, 'message' => 'Error completing workout']);
    		}


    	}
    	else{
    		return response()->json(['status' => false, 'data' => null, 'message' => 'Unauthorized access']);
    	}


    }

   
}











