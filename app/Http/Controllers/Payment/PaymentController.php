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
use App\Http\Resources\Payment\SubscriptionPackageResource;

class PaymentController extends Controller
{
    function createSubscriptioPackage(Request $request){
    	$validator = Validator::make($request->all(), [
			'package_name' => 'required|string',
			'package_description' => 'required|string',
			'price' => 'required',
            'occurrence' => 'required',
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
            $package->occurrence = $request->occurrence;
    		$saved = $package->save();
    		if($saved){
				return response()->json(['status' => true, 'message' => 'Package created', 'data' => new SubscriptionPackageResource($package)]);
    		}
    		else{
    			return response()->json(['status' => false, 'message' => 'Error creating subscription package', 'data' => NULL]);
    		}
    	}
    	else{
    		return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => NULL]);
    	}
    }

    function getSubscriptionPackagesForTrainr(Request $request){
        $user = Auth::user();
        if($user){
            $off_set = 0;
            if($request->has('off_set')){
                $off_set = $request->off_set;
            }
            $packages = SubscriptionPackage::where("user_id", $user->id)->skip($off_set)->take(5)->get();
            return response()->json(['status'=> true, 'message'=> "Packages list", 'data'=> SubscriptionPackageResource::collection($packages)]);

        }
        else{
            return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => NULL]);
        }
    }
}
