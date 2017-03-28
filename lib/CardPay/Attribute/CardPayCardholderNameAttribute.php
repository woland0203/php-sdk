<?php

namespace CardPay\Attribute;

use CardPay\Helper\CardPayStringHelper;
use CardPay\Validation\CardPayStringValidator;

trait CardPayCardholderNameAttribute
{
    private $cardholderName;

    public function setCardholderName($cardholderName)
    {
        CardPayStringValidator::validate($cardholderName, "Cardholder name");

        $this->cardholderName = CardPayStringHelper::normalizeString($cardholderName);

        return $this;
    }

    public function getCardholderName()
    {
        return $this->cardholderName;
    }
}