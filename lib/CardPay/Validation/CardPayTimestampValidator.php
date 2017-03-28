<?php

namespace CardPay\Validation;

use CardPay\Exception\CardPayValidationException;

class CardPayTimestampValidator
{
    public static function validate($value, $label)
    {
        try {
            CardPayStringValidator::validate($value, $label, 10, 10);
            CardPayDigitValidator::validate($value, $label);

        } catch (CardPayValidationException $e) {
            throw new CardPayValidationException("'{$label}' has not a valid timestamp value");
        }
    }

    public static function validateMilliseconds($value, $label)
    {
        try {
            CardPayStringValidator::validate($value, $label, 13, 13);
            CardPayDigitValidator::validate($value, $label);

        } catch (CardPayValidationException $e) {
            throw new CardPayValidationException("'{$label}' has not a valid timestamp with milliseconds value");
        }
    }
}