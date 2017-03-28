<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayRequestValidator
{
    public static function validate($value, $label)
    {
        if (empty($value)) {
            throw new CardPayValidationException("'{$label}' has not a valid value");
        }

        return true;
    }
}