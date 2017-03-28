<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayIntegerValidator
{
    public static function validate($value, $label, $unsigned = true)
    {
        if ((!is_integer($value) && !ctype_digit($value)) || ($unsigned && intval($value) <= 0)) {
            throw new CardPayValidationException("'{$label}' has not a valid integer value");
        }

        return true;
    }

    public static function validateRange($value, $label, $min_value = 1, $max_value = 10000)
    {
        if (intval($value) < $min_value) {
            throw new CardPayValidationException("'{$label}' has not a valid value. Minimum {$min_value}");
        }

        if (intval($value) > $max_value) {
            throw new CardPayValidationException("'{$label}' has not a valid value. Maximum {$max_value}");
        }
    }
}