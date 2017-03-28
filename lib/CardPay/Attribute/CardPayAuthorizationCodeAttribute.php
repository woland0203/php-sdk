<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayAuthorizationCodeAttribute
{
    private $authorizationCode;

    public function setAuthorizationCode($authorizationCode)
    {
        CardPayStringValidator::validate($authorizationCode, "Authorization code", 1, 200);

        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }
}