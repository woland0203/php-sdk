<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayCardTokenAttribute
{
    private $cardToken;

    public function setCardToken($cardToken)
    {
        CardPayStringValidator::validate($cardToken, "Card token", 1, 36);

        $this->cardToken = $cardToken;

        return $this;
    }

    public function getCardToken()
    {
        return $this->cardToken;
    }
}