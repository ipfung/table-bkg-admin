<?php

namespace App\Helpers\Response\mPayResponse;

use App\Helpers\Response\AbstractPaymentGatwayResponse;

class mPay extends AbstractPaymentGatwayResponse
{

    public function getResponseMessage($respCode)
    {
        /**
         */
        switch ($respCode) {
            case '100':
                return 'Transaction successful.';
            case '1A1':
                return 'Initial status of transaction (Transaction not success)';
            case '1A2':
                return 'Transaction has been sent to Financial Institution';
            case '1A3':
                return 'Transaction timeout at mPay';
            case '1A4':
                return 'Transaction timeout at Financial Institution';
            case '1A5':
                return 'Transaction cancel at mPay';
            case '1A9':
                return 'Duplicate form submission';
            case '101':
                return 'Invalid certificate';
            case '102':
                return 'Data verification fail';
            case '103':
                return 'Amount should be greater than zero';
            case '104':
                return 'Invalid amount format';
            case '105':
                return 'Invalid currency';
            case '106':
                return 'Invalid order date';
            case '107':
                return 'Invalid merchant IP address';
            case '108':
                return 'Invalid merchant ID';
            case '109':
                return 'Invalid merchant order number';
            case '110':
                return 'Invalid system reference number';
            case '111':
                return 'Invalid return URL';
            case '112':
                return 'Invalid response code';
            case '113':
                return 'Daily maximum amount of transactions exceed';
            case '114':
                return 'Daily maximum number of transactions exceed';
            case '115':
                return 'Maximum amount of transaction exceed';
            case '116':
                return 'Merchant not exist';
            case '117':
                return 'Merchant not enable';
            case '118':
                return 'Merchant terminal not exist';
            case '119':
                return 'Merchant terminal not enable';
            case '120':
                return 'Duplicate form submission';
            case '121':
                return 'Connection error';
            case '122':
                return 'No merchant id found';
            case '123':
                return 'Invalid password';
            case '124':
                return 'Duplicate merchant order number';
            case '125':
                return 'Invalid Recurring Amount';
            case '126':
                return 'Invalid Recurring Period';
            case '127':
                return 'Invalid Recurring Time';
            case '128':
                return 'Invalid Recurring Number';
            case '129':
                return 'Merchant Not Allow to perform Recurring Transaction';
            case '130':
                return 'Merchant Not Allow to perform Recurring Transaction with Trial';
            case '131':
                return 'Merchant Not Allow to perform Recurring Transaction with 2 Trials';
            case '132':
                return 'Financial Institution is now temporary unavailable';
            case '133':
                return 'Invalid payment request';
            case '134':
                return 'Duplicate response for the same transaction';
            case '135':
                return 'Transaction amount less than minimum amount limit';
            case '136':
                return 'Duplicate payment request for the same transaction';
            case '137':
                return 'Decimal places exceed for transaction amount';
            case '187':
                return 'Security error. Please check the hash value.';
            case '196':
                return 'System error';
        }
    }
}
