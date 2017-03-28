<?php

namespace CardPay\Validation;

use CardPay\Helper\CardPayStringHelper;
use CardPay\Exception\CardPayAttributeException;

class CardPayAttributeValidator
{
    public static function validate($mode, $param)
    {
        $param = ucwords($param);

        $value = call_user_func([$mode, "get" . $param]);

        if (empty($value)) {
            throw new CardPayAttributeException("Attribute '{$param}' has not a valid value");
        }
    }
}