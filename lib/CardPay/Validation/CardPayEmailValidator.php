<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayEmailValidator
{
    public static function validate($value, $label)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new CardPayValidationException("'{$label}' has not a fully qualified email");
        }

        return true;
    }
}