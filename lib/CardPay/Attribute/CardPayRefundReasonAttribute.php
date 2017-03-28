<?php

namespace CardPay\Attribute;

use CardPay\Helper\CardPayStringHelper;
use CardPay\Validation\CardPayStringValidator;

trait CardPayRefundReasonAttribute
{
    private $refundReason;

    public function setRefundReason($refundReason)
    {
        $refundReason = (string)$refundReason;

        CardPayStringValidator::validate($refundReason, "Refund Reason", 1, 2048);

        $this->refundReason = CardPayStringHelper::normalizeString($refundReason);

        return $this;
    }

    public function getRefundReason()
    {
        return $this->refundReason;
    }
}