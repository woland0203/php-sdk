<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayIntegerValidator;

trait CardPayCardBinAttribute
{
    private $cardBin;

    public function setCardBin($cardBin)
    {
        CardPayIntegerValidator::validate($cardBin, "Card bin", false);

        $this->cardBin = $cardBin;

        return $this;
    }

    public function getCardBin()
    {
        return $this->cardBin;
    }
}