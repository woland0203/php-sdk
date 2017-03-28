<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayDatetimeValidator
{
    public static function validate($value, $label)
    {
        if (strtotime($value) === false) {
            throw new CardPayValidationException("'{$label}' has not a valid datetime value");
        }
    }
}