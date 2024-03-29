<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\UserProfileFullResource;
use App\Http\Resources\UserProfileLiteResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Payment\SubscriptionPackage;

class UserController extends Controller
{
    function GetTrainrsListForClient(Request $request){

    	$validator = Validator::make($request->all(), [
			'api_key' => 'required|string',
				]);

			if($validator->fails()){
				return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null, 
					'validation_errors'=> $validator->errors()]);
			}
			$off_set = 0;
			if($request->has('off_set')){
				$off_set = $request->off_set;
			}
			if($request->api_key == \Config::get('constants.api_key')){
				$profiles = Profile::where('role', '!=', Role::RoleClient)->where('role', '!=', Role::RoleAdmin)
				->when($request->has('search'), function ($query) use($request) {
                   // echo 'has difficulty '. $request->difficulty;
					$search = $request->search;
                   
                   	$query->where('full_name', 'LIKE', "%$search%")->orWhere('username', 'LIKE', "%$search%");
                   
            	})
				->skip($off_set)->take(20)->get();
				return response()->json([
        		    'status' => true,
        		    'message' => 'User list',
        		    'data' => UserProfileLiteResource::collection($profiles)
        		]);
			}
			else{
				return response()->json([
            		'status' => false,
            		'message' => "Unauthorized access: api key needed",
            		'data' => NULL,
            
        		]);
			}

    }

    function GetClientsListForTrainr(Request $request){

    	$user = Auth::user();
    	if(!$user){
    		return response()->json([
            		'status' => false,
            		'message' => "Unauthorized access: no token",
            		'data' => NULL,
            
        		]);
    	}
			$off_set = 0;
			if($request->has('off_set')){
				$off_set = $request->off_set;
			}
			$profiles = Profile::join('user_trainrs', 'profiles.user_id', 'user_trainrs.client_id')->where('user_trainrs.trainr_id', $user->id)
			// $profiles = Profile::where('role', '=', Role::RoleClient)
			->when($request->has('search'), function ($query) use($request) {
               // echo 'has difficulty '. $request->difficulty;
				$search = $request->search;
               
               	$query->where('full_name', 'LIKE', "%$search%")->orWhere('username', 'LIKE', "%$search%");
               
            })
            
			->skip($off_set)->take(20)->get();
			return response()->json([
        	    'status' => true,
        	    'message' => 'User list',
        	    'data' => UserProfileLiteResource::collection($profiles)
        	]);
			

    }



    


}






















