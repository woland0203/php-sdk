<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayAmountValidator;

trait CardPayRemainingAmountAttribute
{
    private $remainingAmount;

    public function setRemainingAmount($remainingAmount)
    {
        CardPayAmountValidator::validate($remainingAmount, "Remaining amount", false);

        $this->remainingAmount = $remainingAmount;

        return $this;
    }

    public function getRemainingAmount()
    {
        return $this->remainingAmount;
    }
}