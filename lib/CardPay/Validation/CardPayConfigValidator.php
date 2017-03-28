<?php

namespace CardPay\Validation;

use CardPay\Core\CardPayConfig;
use CardPay\Helper\CardPayStringHelper;
use CardPay\Exception\CardPayConfigurationException;

class CardPayConfigValidator
{
    public static function validate(CardPayConfig $config, $param)
    {
        $param = ucwords($param);

        $value = call_user_func([$config, "get" . $param]);

        if (empty($value)) {
            throw new CardPayConfigurationException("Config param '{$param}' has not a valid value");
        }
    }
}