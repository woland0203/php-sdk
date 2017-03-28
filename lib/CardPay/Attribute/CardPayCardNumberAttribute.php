<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayCardNumberAttribute
{
    private $cardNumber;

    public function setCardNumber($cardNumber)
    {
        CardPayStringValidator::validate($cardNumber, "Card number", 13, 19);

        $this->cardNumber = $cardNumber;

        return $this;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }
}