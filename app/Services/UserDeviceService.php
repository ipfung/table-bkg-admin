<?php

namespace App\Services;

use App\Mail\PayloadNotification;
use App\Models\NotifyMessage;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Mail;

class UserDeviceService
{
    public static function sendToCustomer($recipient, $template, $payload, $created_by, $log_to_db = true)
    {
        $defaultLanguage = config("app.jws.whatsapp.default_language");
        // email once.
        Mail::to($recipient->email)
            ->bcc(config('mail.from.address'))
            ->send(new PayloadNotification($payload));

        // app push notification.
        $userDevice = UserDevice::where('user_id', $recipient->id)
            ->where('status', 'approved');
        $counter = $userDevice->count();
//echo 'push $counter=' . $counter;
        if ($counter > 0) {
            $responseCode = FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
            if (200 == $responseCode && $log_to_db) {
                $message = new NotifyMessage;
                $message->customer_id = $recipient->id;
                $message->title = $payload['title'];
                $message->body = $payload['body'];
                $message->params = json_encode($payload['data']);
                $message->created_by = $created_by;
                $message->save();
            }
            return $responseCode;
        }
        return -1;   // no need to push, but email still has been sent.
    }

    public static function sendToAll($payload)
    {
        $userDevice = UserDevice::where('status', 'approved');
        $responseCode = FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
        return $responseCode;
    }
}
