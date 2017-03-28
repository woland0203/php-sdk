<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;
use CardPay\Validation\CardPayStringValidator;

trait CardPayStatusAttribute
{
    static $STATUS_APPROVED = "APPROVED";
    static $STATUS_DECLINED = "DECLINED";
    static $STATUS_PENDING = "PENDING";
    static $STATUS_VOIDED = "VOIDED";
    static $STATUS_REFUNDED = "REFUNDED";
    static $STATUS_CHARGEBACK = "CHARGEBACK";
    static $STATUS_CHARGEBACK_RESOLVED = "CHARGEBACK RESOLVED";

    private $status;

    public function setStatus($status)
    {
        $status = (string)$status;

        CardPayStringValidator::validate($status, "Status", 1, 256);

        CardPayAllowValidator::validate($status, [
            static::$STATUS_APPROVED,
            static::$STATUS_DECLINED,
            static::$STATUS_PENDING,
            static::$STATUS_VOIDED,
            static::$STATUS_REFUNDED,
            static::$STATUS_CHARGEBACK,
            static::$STATUS_CHARGEBACK_RESOLVED,
        ], "Status");

        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isApproved()
    {
        return $this->status == static::$STATUS_APPROVED;
    }

    public function isDeclined()
    {
        return $this->status == static::$STATUS_DECLINED;
    }

    public function isPending()
    {
        return $this->status == static::$STATUS_PENDING;
    }

    public function isVoided()
    {
        return $this->status == static::$STATUS_VOIDED;
    }

    public function isRefunded()
    {
        return $this->status == static::$STATUS_REFUNDED;
    }

    public function isChargeback()
    {
        return $this->status == static::$STATUS_CHARGEBACK;
    }

    public function isChargebackResolved()
    {
        return $this->status == static::$STATUS_CHARGEBACK_RESOLVED;
    }
}