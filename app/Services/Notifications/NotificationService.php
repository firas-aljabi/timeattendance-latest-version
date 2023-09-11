<?php

namespace App\Services\Notifications;

use Exception;

class NotificationService
{
    public static function sendNotification($device_key, $body, $title)
    {
        try {
            $URL = 'https://fcm.googleapis.com/fcm/send';
            $data = '{
                "to" : "' . $device_key . '",
                "notification" : {
                    "body" : "' . $body . '",
                    "title" : "' . $title . '"
                    },
                }';
            $crl = curl_init();

            $header = array();
            $header[] = 'Content-type: application/json';
            $header[] = 'Authorization: key=' . env('SERVER_API_KEY');
            curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($crl, CURLOPT_URL, $URL);
            curl_setopt($crl, CURLOPT_HTTPHEADER, $header);

            curl_setopt($crl, CURLOPT_POST, true);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($crl);
        } catch (Exception $e) {
            return "NOTIFICATION FAILED !";
        }
    }
}
