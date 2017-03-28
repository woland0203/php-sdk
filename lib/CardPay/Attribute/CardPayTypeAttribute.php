<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;
use CardPay\Validation\CardPayStringValidator;

trait CardPayTypeAttribute
{
    static $TYPE_PAYMENTS = "PAYMENTS";
    static $TYPE_REFUNDS = "REFUNDS";

    private $type;

    public function setType($type)
    {
        $type = (string)$type;

        CardPayStringValidator::validate($type, "Type", 1, 256);

        CardPayAllowValidator::validate($type, [
            static::$TYPE_PAYMENTS,
            static::$TYPE_REFUNDS,
        ], "Type");

        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isPayment()
    {
        return $this->type == static::$TYPE_PAYMENTS;
    }

    public function isRefund()
    {
        return $this->type == static::$TYPE_REFUNDS;
    }
}