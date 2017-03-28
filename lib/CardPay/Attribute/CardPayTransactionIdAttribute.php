<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayIntegerValidator;

trait CardPayTransactionIdAttribute
{
    private $transactionId;

    public function setTransactionId($transactionId)
    {
        CardPayIntegerValidator::validate($transactionId, "Transaction id", false);

        $this->transactionId = $transactionId;

        return $this;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }
}