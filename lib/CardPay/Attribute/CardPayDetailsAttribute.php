<?php

namespace CardPay\Attribute;

use CardPay\Helper\CardPayStringHelper;
use CardPay\Validation\CardPayStringValidator;

trait CardPayDetailsAttribute
{
    private $details;

    public function setDetails($details)
    {
        $details = (string)$details;

        CardPayStringValidator::validate($details, "Details", 1, 200);

        $this->details = CardPayStringHelper::normalizeString($details);

        return $this;
    }

    public function getDetails()
    {
        return $this->details;
    }
}