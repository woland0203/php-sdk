<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayLocaleAttribute
{
    private $locale;

    public function setLocale($locale)
    {
        $locale = strtolower($locale);

        CardPayStringValidator::validate($locale, "Locale", 2, 2);

        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}