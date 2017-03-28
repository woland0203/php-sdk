<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayCustomerIpAttribute
{
    private $customerIp;

    public function setCustomerIp($customerIp)
    {
        CardPayStringValidator::validate($customerIp, "Customer ip", 1, 200);

        $this->customerIp = $customerIp;

        return $this;
    }

    public function getCustomerIp()
    {
        return $this->customerIp;
    }
}