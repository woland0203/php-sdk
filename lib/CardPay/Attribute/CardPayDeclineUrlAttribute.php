<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayUrlValidator;

trait CardPayDeclineUrlAttribute
{
    private $declineUrl;

    public function setDeclineUrl($declineUrl)
    {
        CardPayUrlValidator::validate($declineUrl, "Decline url");

        $this->declineUrl = $declineUrl;

        return $this;
    }

    public function getDeclineUrl()
    {
        return $this->declineUrl;
    }
}