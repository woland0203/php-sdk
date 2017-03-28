<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayIntegerValidator;

trait CardPayRefundedTransactionIdAttribute
{
    private $refundedTransactionId;

    public function setRefundedTransactionId($refundedTransactionId)
    {
        CardPayIntegerValidator::validate($refundedTransactionId, "Refunded Transaction id", false);

        $this->refundedTransactionId = $refundedTransactionId;

        return $this;
    }

    public function getRefundedTransactionId()
    {
        return $this->refundedTransactionId;
    }
}