<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;

trait CardPayIs3dsAttribute
{
    private $is3ds = false;

    public function setIs3ds($is3ds = true)
    {
        CardPayAllowValidator::validate($is3ds, ["true", "false", true, false, 1, 0], "Is 3ds");

        $this->is3ds = ($is3ds == "true");

        return $this;
    }

    public function getIs3ds()
    {
        return $this->is3ds;
    }

    public function is3ds()
    {
        return $this->is3ds;
    }
}