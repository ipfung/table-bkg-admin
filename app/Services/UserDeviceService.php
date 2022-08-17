<?php

namespace App\Services;

use App\Models\UserDevice;

class UserDeviceService
{
    public static function sendToCustomer($custId, $payload)
    {
        $userDevice = UserDevice::where('user_id', $custId)
            ->where('status', 'approved');
        $counter = $userDevice->count();
//echo 'push $counter=' . $counter;
        if ($counter > 0) {
            if (1 == $counter) {
                $device = $userDevice->get()[0]->reg_id;
                FcmService::send($device, $payload);
            } else {
                FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
            }
        }
    }

    public static function sendToAll($payload)
    {
        $userDevice = UserDevice::where('status', 'approved');
        FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
    }
}
