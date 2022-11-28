<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
// use App\Http\Controllers\Auth\ForgotPasswordController;
// use App\Http\Controllers\Auth\UserController;
// use App\Http\Controllers\Admin\AdminController;
// use App\Http\Controllers\ProfileUpdateController;
// use App\Http\Controllers\NotificationsController;
// use App\Http\Controllers\SocialLoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::post("check_social_Login_exists",[SocialLoginController::class,'isSocialLoginAccountExists']);
// Route::post("register_social", [SocialLoginController::class, 'RegisterUserWithSocial']);

// Route::post("invite_friends", [UserController::class, 'inviteContacts']);

// Route::post("update_last_seen", [ProfileUpdateController::class, 'updateLastSeen']);


Route::post("register",[AuthController::class,'register']);
Route::post("login",[AuthController::class,'login']);
Route::post('send_code', [AuthController::class, 'sendVerificationMail']);
Route::post('verify_email', [AuthController::class, 'confirmVerificationCode']);