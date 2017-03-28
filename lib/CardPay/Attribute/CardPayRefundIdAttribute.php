<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayIntegerValidator;

trait CardPayRefundIdAttribute
{
    private $refundId;

    public function setRefundId($refundId)
    {
        CardPayIntegerValidator::validate($refundId, "Refund id");

        $this->refundId = $refundId;

        return $this;
    }

    public function getRefundId()
    {
        return $this->refundId;
    }
}