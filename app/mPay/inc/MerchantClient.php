<?php
namespace App\mPay\inc;

class MerchantClient {

    public function __construct() {
    }

    public function genSalt() {
        $salt = $this->genRandomString(16);
        return $salt;
    }

    public function genHashValue($plainText) {
        $hash = hash("sha256", $plainText);

        return $hash;
    }

    public function __toString() {
        return "";
    }

    private function genRandomString($length) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
?>
