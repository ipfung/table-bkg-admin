<?php

namespace App\Helpers\Response\mPayResponse;

use App\Helpers\Response\AbstractPaymentGatwayResponse;

class AliPayCn extends AbstractPaymentGatwayResponse
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
            case '899':
                return 'Unknown system error.';
            case '8A2':
                return 'Transaction failed as closed/timeout in Financial Institution side.';
        }
    }
}
