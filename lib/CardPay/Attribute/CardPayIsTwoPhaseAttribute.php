<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayAllowValidator;

trait CardPayIsTwoPhaseAttribute
{
    private $isTwoPhase = false;

    public function setIsTwoPhase($isTwoPhase = true)
    {
        CardPayAllowValidator::validate($isTwoPhase, ["true", "false", true, false, 1, 0], "Preauthorization (two phase)");

        $this->isTwoPhase = in_array($isTwoPhase, array("true", true, 1), true);

        return $this;
    }

    public function getIsTwoPhase()
    {
        return $this->isTwoPhase;
    }
}