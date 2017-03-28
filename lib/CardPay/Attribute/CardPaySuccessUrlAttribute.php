<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayUrlValidator;

trait CardPaySuccessUrlAttribute
{
    private $successUrl;

    public function setSuccessUrl($successUrl)
    {
        CardPayUrlValidator::validate($successUrl, "Success url");

        $this->successUrl = $successUrl;

        return $this;
    }

    public function getSuccessUrl()
    {
        return $this->successUrl;
    }
}