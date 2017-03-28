<?php

namespace CardPay\Core;

use CardPay\Attribute\CardPayEndTimestampAttribute;
use CardPay\Attribute\CardPayMaxCountAttribute;
use CardPay\Attribute\CardPayOrderIdAttribute;
use CardPay\Attribute\CardPayStartTimestampAttribute;
use CardPay\Attribute\CardPayTypeAttribute;
use CardPay\Communicator\CardPayRefundsReportCommunicator;
use CardPay\Exception\CardPayValidationException;
use CardPay\Validation\CardPayConfigValidator;

class CardPayRefundsReport
{
    use CardPayStartTimestampAttribute,
        CardPayEndTimestampAttribute,
        CardPayOrderIdAttribute,
        CardPayMaxCountAttribute;

    /** @var CardPayConfig */
    private $config;

    /**
     * @var CardPayRefundsReportCommunicator
     */
    private $communicator;

    private $transactions;

    public function __construct()
    {
        $this->communicator = new CardPayRefundsReportCommunicator();
    }

    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "walletId");

        $this->config = $config;
        $this->communicator->setConfig($this->config);

        return $this;
    }

    public function getQueryParams()
    {
        $queryParams = array();

        $queryParams["walletId"] = $this->config->getWalletId();

        empty($this->getStartTimestampMilliseconds()) || $queryParams["startMillis"] = $this->getStartTimestampMilliseconds();

        empty($this->getEndTimestampMilliseconds()) || $queryParams["endMillis"] = $this->getEndTimestampMilliseconds();

        if (!empty($queryParams["startMillis"]) && !empty($queryParams["endMillis"])) {
            $startMilliseconds = intval(substr($queryParams["startMillis"], 0, -3));
            $endMilliseconds = intval(substr($queryParams["endMillis"], 0, -3));

            if (strtotime("+7 days", $startMilliseconds) < $endMilliseconds) {
                throw new CardPayValidationException("'End milliseconds' has not a valid value. Must be less than 7 days after Start Milliseconds");
            }
        }

        empty($this->getOrderId()) || $queryParams["number"] = $this->getOrderId();

        empty($this->getMaxCount()) || $queryParams["maxCount"] = $this->getMaxCount();

        return $queryParams;
    }

    public function sendRequest()
    {
        $params = $this->getQueryParams();

        $response = $this->communicator
            ->setRequest("?" . http_build_query($params))
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

        foreach ($responseJsonObject->data as $transactionData) {
            $transactionObject = new CardPayTransaction();

            $transactionObject->setType(CardPayTypeAttribute::$TYPE_REFUNDS);

            empty($transactionData->id) || $transactionObject->setTransactionId($transactionData->id);
            empty($transactionData->number) || $transactionObject->setOrderId($transactionData->number);
            empty($transactionData->state) || $transactionObject->setState($transactionData->state);
            empty($transactionData->date) || $transactionObject->setTransactionCreatedTimestampMilliseconds($transactionData->date);
            empty($transactionData->customerId) || $transactionObject->setCustomerId($transactionData->customerId);
            empty($transactionData->declineReason) || $transactionObject->setDeclineReason($transactionData->declineReason);
            empty($transactionData->declineCode) || $transactionObject->setDeclineCode($transactionData->declineCode);
            empty($transactionData->authCode) || $transactionObject->setAuthorizationCode($transactionData->authCode);
            empty($transactionData->is3d) || $transactionObject->setIs3ds($transactionData->is3d);
            empty($transactionData->currency) || $transactionObject->setCurrency($transactionData->currency);
            empty($transactionData->amount) || $transactionObject->setAmount($transactionData->amount);
            empty($transactionData->refundedAmount) || $transactionObject->setRefundAmount($transactionData->refundedAmount);
            empty($transactionData->note) || $transactionObject->setNote($transactionData->note);
            empty($transactionData->description) || $transactionObject->setDescription($transactionData->description);
            empty($transactionData->email) || $transactionObject->setEmail($transactionData->email);
            empty($transactionData->rrn) || $transactionObject->setRRN($transactionData->rrn);
            empty($transactionData->originalOrderId) || $transactionObject->setRefundedTransactionId($transactionData->originalOrderId);

            $this->transactions[] = $transactionObject;
        }

        return true;
    }

    public function getTransactions()
    {
        return $this->transactions;
    }
}