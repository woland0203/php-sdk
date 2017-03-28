<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayUrlValidator;

trait CardPayCancelUrlAttribute
{
    private $cancelUrl;

    public function setCancelUrl($cancelUrl)
    {
        CardPayUrlValidator::validate($cancelUrl, "Cancel url");

        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }
}