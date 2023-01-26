<?php

namespace App\Http\Controllers\Meal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Meal\Meal;
use App\Models\Meal\MealAddedGoals;
use App\Models\Meal\MealIngredients;
use App\Models\Meal\MealDefinedGoals;





// use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\CompletedWorkouts;
use App\Models\Exercise\CompletedWorkoutExercise;
use App\Models\ExerciseType;
use App\Models\MuscleGroup;

use App\Models\UserTrainrs;
use App\Models\Exercise\WorkoutTime;
use App\Models\Exercise\WorkoutExercise;
use App\Models\Exercise\Workout;

use App\Http\Resources\Meal\MealLiteResource;
use App\Http\Resources\Meal\MealFullResource;

use Carbon\Carbon;

class MealController extends Controller
{
    //

    function addMeal(Request $request){
    	$user = Auth::user();
		if($user){
			
			$validator = Validator::make($request->all(), [
			'meal_name' => 'required|string|max:255',
            'meal_image' => 'required',
            // 'youtube_url' => 'required|string|min:6',
            'meal_description' => 'required',
            'ingredients' => 'required',
            'meal_goals' => 'required',
            'meal_carbs' => 'required',
            'meal_fats' => 'required',
            'meal_proteins' => 'required',
				]);

			if($validator->fails()){
				return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null, 
					'validation_errors'=> $validator->errors()]);
			}

			try{
			    DB::beginTransaction();
				$ex = new Meal;
				$ex->meal_title = $request->meal_name;
				$ex->meal_description = $request->meal_description;
				$ex->carbs = $request->meal_carbs;
				$ex->fats = $request->meal_fats;
				$ex->proteins = $request->meal_proteins;
	
				$ex->user_id = $user->id;
				
	
				$data=$request->file('meal_image')->store(\Config::get('constants.meal_images_save'));
				$ex->meal_image = $data;
				$saved = $ex->save();
				

				
				if($saved){
					//add meal ingredients
					$ingredients = $request->ingredients;
					foreach($ingredients as $ing){
						$mealIng = new MealIngredients;
						$mealIng->meal_id = $ex->id;
						$mealIng->meal_ingredient = $ing;
						$mealIng->save();
					}


					//add meal goals
					// check if Goal already exists and if not then create new one
					$goals = $request->meal_goals;
					foreach($goals as $goal){
						// check already exists
						$definedGoal = MealDefinedGoals::where('id', $goal)->first();

						if(!$definedGoal){//if That goal doesn't exist then trainr is adding new one
						// echo "Goal deoesn't exist " . $goal;
							$definedGoal = new MealDefinedGoals;
							$definedGoal->name = $goal;
							$definedGoal->user_id = $user->id;
							$definedGoal->save();
						}
						else{
							// echo "Goal exist " . $definedGoal->name;
						}

						$addedGoal = new MealAddedGoals;
						$addedGoal->meal_id = $ex->id;
						$addedGoal->meal_goal = $definedGoal->id;
						$addedGoal->save();
					}




					DB::commit();
					return response()->json(['data'=> new MealFullResource($ex), 'message' => 'Meal Saved', 'status' => true]);
				
				}
			}
			catch(\Exception $e){
			    \Log::info("Exception adding Meal start");
			    \Log::info($e);
			    \Log::info($request->all());
			    \Log::info("Exception adding Meal end");
			    DB::rollBack();
				return response()->json(['data'=> null, 'message' => 'Error Saving Meal', 'status' => false]);
			}
			//add sets

		}
		else{
			return response()->json(['data'=> null, 'message' => 'Unauthorized access', 'status' => false]);
		}
    }
}
