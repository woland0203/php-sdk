<?php

namespace CardPay\Communicator;

use CardPay\Core\CardPayConfig;
use CardPay\Exception\CardPayResponseException;
use CardPay\Helper\CardPayXmlHelper;
use CardPay\Validation\CardPayConfigValidator;

class CardPayChangeOrderStatusCommunicator extends CardPayApiCommunicator
{
    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "changeOrderStatusUrl");

        $this->config = $config;

        $this->setRequestUrl($this->config->getChangeOrderStatusUrl());

        return $this;
    }

    public function validateResponseXml()
    {
        $responseXmlObject = $this->getResponseXmlObject();

        $rootNodeName = strtoupper($responseXmlObject->getName());

        if ($rootNodeName != 'RESPONSE') {
            throw new CardPayResponseException("Incorrect structure of Change Order Status Response xml");
        }

        if (!isset($responseXmlObject->order)) {
            throw new CardPayResponseException("Incorrect structure of Change Order Status Response xml. This xml has not order");
        }

        return true;
    }

    public function getResponseXmlOrderAttributes()
    {
        $responseXmlObject = $this->getResponseXmlObject();

        return CardPayXmlHelper::getXmlAttributes($responseXmlObject->order);
    }
}