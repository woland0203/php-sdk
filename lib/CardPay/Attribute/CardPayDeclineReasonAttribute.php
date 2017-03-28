<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayStringValidator;

trait CardPayDeclineReasonAttribute
{
    private $declineReason;

    public function setDeclineReason($declineReason)
    {
        CardPayStringValidator::validate($declineReason, "Decline reason", false);

        $this->declineReason = $declineReason;

        return $this;
    }

    public function getDeclineReason()
    {
        return $this->declineReason;
    }
}