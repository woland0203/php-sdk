<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayStringValidator;

trait CardPayRRNAttribute
{
    private $rrn;

    public function setRRN($rrn)
    {
        $rrn = (string)$rrn;

        CardPayStringValidator::validate($rrn, "RRN", 1, 256);

        $this->rrn = $rrn;

        return $this;
    }

    public function getRRN()
    {
        return $this->rrn;
    }
}