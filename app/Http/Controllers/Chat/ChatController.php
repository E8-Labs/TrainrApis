<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

use App\Models\Notification\Notification;
use App\Models\Notification\NotificationType;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat\Chat;
use App\Models\Chat\ChatUser;
use App\Models\Chat\ChatType;
use App\Models\Chat\ChatMessage;
use App\Models\Profile;

// use App\Models\UserType;
// use App\Models\NotificationType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserProfileLiteResource;
use App\Http\Resources\Chat\ChatLiteResource;
use App\Http\Resources\Chat\ChatMessageResource;

use Carbon\Carbon;
use Pusher;


class ChatController extends Controller
{


    const ChatChannel = "Chat";

    const NewMessageEvent = "NewMessage";
    const NewChatEvent = "NewChat";
    const ChatDeletedEvent = "ChatDeleted";
    const OtherUserStartedTyping = "StartedTyping";
    const OtherUserStoppedTyping = "StoppedTyping";

    private function getPusher(){
        $options = [
                  'cluster' => env('PUSHER_APP_CLUSTER'),
                  'useTLS' => false
                ];
        $pusher = new Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), $options);
        return $pusher;
    }



    function getUnreadMessagesCount()
    {
        $user = Auth::user();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => "Unauthorized access",
                'data' => 0,
            ]);
        }

        $id = $user->id;
        $unread = ChatUser::where('user_id', $id)->sum('unread_count');
        return response()->json([
            'status' => true,
            'message' => "unread count",
            'data' => (int)$unread,
        ]);
    }

    function deleteChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false,
                'message' => 'validation error',
                'data' => null,
                'validation_errors' => $validator->errors()]);
        }
        $user = Auth::user();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => "Unauthorized access",
                'data' => null,
            ]);
        }
        $deleted = Chat::where('chat_id', $request->chat_id)->delete();


        if ($deleted) {
            Notification::where('notifiable_id', $request->chat_id)
                    ->where(function($query){
                        $query->where('notification_type', NotificationType::NewMessage);
                    })->delete();
            return response()->json([
                'status' => true,
                'message' => "Chat deleted",
                'data' => null,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Chat not deleted",
                'data' => null,
            ]);
        }

    }

    function uploadChatImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required',
            'user_id' => 'required',
            'chat_image' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false,
                'message' => 'validation error',
                'data' => null,
                'validation_errors' => $validator->errors()]);
        }

        $chatid = $request->chat_id;
        $userid = $request->user_id;
        $last_message_date = $request->last_message_date;

        if ($request->hasFile('chat_image')) {
            $data = $request->file('chat_image')->store('Chat/Images');
            Chat::where('chat_id', $chatid)->update(['lastmessage' => 'Image', 'last_message_date' => $last_message_date]);
            ChatUser::where('chat_id', $chatid)->where('user_id', $userid)->update(["unread_count" => 0]);//set own count 0
            ChatUser::where('chat_id', $chatid)->where('user_id', '!=', $userid)->increment("unread_count");// increment other's count
            return response()->json([
                'status' => true,
                'message' => "Image uploaded",
                'data' => $data,
            ]);

        } else {
            return response()->json([
                'status' => false,
                'message' => "Message sent",
                'data' => $data,
            ]);
        }
    }

    function resetReadCounter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false,
                'message' => 'validation error',
                'data' => null,
                'validation_errors' => $validator->errors()]);
        }
        $user = Auth::user();
        $userid = $user->id;
        $chatid = $request->chat_id;
        ChatUser::where('chat_id', $chatid)->where('user_id', $userid)->update(["unread_count" => 0]);
        $chat = Chat::find($chatid);
        return response()->json([
            'status' => true,
            'message' => "Message sent",
            'data' => new ChatLiteResource($chat),
        ]);
    }

    function updateChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required',
            'last_message_date' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false,
                'message' => 'validation error',
                'data' => null,
                'validation_errors' => $validator->errors()]);
        }

        $user = Auth::user();
        if ($user == null) {//($email != 'admin@kodamaapp.com'){
            return response()->json([
                'status' => false,
                'message' => "You're not authorized to perform this action",
                'data' => null,
            ]);
        }
        $userid = $user->id;
        $chatid = $request->chat_id;
        $last_message_date = $request->last_message_date;
        $mess = new ChatMessage();
        $chat = Chat::find($chatid);

        if ($request->has('last_message')) {

            $lastmessage = $request->last_message;
            $mess->message = $lastmessage;
            $chat->update(['lastmessage' => $lastmessage, 'last_message_date' => $last_message_date]);

            $other_user_id = ChatUser::where('chat_id', $chatid)->where('user_id', '!=', $userid)->first()->user_id;
            Notification::add(NotificationType::NewMessage, $userid, $other_user_id, Chat::find($chatid));

            // $this->sendNotToOtherUserInChat($request)
        }
        ChatUser::where('chat_id', $chatid)->where('user_id', $userid)->update(["unread_count" => 0]);//set own count 0
        ChatUser::where('chat_id', $chatid)->where('user_id', '!=', $userid)->increment("unread_count");// increment other's count

        $mess->chat_id = $chatid;
        $mess->user_id = $userid;
        $mess->save();

        return response()->json([
            'status' => true,
            'message' => "Message sent",
            'data' => new ChatLiteResource($chat),
        ]);


    }

    //

    function createChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required',
            "last_message_date" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false,
                'message' => 'validation error',
                'data' => null,
                'validation_errors' => $validator->errors()]);
        }

        $users = $request->users;
        $user1 = $users[0];
        $user2 = $users[1];

        $user1Details = User::where('id', $user1)->first();
        $user2Details = User::where('id', $user2)->first();
        $orderId = '';

        $requestingUser = Auth::user();
        $chatType = ChatType::OneToOne;
        

        DB::beginTransaction();

        $chat = $this->checkChatExists($user1Details, $user2Details, $orderId);
        // return response()->json([
        //         "data" => $chat,
        //         'message' => 'Chats already exists',
        //         'status' => true,

        //     ]);
        if ($chat !== null) {
            $chats = DB::table('chats')
                ->join('chat_users', 'chat_users.chat_id', '=', 'chats.chat_id')
                ->where('chat_users.chat_id', $chat->chat_id)
                ->select("*")
                ->first();

            return response()->json([
                "data" => new ChatLiteResource($chats),
                'message' => 'Chats already exists',
                'status' => true,

            ]);
        }

        $chat = new Chat;
        if ($request->has('order_id')) {
            $chat->order_id = $orderId;
        }
        $chat->last_message_date = $request->last_message_date;
        $chat->lastmessage = '';
        $chat->chat_type = $chatType;
        $result = $chat->save();
        $chat_id = $chat->chat_id;

        foreach ($users as $user) {
            $chatuser = new ChatUser;
            $chatuser->chat_id = $chat_id;
            $chatuser->user_id = $user;
            $chatuser->unread_count = 0;
            $saved = $chatuser->save();
            if (!$saved) {
                DB::rollBack();
                return response()->json([
                    'message' => 'chat not created',
                    'status' => false,
                    'data' => null,

                ]);
            }
        }
        DB::commit();
        return response()->json([
            'message' => 'chat created',
            'status' => true,
            'data' => new ChatLiteResource($chat)
        ]);
    }


    function loadChats(Request $request)
    {
        $user = Auth::user();
        if ($user == null) {
            return response() . json([
                    "data" => null,
                    'message' => 'Unauthorized access',
                    'status' => false,

                ]);
        }
        $Rows_To_Fetch = 10;
        $off_set = 0;
        if ($request->has('off_set')) {
            $off_set = $request->off_set;
        }

        $userid = $user->id;
        $chats = DB::table('chats')
            ->join('chat_users', 'chat_users.chat_id', '=', 'chats.chat_id')
            ->where('chat_users.user_id', $userid)
            ->select("*")
            ->skip($off_set)
            ->take($Rows_To_Fetch)
            ->orderBy('last_message_date', 'desc')
            ->get();

        return response()->json([
            "data" => ChatLiteResource::collection($chats),
            'message' => 'Chats list',
            'status' => true,

        ]);
    }

    function checkChatExists($user1, $user2, $orderId)
    {
        if ($orderId != '') {
            // return "Chat with order";
            // chat for on order. We can use orderid to fetch the chat
            $chats = Chat::with('chatUser')->where('order_id', $orderId)->get();
            if ($chats->count() == 0){
                return null;
            }

            foreach ($chats as $chat)
            {
                $chatUser1 = $chat->chatUser[0];
                $chatUser2 = $chat->chatUser[1];
                $already_created_chat_users = [$chatUser1->user_id, $chatUser2->user_id];
                sort($already_created_chat_users);
                $requested_chat_users = [$user1->id, $user2->id];
                sort($requested_chat_users);
                if ($already_created_chat_users == $requested_chat_users) {
                    return $chat;
                }
            }
            return null;
        }
        if ($user1->isAdmin() || $user2->isAdmin()) {
            // chat between admin and another user without order
            $chatusers = ChatUser::where('user_id', $user1->id)->get();
            $chatids = array();
            foreach ($chatusers as $c) {
                $chatids[] = $c->chat_id;
            }
            $otherUserChats = ChatUser::whereIn('chat_id', $chatids)->where('user_id', $user2->id)->first();
            if ($otherUserChats == null) {
                return null;
            } else {
                $chat = Chat::where('chat_id', $otherUserChats->chat_id)->first();
                return $chat;
            }
        }
    }


    function getMessagesForChat(Request $request){
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false,
                'message' => 'validation error',
                'data' => null,
                'validation_errors' => $validator->errors()]);
        }

        $user = Auth::user();
        $chat_id = $request->chat_id;

        $last_message_id = 0;
        if($request->has('last_message_id')){
            if($request->has('load_new')){
                $last_message_id = $request->last_message_id;
                $lastMessages = ChatMessage::where('chat_id', $chat_id)
                ->where('id', '>', $last_message_id)
                ->orderBy('created_at', 'DESC')
                ->take(500)->get();
            }
            else{
                $last_message_id = $request->last_message_id;
                $lastMessages = ChatMessage::where('chat_id', $chat_id)
                ->where('id', '<', $last_message_id)
                ->orderBy('created_at', 'DESC')
                ->take(50)->get();
            }
        }
        else{
            $lastMessages = ChatMessage::where('chat_id', $chat_id)
            ->orderBy('created_at', 'DESC')
            ->take(50)->get();
        }

        $messages = array();
        $size = sizeof($lastMessages);
        for($i=$size-1; $i>=0; $i--){
            $m = $lastMessages[$i];
            $messages[] = $m;
        }

        

        return response()->json([
            "data" => ChatMessageResource::collection($messages),
            'message' => 'Message list',
            'status' => true,

        ]);

    }

    public function sendMessage(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'chat_id' => 'required',
        //     'message' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(['status' => false,
        //         'message' => 'validation error',
        //         'data' => null,
        //         'validation_errors' => $validator->errors()]);
        // }
        $user = Auth::user();
        $chatid = $request->chat_id;
        $userid = $user->id;
        $updated_at = Carbon::today();

        $message = new ChatMessage;
            $message->chat_id = $chatid;
            $message->user_id = $userid;
            
        if ($request->hasFile('chat_image')) {
            $data = $request->file('chat_image')->store('Chat/Images');
            $message->message = '';
            $message->image_url = $data;
        }

        else if ($request->has('message')) {
            $data = $request->message;
            $message->message = $data;
            if($data == ""){
                ChatUser::where('chat_id', $chatid)->where('user_id', $userid)->update(["unread_count" => 0]);//set own count 0
            return response()->json([
                'status' => true,
                'message' => "Chat count reset",
                'data' => null,
            ]);
            }

        } else {
            ChatUser::where('chat_id', $chatid)->where('user_id', $userid)->update(["unread_count" => 0]);//set own count 0
            return response()->json([
                'status' => false,
                'message' => "Message not sent",
                'data' => null,
            ]);
        }
        $saved = $message->save();
            if($saved){
                $newMessage = ChatMessage::where('id', $message->id)->first();
                $otherUser = ChatUser::where('chat_id', $chatid)->where('user_id', '!=', $userid)->first();
                // $admin = User::where('id', $post->user_id)->first();
                Notification::add(NotificationType::NewMessage, $user->id, $otherUser->user_id, $newMessage);
                Chat::where('chat_id', $chatid)->update(['lastmessage' => $data]);
                ChatUser::where('chat_id', $chatid)->where('user_id', $userid)->update(["unread_count" => 0]);//set own count 0
                ChatUser::where('chat_id', $chatid)->where('user_id', '!=', $userid)->increment("unread_count");// increment other's count
                
                $pusher = $this->getPusher();
                $pusher->trigger(ChatController::ChatChannel, ChatController::NewMessageEvent . $chatid, new ChatMessageResource($newMessage));

                $otherUserId = ChatUser::where('chat_id', $chatid)->where('user_id', '!=', $userid)->get("user_id");// increment
                $pusher->trigger(ChatController::ChatChannel, ChatController::NewMessageEvent . $otherUserId, new ChatMessageResource($newMessage));
                return response()->json([
                    'status' => true,
                    'message' => "Message sent",
                    'data' => new ChatMessageResource($newMessage),
                 ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => "Message not sent",
                    'data' => null,
                 ]);
            }
    }


    private function sendNotToOtherUserInChat(Request $request)
    {

        $user = Auth::user();
        $userid = $user->id;
        $chatid = $request->chat_id;

        $otherUser = ChatUser::where('chat_id', $chatid)->where('user_id', '!=', $userid)->first();


        $admin = User::where('role', UserType::Admin)->first();
        $not = NotificationController::createNotification($userid, $otherUser->user_id, NotificationType::NewMessage);
    }

    public function showChat()
    {
        $validator = Validator::make(request()->all(), [
            'chat_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false,
                'message' => 'validation error',
                'data' => null,
                'validation_errors' => $validator->errors()]);
        }
        return response()->json([
            "data" => ChatLiteResource::make(Chat::where("chat_id", request()->chat_id)->first()),
            'message' => 'Chats list',
            'status' => true,

        ]);
    }
}
