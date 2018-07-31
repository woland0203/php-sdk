<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayStringValidator;

trait CardPayTransactionIdAttribute
{
    private $transactionId;

    public function setTransactionId($transactionId)
    {
        CardPayStringValidator::validate($transactionId, "Transaction id", 1, 256);

        $this->transactionId = $transactionId;

        return $this;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }
}