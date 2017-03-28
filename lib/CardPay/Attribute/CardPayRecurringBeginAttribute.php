<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;

trait CardPayRecurringBeginAttribute
{
    private $recurringBegin = false;

    public function setRecurringBegin($recurringBegin = true)
    {
        CardPayAllowValidator::validate($recurringBegin, ["true", "false", true, false, 1, 0], "Recurring begin");

        $this->recurringBegin = in_array($recurringBegin, array("true", true, 1));

        return $this;
    }

    public function getRecurringBegin()
    {
        return $this->recurringBegin;
    }
}