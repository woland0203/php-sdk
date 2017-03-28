<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;
use CardPay\Validation\CardPayStringValidator;

trait CardPayStatusToAttribute
{
    static $STATUS_CAPTURE = "capture";
    static $STATUS_VOID = "void";
    static $STATUS_REFUND = "refund";

    private $statusTo;

    public function setStatusTo($statusTo)
    {
        $statusTo = (string)$statusTo;

        CardPayStringValidator::validate($statusTo, "Status To", 1, 256);

        CardPayAllowValidator::validate($statusTo, [
            static::$STATUS_CAPTURE,
            static::$STATUS_VOID,
            static::$STATUS_REFUND,
        ], "Status");

        $this->statusTo = $statusTo;

        return $this;
    }

    public function setCaptureStatus()
    {
        $this->setStatusTo(static::$STATUS_CAPTURE);

        return $this;
    }

    public function isCapture()
    {
        return $this->statusTo == static::$STATUS_CAPTURE;
    }

    public function setVoidStatus()
    {
         $this->setStatusTo(static::$STATUS_VOID);

        return $this;
    }

    public function isVoid()
    {
        return $this->statusTo == static::$STATUS_VOID;
    }

    public function setRefundStatus()
    {
        $this->setStatusTo(static::$STATUS_REFUND);

        return $this;
    }

    public function isRefund()
    {
        return $this->statusTo == static::$STATUS_REFUND;
    }

    public function getStatusTo()
    {
        return $this->statusTo;
    }
}