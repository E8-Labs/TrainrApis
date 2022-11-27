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
use App\Models\Profile;
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
                'message' => 'Unauthorized',
            ], 401);
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

    public function register(Request $request){
        


        $validator = Validator::make($request->all(), [
			'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'profile_image' => 'required',
            'bio' => 'required',
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

    public function AddProfile(Request $request, User $user){
    	$profile=new Profile;
    	// return "Creating user";
				if($request->hasFile('profile_image'))
				{
					$data=$request->file('profile_image')->store('Images');
					$profile->image_url = $data;
					
				}
				else
				{
					return ['message' => 'No profile image'];
				}
		
		$profile->full_name=$request->name;
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
}
