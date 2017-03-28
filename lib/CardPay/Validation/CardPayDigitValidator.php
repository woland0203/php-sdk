<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayDigitValidator
{
    public static function validate($value, $label)
    {
        $value = (string)$value;

        if (!ctype_digit($value)) {
            throw new CardPayValidationException("'{$label}' has not a valid digit value");
        }
    }
}