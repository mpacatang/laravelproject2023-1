<?php

namespace App\Helpers\Notification;


use App\Models\BusinessSetting;

class FCMOption
{

    public static string $order_type = 'order';
    public static string $promotional_type = 'promotional';
    public static string $chat_message_notification_type = 'chat_message_notification';
    public static string $chat_message_status_notification_type = 'chat_message_status_notification';
    public static string $other_type = 'other';

    public string $title = "";
    public bool $withNotification = true;
    public ?string $body = null;
    public ?string $token = null;
    public ?array $tokens = null;
    public ?array $data = null;


    public function getRegistrationIds(): array|string|null
    {
        return $this->token != null ? [$this->token] : $this->tokens;
    }

    public function isValid(): bool
    {
        return $this->token != null || ($this->tokens != null && sizeof($this->tokens) != 0);
    }

    public function sendNotification(): bool|string
    {
        if (!$this->isValid()) {
            return false;
        }

        $server_key = BusinessSetting::_get(BusinessSetting::$fcm_server_key);
        if (!$server_key) {
            return false;
        }
        $data = array_merge([],
            $this->data ?? [
            'type' => self::$other_type
        ]);

        $json_data = [
            "registration_ids" => $this->getRegistrationIds(),
            "priority" => "high",

            "data" => $data,
            'android' => [
                "priority" => "high",
            ]
        ];
        if ($this->withNotification) {
            $json_data["notification"] = [
                "title" => $this->title,
                "body" => $this->body,
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ];
        }
        $body = json_encode($json_data);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $result = curl_exec($ch);
        error_log($result);
        curl_close($ch);
        return !($result === false);
    }


}
