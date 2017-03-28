<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayXmlValidator
{
    public static function validate($value, $label)
    {
        libxml_use_internal_errors(true);

        if (simplexml_load_string($value) === false) {
            throw new CardPayValidationException("'{$label}' has not a valid xml value");
        }

        return true;
    }
}