<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;

trait CardPayGenerateCardTokenAttribute
{
    private $generateCardToken = false;

    public function setGenerateCardToken($generateCardToken = true)
    {
        CardPayAllowValidator::validate($generateCardToken, ["true", "false", true, false, 1, 0], "Generate card token");

        $this->generateCardToken = in_array($generateCardToken, array("true", true, 1), true);

        return $this;
    }

    public function getGenerateCardToken()
    {
        return $this->generateCardToken;
    }
}