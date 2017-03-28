<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;

trait CardPayIsExecutedAttribute
{
    private $isExecuted = false;

    public function setIsExecuted($isExecuted = true)
    {
        CardPayAllowValidator::validate($isExecuted, ["yes", "no"], "Is Executed");

        $this->isExecuted = ($isExecuted == "yes");

        return $this;
    }

    public function getIsExecuted()
    {
        return $this->isExecuted;
    }

    public function isExecuted()
    {
        return $this->isExecuted;
    }
}