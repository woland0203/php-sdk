<?php

namespace CardPay\Security;

use CardPay\Exception\CardPayCipherException;

class CardPayCipher
{

    private $secretKey = null;

    public function __construct($secret_key)
    {
        $this->secretKey = $secret_key;
    }

    public function signature($data)
    {
        try {
            if (empty($this->secretKey)) {
                throw new CardPayCipherException("Secret key cannot be blank");
            }

            $hash = hash("sha512", $data . $this->secretKey);

            if (empty($hash)) {
                throw new CardPayCipherException("Signature creating fail");
            }
        } catch (\Exception $e) {
            throw new CardPayCipherException("Signature creating fail");
        }

        return $hash;
    }

    public function verify($data, $hash)
    {
        if ($hash !== $this->signature($data)) {
            throw new CardPayCipherException("Incorrect signature");
        }

        return true;
    }
}