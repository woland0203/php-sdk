<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayAllowValidator
{
    public static function validate($value, $haystack, $label)
    {
        if (!in_array($value, $haystack, true)) {
            throw new CardPayValidationException("'{$label}' has not a valid value");
        }
    }
}