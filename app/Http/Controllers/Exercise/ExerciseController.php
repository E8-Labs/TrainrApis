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
use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\ExerciseType;
use App\Models\MuscleGroup;

use App\Http\Resources\Exercise\ExerciseLiteResource;


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

			$data=$request->file('cover_image')->store('Images/exercise');
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
			$list = Exercise::where('user_id', $user->id)->skip($off_set)->take(20)->get();
			return response()->json(['data'=> ExerciseLiteResource::collection($list), 'message' => 'Error Saving Exercise', 'status' => true]);
		}
		else{
			return response()->json(['data'=> null, 'message' => 'Unauthorized access', 'status' => false]);
		}
	}


    function GetExerciseTypes(Request $request){
    	$user = Auth::user();
    	if($user){
    		$list = ExerciseType::get();
    		return response()->json(['status' => true, 'message' => 'Exercise Types', 'data' => $list]);
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
}
