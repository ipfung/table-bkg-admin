<?php

namespace App\Services;

use App\Models\NotifyMessage;
use App\Models\UserDevice;

class UserDeviceService
{
    public static function sendToCustomer($custId, $payload, $created_by, $log_to_db = true)
    {
        $userDevice = UserDevice::where('user_id', $custId)
            ->where('status', 'approved');
        $counter = $userDevice->count();
//echo 'push $counter=' . $counter;
        if ($counter > 0) {
            if (1 == $counter) {
                $device = $userDevice->get()[0]->reg_id;
                $responseCode = FcmService::send($device, $payload);
            } else {
                $responseCode = FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
            }
            if (200 == $responseCode && $log_to_db) {
                $message = new NotifyMessage;
                $message->user_id = $custId;
                $message->title = $payload['title'];
                $message->body = $payload['body'];
                $message->params = json_encode($payload['data']);
                $message->created_by = $created_by;
                $message->save();
            }
            return $responseCode;
        }
        return -1;
    }

    public static function sendToAll($payload)
    {
        $userDevice = UserDevice::where('status', 'approved');
        $responseCode = FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
        return $responseCode;
    }
}
