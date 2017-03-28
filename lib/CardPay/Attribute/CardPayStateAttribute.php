<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;
use CardPay\Validation\CardPayStringValidator;

trait CardPayStateAttribute
{
    static $STATE_NEW = "NEW";
    static $STATE_IN_PROGRESS = "IN_PROGRESS";
    static $STATE_AUTHORIZED = "AUTHORIZED";
    static $STATE_DECLINED = "DECLINED";
    static $STATE_COMPLETED = "COMPLETED";
    static $STATE_CANCELLED = "CANCELLED";
    static $STATE_REFUNDED = "REFUNDED";
    static $STATE_VOIDED = "VOIDED";
    static $STATE_CHARGED_BACK = "CHARGED_BACK";
    static $STATE_CHARGEBACK_RESOLVED = "CHARGEBACK_RESOLVED";

    private $state;

    public function setState($state)
    {
        $state = (string)$state;

        CardPayStringValidator::validate($state, "State", 1, 256);

        CardPayAllowValidator::validate($state, [
            static::$STATE_NEW,
            static::$STATE_IN_PROGRESS,
            static::$STATE_AUTHORIZED,
            static::$STATE_DECLINED,
            static::$STATE_COMPLETED,
            static::$STATE_CANCELLED,
            static::$STATE_REFUNDED,
            static::$STATE_VOIDED,
            static::$STATE_CHARGED_BACK,
            static::$STATE_CHARGEBACK_RESOLVED,
        ], "State");

        $this->state = $state;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function isInProgress()
    {
        return in_array($this->state, [static::$STATE_NEW, static::$STATE_IN_PROGRESS]);
    }

    public function isApproved()
    {
        return $this->state == static::$STATE_COMPLETED;
    }

    public function isDeclined()
    {
        return in_array($this->state, [static::$STATE_DECLINED, static::$STATE_CANCELLED]);
    }

    public function isPending()
    {
        return $this->state == static::$STATE_AUTHORIZED;
    }

    public function isVoided()
    {
        return $this->state == static::$STATE_VOIDED;
    }

    public function isRefunded()
    {
        return $this->state == static::$STATE_REFUNDED;
    }

    public function isChargeback()
    {
        return $this->state == static::$STATE_CHARGED_BACK;
    }

    public function isChargebackResolved()
    {
        return $this->state == static::$STATE_CHARGEBACK_RESOLVED;
    }
}