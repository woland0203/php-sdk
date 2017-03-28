<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayUrlValidator;

trait CardPayRedirectUrlAttribute
{
    private $redirectUrl;

    public function setRedirectUrl($redirectUrl)
    {
        CardPayUrlValidator::validate($redirectUrl, "Redirect url");

        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
}