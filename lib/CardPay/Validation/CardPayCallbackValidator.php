<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayCallbackValidator
{
    public static function validate($value, $label)
    {
        if (!is_array($value) || empty($value)) {
            throw new CardPayValidationException("'{$label}' has not a valid value");
        }

        return true;
    }
}