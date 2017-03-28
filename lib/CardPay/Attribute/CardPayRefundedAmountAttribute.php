<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayAmountValidator;

trait CardPayRefundedAmountAttribute
{
    private $refundedAmount;

    public function setRefundedAmount($refundedAmount)
    {
        CardPayAmountValidator::validate($refundedAmount, "Refunded amount");

        $this->refundedAmount = $refundedAmount;

        return $this;
    }

    public function getRefundedAmount()
    {
        return $this->refundedAmount;
    }
}