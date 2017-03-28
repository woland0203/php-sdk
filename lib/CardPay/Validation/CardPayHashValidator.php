<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayHashValidator
{
    public static function validate($value, $label)
    {
        if (empty($value)) {
            throw new CardPayValidationException("'{$label}' has not a valid sha512 value");
        }

        return true;
    }
}