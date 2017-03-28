<?php

namespace CardPay\Communicator;

use CardPay\Core\CardPayConfig;
use CardPay\Exception\CardPayCallbackException;
use CardPay\Helper\CardPayXmlHelper;
use CardPay\Log\CardPayLogger;
use CardPay\Security\CardPayCipher;
use CardPay\Validation\CardPayBase64Validator;
use CardPay\Validation\CardPayCallbackValidator;
use CardPay\Validation\CardPayConfigValidator;
use CardPay\Validation\CardPayHashValidator;
use CardPay\Validation\CardPayXmlValidator;

class CardPayCallbackCommunicator
{
    /**
     * @var CardPayConfig
     */
    private $config;

    private $request;
    private $requestXml;
    private $requestXmlObject;

    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "secretKey");

        $this->config = $config;

        return $this;
    }

    public function setRequest($request)
    {
        CardPayLogger::log($request);

        CardPayCallbackValidator::validate($request, "Callback Request");
        $this->request = $request;

        return $this;
    }

    public function validateRequest()
    {
        CardPayBase64Validator::validate($this->request["orderXML"]
            ?: null, "orderXML");
        $requestXml = base64_decode($this->request["orderXML"]);

        CardPayXmlValidator::validate($requestXml, "Callback Request Order XML");
        $this->requestXml = $requestXml;

        CardPayHashValidator::validate($this->request["sha512"]
            ?: null, "sha512");

        $cipher = new CardPayCipher($this->config->getSecretKey());
        $cipher->verify($this->requestXml, $this->request["sha512"]);

        return $this;
    }

    public function decodeRequestXml()
    {
        $this->requestXmlObject = simplexml_load_string($this->requestXml);

        return $this;
    }

    public function validateRequestXml()
    {
        $rootNodeName = strtoupper($this->requestXmlObject->getName());

        if ($rootNodeName != 'ORDER') {
            throw new CardPayCallbackException("Incorrect structure of Callback Response xml");
        }

        return true;
    }

    public function getRequestXmlAttributes()
    {
        return CardPayXmlHelper::getXmlAttributes($this->requestXmlObject);
    }

    public function getRequestXmlObject()
    {
        return $this->requestXmlObject;
    }
}