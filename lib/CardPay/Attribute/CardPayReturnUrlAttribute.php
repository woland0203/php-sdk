<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayUrlValidator;

trait CardPayReturnUrlAttribute
{
    private $returnUrl;

    public function setReturnUrl($returnUrl)
    {
        CardPayUrlValidator::validate($returnUrl, "Return url");

        $this->returnUrl = $returnUrl;

        return $this;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }
}