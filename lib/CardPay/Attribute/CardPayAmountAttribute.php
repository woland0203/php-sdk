<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayAmountValidator;

trait CardPayAmountAttribute
{
    private $amount;

    public function setAmount($amount)
    {
        CardPayAmountValidator::validate($amount, "Amount");

        $this->amount = floatval($amount);

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}