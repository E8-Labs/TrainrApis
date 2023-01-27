<?php

namespace App\Http\Resources\Meal;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Profile;
use App\Http\Resources\UserProfileLiteResource;
use App\Models\Meal\MealAddedGoals;
use App\Models\Meal\MealIngredients;
use App\Models\Meal\MealDefinedGoals;

class MealFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = Profile::where('user_id', $this->user_id)->first();
        $ingredients = MealIngredients::where('meal_id', $this->id)->get();
        $addedGoals = MealAddedGoals::where('meal_id', $this->id)->pluck('meal_goal')->toArray();
        $goals = MealDefinedGoals::whereIn('id', $addedGoals)->get();
        
        return [
            "id" => $this->id,
            "meal_name" => $this->meal_title,
            "meal_descrition" => $this->meal_description,
            'user' => new UserProfileLiteResource($user),
            'ingredients' => $ingredients,
            "meal_goals" => $goals,
            "meal_image" => \Config::get('constants.base_url').$this->meal_image,
            "fats" => (int)$this->fats,
            "proteins" => (int)$this->proteins,
            'carbs' => (int)$this->carbs,
        ];
    }
}
