<?php

namespace App\Helpers\Response;

abstract class AbstractPaymentGatwayResponse {
    abstract public function getResponseMessage($respCode);
}
