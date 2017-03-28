<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayTimestampValidator;

trait CardPayStartTimestampAttribute
{
    private $startTimestampMilliseconds;

    public function setStartTimestampMilliseconds($startTimestampMilliseconds)
    {
        $startTimestampMilliseconds = (string)$startTimestampMilliseconds;

        CardPayTimestampValidator::validateMilliseconds($startTimestampMilliseconds, "Start milliseconds");

        $this->startTimestampMilliseconds = $startTimestampMilliseconds;

        return $this;
    }

    public function getStartTimestampMilliseconds()
    {
        return $this->startTimestampMilliseconds;
    }

    public function getStartTimestamp()
    {
        return empty($this->startTimestampMilliseconds) ? null : substr($this->startTimestampMilliseconds, 0, -3);
    }
}