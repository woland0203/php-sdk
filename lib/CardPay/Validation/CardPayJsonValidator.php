<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayJsonValidator
{
    public static function validate($value, $label)
    {
        if (empty($value)) {
            throw new CardPayValidationException("'{$label}' has not a valid value");
        }

        json_decode($value);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new CardPayValidationException("'{$label}' has not a valid json value");
        }

        return true;
    }
}