<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayUrlValidator
{
    public static function validate($value, $label)
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false || !in_array(parse_url($value, PHP_URL_SCHEME),
                ["http", "https"])
        ) {
            throw new CardPayValidationException("'{$label}' has not a fully qualified URL");
        }

        return true;
    }
}