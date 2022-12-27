<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exercise\ExerciseController;
// use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Trainr\TrainrWorkoutProgramController;
use App\Http\Controllers\Workout\HomeWorkoutController;
use App\Http\Controllers\Chat\ChatController;
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
Route::get("get_coaches",[UserController::class,'GetTrainrsListForClient']);
Route::post('send_code', [AuthController::class, 'sendVerificationMail']);
Route::post('verify_email', [AuthController::class, 'confirmVerificationCode']);



Route::get('get_user_profile', [AuthController::class, 'GetUserProfile']);
Route::post('update_user_profile', [AuthController::class, 'updateProfile']);



Route::get('trainr_dashboard', [HomeWorkoutController::class, 'getTrainrDashboardData']);
Route::get('client_dashboard', [HomeWorkoutController::class, 'getClientDashboardData']);
Route::post('complete_exercise', [ExerciseController::class, 'CompleteExercise']);

Route::post('add_exercise', [ExerciseController::class, 'AddExercise']);
Route::get('get_user_exercises', [ExerciseController::class, 'GetExerciseListForUser']);
Route::get('get_exercise_types', [ExerciseController::class, 'GetExerciseTypes']);
Route::get('get_muscle_groups', [ExerciseController::class, 'GetMuscleGroups']);
Route::get("get_clients",[UserController::class,'GetClientsListForTrainr']);
Route::post('create_workout_program', [TrainrWorkoutProgramController::class, 'AddWorkout']);





//Chat
Route::post("create_chat",[ChatController::class,'createChat']);

Route::get('load_chats', [ChatController::class, 'loadChats']);
Route::post('send_message', [ChatController::class, 'sendMessage']);//New
    Route::post("update_chat", [ChatController::class, 'updateChat']);
    Route::post('resetReadCount', [ChatController::class, 'resetReadCounter']);
    Route::post('delete_chat', [ChatController::class, 'deleteChat']);
    Route::post('upload_chat_image', [ChatController::class, 'uploadChatImage']);
    Route::post('user_unread_count', [ChatController::class, 'getUnreadMessagesCount']);
    Route::post('get_chat_by_id', [ChatController::class, 'showChat']);
