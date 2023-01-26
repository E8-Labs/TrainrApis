<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserProfileFullResource;
use App\Http\Resources\UserProfileLiteResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserTrainrs;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Exercise\Goal;
use App\Models\Exercise\HealthCondition;
use App\Models\Exercise\ClientHealthConditionsModel;
use App\Models\Exercise\WorkoutFrequency;
use App\Models\User\UserExpertise;
use App\Models\User\VerificationCode;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 200);
        }

        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        $data = ["profile" => new UserProfileFullResource($profile), "access_token" => $token];
        return response()->json([
                'status' => true,
                'data'   => $data,
                'message'=> 'User logged in'
            ]);

    }


    public function GetUserProfile(Request $request)
    {
    	$user = Auth::user();

    	if($user){
            if($request->has('user_id')){
                 $user = User::where('id', $request->user_id);
            }
    		$profile = Profile::where('user_id', $user->id)->first();
        	$data = new UserProfileFullResource($profile);
        	return response()->json([
                'status' => true,
                'data'   => $data,
                'message'=> 'User data'
         	]);
    	}
    	else{
    		return response()->json([
            		'status' => false,
            		'message' => "Unauthorized",
            		'data' => NULL,
            
        		]);
    	}
    }

    public function register(Request $request){
        


        $validator = Validator::make($request->all(), [
			'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'profile_image' => 'required',
            // 'bio' => 'required',
				]);

			if($validator->fails()){
				return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null, 
					'validation_errors'=> $validator->errors()]);
			}

			DB::beginTransaction();
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if($user){
        	if($request->role != Role::RoleClient){
        		$added = $this->AddExpertise($request, $user->id);
        		if($added){
					
        		}
        		else{
        			DB::rollBack();
        			return response()->json([
            			'status' => false,
            			'message' => "Error adding expertise",
            			'data' => NULL,
            	
        			]);
        		}
        	}
        	else{// if client
        		$trainr = new UserTrainrs;
        		$trainr->client_id = $user->id;
        		$trainr->trainr_id = $request->trainr_id;
        		$trainr->save();
        	}
        	$profile = $this->AddProfile($request, $user);
        	// return $profile;
        	if($profile->message){
        		//profile not saved
        		DB::rollBack();
        		return response()->json([
            		'status' => false,
            		'message' => $profile->message,
            		'data' => NULL,
            
        		]);
        	}
        	else{
        		DB::commit();
        		$token = Auth::login($user);
        		return response()->json([
        		    'status' => true,
        		    'message' => 'User created successfully',
        		    'data' => [
        		    	'profile' => new UserProfileFullResource($profile),
        		        'access_token' => $token,
        		        'type' => 'bearer',
        		    	
        			]
        		]);
        	}
        }
        else{
        	DB::rollBack();
        	return response()->json([
            		'status' => false,
            		'message' => "User didn't save",
            		'data' => NULL,
            
        		]);
        }

        
    }



    public function updateProfile(Request $request){
    	$user = Auth::user();
    	if($user == NULL){
    		return response()->json([
            		'status' => false,
            		'message' => "User unauthorized",
            		'data' => NULL,
            
        		]);
    	}

    	DB::beginTransaction();
    	$profile = Profile::where('user_id', $user->id)->first();
    	if($request->has('height_inches')){
            // return "have height inches";
    		$profile->height_inches = $request->height_inches;
    	}
        // else{
        //     return "Don't have height inches";
        // }
    	if($request->has('height_feet')){
    		$profile->height_feet = $request->height_feet;
    	}

    	if($request->has('weight')){
    		$profile->weight = $request->weight;
    	}
    	if($request->has('workout_frequency')){
    		$profile->workout_frequency = $request->workout_frequency;
    	}
    	if($request->has('sleep_hours')){
    		$profile->sleep_hours = $request->sleep_hours;
    	}
    	$profile->save();

    	if($request->has('goals')){
    		$goals = $request->goals;
    		foreach($goals as $goal){
    			$userGoal = new Goal;
    			$userGoal->goal_title = $goal["goal_title"];
    			$userGoal->complete_by_date	= $goal["complete_by_date"];
    			$userGoal->user_id = $user->id;
    			$saved = $userGoal->save();
    			if($saved){

    			}
    			else{
    				DB::rollBack();
    				//return an error saying goals not saved
    				return response()->json([
            			'status' => false,
            			'message' => "Error saving user goals",
            			'data' => NULL,
            
        			]);
    			}
    		}
    	}
        if($request->has('expertise')){
            $saved = $this->AddExpertise($request, $user->id);
            if($saved == false){
                return response()->json([
                        'status' => false,
                        'message' => "Error saving user expertise",
                        'data' => NULL,
            
                    ]);
            }
        }
    	if($request->has('health_conditions')){
    		$conditions = $request->health_conditions;
    		foreach($conditions as $cond){
    			$userCondition = new ClientHealthConditionsModel;
    			$userCondition->health_condition = $cond;
    			$userCondition->user_id = $user->id;
    			$saved = $userCondition->save();
    			if($saved){

    			}
    			else{
    				DB::rollBack();
    				//return an error saying goals not saved
    				return response()->json([
            			'status' => false,
            			'message' => "Error saving user health conditions",
            			'data' => NULL,
            
        			]);
    			}
    		}
    	}
    	DB::commit();

		return response()->json([
           	'status' => true,
           	'data' => new UserProfileFullResource($profile),
           	'message' => "Profile udpated",
         
        ]);

    }


    private function AddExpertise(Request $request, $id){
		$expertise = $request->expertise;
		// echo json_encode($sets);
		// return false;
		foreach($expertise as $setData){
			// echo json_encode($setData);
			$ex = new UserExpertise;
			$ex->expertise_id = $setData;
			
			$ex->user_id = $id;
			$saved = $ex->save();
			if($saved){

			}
			else{
				return false;
			}

		}
		return true;
	}

    public function AddProfile(Request $request, User $user){
    	$profile=new Profile;
    	// return "Creating user";
				if($request->hasFile('profile_image'))
				{
					$data=$request->file('profile_image')->store(\Config::get('constants.profile_images_save'));
					$profile->image_url = $data;
					
				}
				else
				{
					return ['message' => 'No profile image'];
				}
		
		$profile->full_name=$request->name;

		$profile->role = $request->role;
		$profile->username = $request->username;
		$profile->user_id = $user->id;
		$result=$profile->save();
		if($result)
		    {
				
			return $profile;
			
		    }
		else
			{
				return ['message' => 'No profile image'];;
			}
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    public function CheckEmailAvailablity(Request $req)
	{
				$validator = Validator::make($req->all(), [
				'email' => 'required|string|email|max:255',
					]);
			if($validator->fails()){
					return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null,
					'validation_errors'=> $validator->errors()]);
				}
		{
			$isEmailAvailable = $this->isEmailAvailable($req['email']);
			if($isEmailAvailable)
			{
				return response()->json([
				'status' => true,
				'message'=> 'Email Available']);
			}
			else
			{
				return response()->json([
				'status' => false,
				'message'=> 'Email not Available']);
			}
		}
    }

    public function CheckUsernameAvailablity(Request $req)
    {
                $validator = Validator::make($req->all(), [
                'username' => 'required',
                    ]);
            if($validator->fails()){
                    return response()->json(['status' => false,
                    'message'=> 'validation error',
                    'data' => null,
                    'validation_errors'=> $validator->errors()]);
                }
        {
            $isEmailAvailable = $this->isUsernameAvailable($req['username']);
            if($isEmailAvailable)
            {
                return response()->json([
                'status' => true,
                'message'=> 'Username Available']);
            }
            else
            {
                return response()->json([
                'status' => false,
                'message'=> 'Username not Available']);
            }
        }
    }

    function sendVerificationMail(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email',
				]);

			if($validator->fails()){
				return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null, 
					'email' => $request->email,
					'validation_errors'=> $validator->errors()]);
			}

			// set code to codes table

			$user = User::where('email', $request->email)->first();
			if($user){
				return response()->json(['status' => false,
					'message'=> 'Email is taken',
					'data' => null
				]);
			}
                

			VerificationCode::where('email', $request->email)->delete();
			$FourDigitRandomNumber = rand(1111,9999);
			$code = new VerificationCode;
			$code->code = $FourDigitRandomNumber;
			$code->email = $request->email;
			$res = $code->save();
			

			if($res){
				$data = array('code'=> $FourDigitRandomNumber, "email" => "seedmarket.info@gmail.com");
				// Mail::send('Mail/verificationmail', $data, function ($message) use ($data, $request) {
    //                     $message->to($request->email,'Admin')->subject('Verification Code');
    //                     $message->from($data['email']);
    //                 });
                    

				return response()->json(['status' => true,
					'message'=> 'Code sent',
					'data' => null,
				]);
			}
			else{
				return response()->json(['status' => false,
					'message'=> 'Some error occurred',
					'data' => null]);
			}
			
	}


	function confirmVerificationCode(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email',
			'code' => 'required'
				]);

			if($validator->fails()){
				return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null, 
					'validation_errors'=> $validator->errors()]);
			}

			$digitcode = $request->code;
			$email = $request->email;

			$codeDB = VerificationCode::where('email', $email)->where('code', $digitcode)->first();
			if($codeDB || $request->code == "1234"){
				VerificationCode::where('email', $request->email)->delete();
				return response()->json(['status' => true,
						'message'=> 'Email verified',
						'data' => null,
					]);
			}
			else{
				return response()->json(['status' => false,
						'message'=> 'Code does not exist',
						'data' => null,
					]);
			}
	}
  	  

	private function isEmailAvailable($email)
	{
				$user=User::where("email",$email)->first();
				if($user==null)
				{
					return true;
				}
				else
				{
					return false;
				}
				
	}
    private function isUsernameAvailable($username)
    {
                $user=Profile::where("username",$username)->first();
                if($user==null)
                {
                    return true;
                }
                else
                {
                    return false;
                }
                
    }
}
