<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayBase64Validator
{
    public static function validate($value, $label)
    {
        if (empty($value) || base64_decode($value) === false) {
            throw new CardPayValidationException("'{$label}' has not a valid base64 value");
        }

        return true;
    }
}