<?php 
namespace App\Models\Notification;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Bank\WireFund;

class Notification extends Model
{

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public static function add(int $notification_type, int $from_user, int $to_user, $notification_for = null)
    {
        $notifiable_type = null;
        $notifiable_id   = null;

        if ($notification_for) {
            $primary_key = $notification_for->getKeyName();
            $notifiable_type  = get_class($notification_for);
            $notifiable_id    = $notification_for->$primary_key;
        }

        $notification = self::create([
            'notification_type' => $notification_type,
            'from_user'         => $from_user,
            'to_user'           => $to_user,
            'notifiable_id'     => $notifiable_id,
            'notifiable_type'   => $notifiable_type,
        ]);
        self::sendFirebasePushNotification($notification);
    }

    public static function sendFirebasePushNotification(Notification $notification)
    {
        $sendToUser = User::find($notification->to_user);
        if (isset($sendToUser->firebase_device_token) && $sendToUser->firebase_device_token)
        {
            $SERVER_API_KEY = env('FCM_SERVER_API_KEY');
            // $message = $notification->getMessageAttribute();
            $data = [
                "registration_ids" => [$sendToUser->firebase_device_token],
                "notification" => [
                    "title" => $notification->title,
                    "body" => $notification->message,
                    'data' => $notification,
                ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            return curl_exec($ch);
        }
        return null;
    }

    public static function sendFirebasePushNotificationFromData($data)
    {
        $sendToUser = User::find($data['to_user']);
        if (isset($sendToUser->firebase_device_token) && $sendToUser->firebase_device_token)
        {
            $SERVER_API_KEY = env('FCM_SERVER_API_KEY');
            // $message = $notification->getMessageAttribute();
            $data = [
                "registration_ids" => [$sendToUser->firebase_device_token],
                "notification" => [
                    "title" => $data['title'],
                    "body" => $data['body'],
                    'data' => $data,
                ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            return curl_exec($ch);
        }
        return null;
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function getTitleAttribute()
    {
        $title = "";

        switch ($this->notification_type) {
            
            case NotificationType::NewMessage:
                $title = "New Message";
                break;
        }

        return $title;
    }

    public function getMessageAttribute()
    {
        $message = "";

        switch ($this->notification_type) {
            case NotificationType::NewMessage:
                $user_name = User::find($this->from_user)->profile->name;
                $message = "From $user_name";
                break;
            
        }
        return $message;
    }

    public function getIconAttribute()
    {
        $icon = "";

        switch ($this->notification_type) {   
            case NotificationType::NewMessage:
                $icon = asset('notifications-icons/newMessageIcon@3x.png');
                break;
        }

        return $icon;
    }
}
