<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * ref: https://dev.to/rabeeaali/send-push-notifications-from-laravel-to-ios-android-29b4
 */
class FcmService
{
    public static function send($token, $notification)
    {
//        echo 'send notification=' . json_encode($notification);
        $response = Http::acceptJson()->withToken(config('app.jws.fcm_server_key'))->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'to' => $token,
                'notification' => [
                    'title' => $notification['title'],
                    'body' => $notification['body']
                ],
                'data' => $notification['data']
            ]
        );
        return $response->status();
    }

    public static function sendMultiple($tokens, $notification)
    {
        $response = Http::acceptJson()->withToken(config('app.jws.fcm_server_key'))->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'registration_ids' => $tokens,
                'notification' => [
                    'title' => $notification['title'],
                    'body' => $notification['body']
                ],
                'data' => $notification['data']
            ]
        );
        return $response->status();
    }
}
