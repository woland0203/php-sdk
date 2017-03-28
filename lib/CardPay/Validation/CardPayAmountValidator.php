<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayAmountValidator
{
    public static function validate($value, $label, $unsigned = true)
    {
        if (!is_numeric($value) || ($unsigned && $value < 0.01)) {
            throw new CardPayValidationException("'{$label}' has not a valid amount value");
        }
    }
}