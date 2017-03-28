<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayStringValidator
{
    public static function validate($value, $label, $min_length = 1, $max_length = 256)
    {
        if (!is_string($value)) {
            throw new CardPayValidationException("'{$label}' has not a valid string value");
        }

        self::validateRange($value, $label, $min_length, $max_length);
    }

    public static function validateRange($value, $label, $min_length = 1, $max_length = 256)
    {
        if (strlen($value) < $min_length) {
            throw new CardPayValidationException("'{$label}' has not a valid length. Minimum {$min_length}");
        }

        if (strlen($value) > $max_length) {
            throw new CardPayValidationException("'{$label}' has not a valid length. Maximum {$max_length}");
        }
    }
}