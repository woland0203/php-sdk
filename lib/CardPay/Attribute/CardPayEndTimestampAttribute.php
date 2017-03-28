<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayTimestampValidator;

trait CardPayEndTimestampAttribute
{
    private $endTimestampMilliseconds;

    public function setEndTimestampMilliseconds($endTimestampMilliseconds)
    {
        $endTimestampMilliseconds = (string)$endTimestampMilliseconds;

        CardPayTimestampValidator::validateMilliseconds($endTimestampMilliseconds, "End timestamp milliseconds");

        $this->endTimestampMilliseconds = $endTimestampMilliseconds;

        return $this;
    }

    public function getEndTimestampMilliseconds()
    {
        return $this->endTimestampMilliseconds;
    }

    public function getEndTimestamp()
    {
        return empty($this->endTimestampMilliseconds) ? null : substr($this->endTimestampMilliseconds, 0, -3);
    }
}