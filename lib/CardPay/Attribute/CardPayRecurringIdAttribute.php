<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayRecurringIdAttribute
{
    private $recurringId;

    public function setRecurringId($recurringId)
    {
        $recurringId = (string)$recurringId;

        CardPayStringValidator::validate($recurringId, "Recurring id", 1, 256);

        $this->recurringId = $recurringId;

        return $this;
    }

    public function getRecurringId()
    {
        return $this->recurringId;
    }
}