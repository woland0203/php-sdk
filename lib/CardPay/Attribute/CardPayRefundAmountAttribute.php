<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAmountValidator;

trait CardPayRefundAmountAttribute
{
    private $refundAmount;

    public function setRefundAmount($refundAmount)
    {
        CardPayAmountValidator::validate($refundAmount, "Refund Amount");

        $this->refundAmount = floatval($refundAmount);

        return $this;
    }

    public function getRefundAmount()
    {
        return $this->refundAmount;
    }
}