<?php

namespace CardPay\Communicator;

use CardPay\Core\CardPayConfig;
use CardPay\Exception\CardPayResponseException;
use CardPay\Validation\CardPayConfigValidator;

class CardPayGatewayModeCommunicator extends CardPayApiCommunicator
{
    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "gatewayUrl");

        $this->config = $config;

        $this->setRequestUrl($this->config->getGatewayUrl());

        return $this;
    }

    public function validateResponseXml()
    {
        $responseXmlObject = $this->getResponseXmlObject();

        $rootNodeName = strtoupper($responseXmlObject->getName());

        if ($rootNodeName != 'REDIRECT') {
            throw new CardPayResponseException("Incorrect structure of Gateway Mode Response xml");
        }

        return true;
    }
}