<?php

namespace App\Http\Controllers\Payment;

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

class PaymentController extends Controller
{
    function createSubscriptioPackage(Request $request){
    	$validator = Validator::make($request->all(), [
			'package_name' => 'required|string',
			'package_description' => 'required|string',
			'price' => 'required',
				]);

			if($validator->fails()){
				return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null, 
					'validation_errors'=> $validator->errors()]);
			}

    	$user = Auth::user();
    	if($user){
    		$package = new SubscriptionPackage;
    		$package->package_name = $request->package_name;
    		$package->package_description = $request->package_description;
    		$package->price = $request->price;
    		$package->user_id = $user->id;
    		$saved = $package->save();
    		if($saved){
				return response()->json(['status' => true, 'message' => 'Package created', 'data' => $package]);
    		}
    		else{
    			return response()->json(['status' => false, 'message' => 'Error creating subscription package', 'data' => NULL]);
    		}
    	}
    	else{
    		return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => NULL]);
    	}
    }
}
