<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayTimestampValidator;

trait CardPayTransactionUpdatedTimestampAttribute
{
    private $transactionUpdatedTimestampMilliseconds;

    public function setTransactionUpdatedTimestampMilliseconds($transactionUpdatedTimestampMilliseconds)
    {
        $transactionUpdatedTimestampMilliseconds = (string)$transactionUpdatedTimestampMilliseconds;

        CardPayTimestampValidator::validateMilliseconds($transactionUpdatedTimestampMilliseconds, "Transaction updated timestamp milliseconds");

        $this->transactionUpdatedTimestampMilliseconds = $transactionUpdatedTimestampMilliseconds;

        return $this;
    }

    public function getTransactionUpdatedTimestampMilliseconds()
    {
        return $this->transactionUpdatedTimestampMilliseconds;
    }

    public function getTransactionUpdatedTimestamp()
    {
        return empty($this->transactionUpdatedTimestampMilliseconds) ? null : substr($this->transactionUpdatedTimestampMilliseconds, 0, -3);
    }
}