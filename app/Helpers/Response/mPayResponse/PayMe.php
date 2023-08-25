<?php

namespace App\Helpers\Response\mPayResponse;

use App\Helpers\Response\AbstractPaymentGatwayResponse;

class PayMe extends AbstractPaymentGatwayResponse
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
            case 'C001':
                return 'Request for Payment Initiated but not paid yet.';
            case 'C002':
                return 'Request for Payment Rejected.';
            case 'C003':
                return 'Payment Request Expired.';
        }
    }
}
