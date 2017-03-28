<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayStringValidator;

trait CardPayCustomerIdAttribute
{
    private $customerId;

    public function setCustomerId($customerId)
    {
        $customerId = (string)$customerId;

        CardPayStringValidator::validate($customerId, "Customer id", 1, 200);

        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }
}