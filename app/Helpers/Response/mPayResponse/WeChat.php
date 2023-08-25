<?php

namespace App\Helpers\Response\mPayResponse;

use App\Helpers\Response\AbstractPaymentGatwayResponse;

class WeChat extends AbstractPaymentGatwayResponse
{

    public function getResponseMessage($respCode)
    {
        /**
         */
        switch ($respCode) {
            case '100':
                return 'Transaction successful.';
            case 'C0A1':
                return 'Transaction is in progress.';
            case 'C0A2':
                return 'Transaction failed as closed/timeout in Financial Institution side.';
            case 'C001':
                return 'Transaction fail. Transaction already refunded in WeChat side.';
            case 'C003':
                return 'Transaction closed/timeout.';
            case 'C004':
                return 'Transaction revoked.';
            case 'C006':
                return 'Payment failed (payment status failed to be returned by bank or other reasons).';
            case 'C099':
                return 'Unknown system error.';
        }
    }
}
