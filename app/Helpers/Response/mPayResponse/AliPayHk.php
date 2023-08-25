<?php

namespace App\Helpers\Response\mPayResponse;

use App\Helpers\Response\AbstractPaymentGatwayResponse;

class AliPayHk extends AbstractPaymentGatwayResponse
{

    public function getResponseMessage($respCode)
    {
        /**
         */
        switch ($respCode) {
            case '100':
                return 'Transaction successful.';
            case 'C1A1':
                return 'Initial status of transaction (Transaction not success)';
            case 'C1A2':
                return 'Transaction has been sent to Financial Institution';
            case 'C099':
                return 'Unknown system error.';
            case 'C0A1':
                return 'Transaction is in progress.';
            case 'C0A2':
                return 'Transaction failed as closed/timeout in Financial Institution side.';
            case 'C001':
                return 'Transaction fail. Transaction not found in Alipay side.';
            case 'C003':
                return 'Transaction canceled.';
            case 'C005':
                return 'Transaction closed.';
        }
    }
}
