<?php

namespace CardPay\Core;

use CardPay\Attribute\CardPayCurrencyAttribute;
use CardPay\Attribute\CardPayDetailsAttribute;
use CardPay\Attribute\CardPayIsExecutedAttribute;
use CardPay\Attribute\CardPayOrderIdAttribute;
use CardPay\Attribute\CardPayRefundReasonAttribute;
use CardPay\Attribute\CardPayRefundAmountAttribute;
use CardPay\Attribute\CardPayRefundedAmountAttribute;
use CardPay\Attribute\CardPayRefundIdAttribute;
use CardPay\Attribute\CardPayRemainingAmountAttribute;
use CardPay\Attribute\CardPayStatusAttribute;
use CardPay\Attribute\CardPayStatusToAttribute;
use CardPay\Communicator\CardPayChangeOrderStatusCommunicator;
use CardPay\Validation\CardPayAttributeValidator;
use CardPay\Validation\CardPayConfigValidator;

class CardPayChangeOrderStatus
{
    use CardPayOrderIdAttribute,
        CardPayStatusToAttribute,
        CardPayRefundAmountAttribute,
        CardPayRefundReasonAttribute,
        CardPayIsExecutedAttribute,
        CardPayRefundIdAttribute,
        CardPayDetailsAttribute,
        CardPayCurrencyAttribute,
        CardPayRefundedAmountAttribute,
        CardPayRemainingAmountAttribute,
        CardPayStatusAttribute;

    /** @var CardPayConfig */
    private $config;

    /**
     * @var CardPayChangeOrderStatusCommunicator
     */
    private $communicator;

    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "clientLogin");
        CardPayConfigValidator::validate($config, "clientPasswordSha256");

        $this->config = $config;

        $this->communicator = new CardPayChangeOrderStatusCommunicator();
        $this->communicator->setConfig($this->config);

        return $this;
    }

    public function getQueryParams()
    {
        $queryParams = array();

        $queryParams["client_login"] = $this->config->getClientLogin();
        $queryParams["client_password"] = $this->config->getClientPasswordSHA256();

        CardPayAttributeValidator::validate($this, "orderId");
        $queryParams["id"] = $this->getOrderId();

        CardPayAttributeValidator::validate($this, "statusTo");
        $queryParams["status_to"] = $this->getStatusTo();

        if ($this->isRefund()) {
            empty($this->getRefundAmount()) || $queryParams["amount"] = $this->getRefundAmount();

            CardPayAttributeValidator::validate($this, "refundReason");
            $queryParams["reason"] = $this->getRefundReason();
        }

        return $queryParams;
    }

    public function sendRequest()
    {
        $params = $this->getQueryParams();

        $response = $this->communicator
            ->setRequest(http_build_query($params))
            ->sendRequest();

        $this->communicator
            ->setResponse($response);

        return $this;
    }

    public function parseResponse()
    {
        $this->communicator->decodeResponseXml();

        $this->communicator->validateResponseXml();

        $responseXmlAttributes = $this->communicator->getResponseXmlAttributes();

        $this->setIsExecuted(isset($responseXmlAttributes->is_executed)
            ? $responseXmlAttributes->is_executed : null);

        if(!$this->isExecuted()){
            $this->setDetails($responseXmlAttributes->details);
        }

        $responseXmlOrderAttributes = $this->communicator->getResponseXmlOrderAttributes();

        $responseOrderId = isset($responseXmlOrderAttributes->id) ? $responseXmlOrderAttributes->id : null;
        $this->setOrderId($responseOrderId == $this->getOrderId() ? $responseOrderId : null);

        $responseStatusTo = isset($responseXmlOrderAttributes->status_to) ? $responseXmlOrderAttributes->status_to : null;
        $this->setStatusTo($responseStatusTo == $this->getStatusTo() ? $responseStatusTo : null);

        if ($this->isExecuted() && $this->isRefund()) {
            $this->setRefundId(isset($responseXmlAttributes->refund_id)
                ? $responseXmlAttributes->refund_id : null);

            $this->setCurrency(isset($responseXmlOrderAttributes->currency)
                ? $responseXmlOrderAttributes->currency : null);

            $this->setRefundedAmount(isset($responseXmlOrderAttributes->refund_amount)
                ? $responseXmlOrderAttributes->refund_amount : null);

            $this->setRemainingAmount(isset($responseXmlOrderAttributes->remaining_amount)
                ? $responseXmlOrderAttributes->remaining_amount : null);
        }

        empty($responseXmlOrderAttributes->status) || $this->setStatus($responseXmlOrderAttributes->status);

        return true;
    }
}