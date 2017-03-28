<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayTimestampValidator;

trait CardPayTransactionCreatedTimestampAttribute
{
    private $transactionCreatedTimestampMilliseconds;

    public function setTransactionCreatedTimestampMilliseconds($transactionCreatedTimestampMilliseconds)
    {
        $transactionCreatedTimestampMilliseconds = (string)$transactionCreatedTimestampMilliseconds;

        CardPayTimestampValidator::validateMilliseconds($transactionCreatedTimestampMilliseconds, "Transaction created timestamp milliseconds");

        $this->transactionCreatedTimestampMilliseconds = $transactionCreatedTimestampMilliseconds;

        return $this;
    }

    public function getTransactionCreatedTimestampMilliseconds()
    {
        return $this->transactionCreatedTimestampMilliseconds;
    }

    public function getTransactionCreatedTimestamp()
    {
        return empty($this->transactionCreatedTimestampMilliseconds) ? null : substr($this->transactionCreatedTimestampMilliseconds, 0, -3);
    }
}