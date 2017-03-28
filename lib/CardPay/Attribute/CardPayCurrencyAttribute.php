<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayCurrencyAttribute
{
    private $currency;

    public function setCurrency($currency)
    {
        $currency = strtoupper($currency);

        CardPayStringValidator::validate($currency, "Currency", 3, 3);

        $this->currency = $currency;

        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}