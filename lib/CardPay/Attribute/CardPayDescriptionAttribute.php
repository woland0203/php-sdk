<?php

namespace CardPay\Attribute;

use CardPay\Helper\CardPayStringHelper;
use CardPay\Validation\CardPayStringValidator;

trait CardPayDescriptionAttribute
{
    private $description;

    public function setDescription($description)
    {
        $description = (string)$description;

        CardPayStringValidator::validate($description, "Description", 1, 200);

        $this->description = CardPayStringHelper::normalizeString($description);

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }
}