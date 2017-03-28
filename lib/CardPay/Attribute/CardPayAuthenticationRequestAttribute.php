<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;

trait CardPayAuthenticationRequestAttribute
{
    private $authenticationRequest = false;

    public function setAuthenticationRequest($authenticationRequest = true)
    {
        CardPayAllowValidator::validate($authenticationRequest, ["true", "false", true, false, 1, 0], "Authentication request");

        $this->authenticationRequest = in_array($authenticationRequest, array("true", true, 1), true);

        return $this;
    }

    public function getAuthenticationRequest()
    {
        return $this->authenticationRequest;
    }
}