<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayIntegerValidator;

trait CardPayMaxCountAttribute
{
    private $maxCount;

    public function setMaxCount($maxCount)
    {
        CardPayIntegerValidator::validate($maxCount, "Max count");
        CardPayIntegerValidator::validateRange($maxCount, "Max count", 1, 10000);

        $this->maxCount = $maxCount;

        return $this;
    }

    public function getMaxCount()
    {
        return $this->maxCount;
    }
}