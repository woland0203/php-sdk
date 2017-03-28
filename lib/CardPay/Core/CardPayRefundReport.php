<?php

namespace CardPay\Core;

use CardPay\Attribute\CardPayTransactionIdAttribute;
use CardPay\Communicator\CardPayRefundsReportCommunicator;
use CardPay\Validation\CardPayAttributeValidator;

class CardPayRefundReport
{
    use CardPayTransactionIdAttribute;

    /** @var CardPayConfig */
    private $config;

    /**
     * @var CardPayRefundsReportCommunicator
     */
    private $communicator;

    /**
     * @var CardPayTransaction
     */
    private $transaction;

    public function __construct()
    {
        $this->communicator = new CardPayRefundsReportCommunicator();
    }

    public function setConfig(CardPayConfig $config)
    {
        $this->config = $config;
        $this->communicator->setConfig($this->config);

        return $this;
    }

    public function getQueryUrn()
    {
        $queryUrn = array();

        CardPayAttributeValidator::validate($this, "transactionId");
        $queryUrn["transactionId"] = $this->getTransactionId();

        return $queryUrn;
    }

    public function sendRequest()
    {
        $urn = $this->getQueryUrn();

        $response = $this->communicator
            ->setRequest("/" . implode("/", $urn))
            ->sendRequest();

        $this->communicator
            ->setResponse($response);

        return $this;
    }

    public function parseResponse()
    {
        $this->communicator->decodeResponseJson();

        $this->communicator->validateResponseJson();

        $responseJsonObject = $this->communicator->getResponseJsonObject();

        $transactionData = $responseJsonObject->data;

        $this->transaction = new CardPayTransaction();

        empty($transactionData->type) || $this->transaction->setType($transactionData->type);
        empty($transactionData->id) || $this->transaction->setTransactionId($transactionData->id);
        empty($transactionData->merchantOrderId) || $this->transaction->setOrderId($transactionData->merchantOrderId);
        empty($transactionData->state) || $this->transaction->setState($transactionData->state);
        empty($transactionData->created) || $this->transaction->setTransactionCreatedTimestampMilliseconds($transactionData->created);
        empty($transactionData->updated) || $this->transaction->setTransactionUpdatedTimestampMilliseconds($transactionData->updated);
        empty($transactionData->decline->code) || $this->transaction->setDeclineCode($transactionData->decline->code);
        empty($transactionData->description) || $this->transaction->setDescription($transactionData->description);
        empty($transactionData->rrn) || $this->transaction->setRRN($transactionData->rrn);

        return true;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }
}