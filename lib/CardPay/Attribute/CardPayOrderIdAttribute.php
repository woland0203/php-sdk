<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayOrderIdAttribute
{
    private $orderId;

    public function setOrderId($orderId)
    {
        $orderId = (string)$orderId;

        CardPayStringValidator::validate($orderId, "Order id", 1, 256);

        $this->orderId = $orderId;

        return $this;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }
}