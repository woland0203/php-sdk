<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPaySecretValidator
{
    const SECRET_KEY_MIN_LENGTH = 12;

    public static function validate($value, $label)
    {
        if (!is_string($value) || strlen($value) < self::SECRET_KEY_MIN_LENGTH) {
            throw new CardPayValidationException("'{$label}' has not a valid value");
        }

        return true;
    }
}