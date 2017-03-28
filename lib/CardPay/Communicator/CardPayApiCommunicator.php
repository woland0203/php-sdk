<?php

namespace CardPay\Communicator;

use CardPay\Core\CardPayConfig;
use CardPay\Exception\CardPayCommunicatorException;
use CardPay\Helper\CardPayXmlHelper;
use CardPay\Log\CardPayLogger;
use CardPay\Validation\CardPayRequestValidator;
use CardPay\Validation\CardPayXmlValidator;
use SimpleXMLElement;

class CardPayApiCommunicator
{
    /**
     * @var CardPayConfig
     */
    public $config;

    private $requestUrl;
    private $request;

    private $response;

    /**
     * @var SimpleXMLElement
     */
    private $responseXmlObject;

    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;

        return $this;
    }

    public function setRequest($request)
    {
        CardPayRequestValidator::validate($request, "Api Request");

        $this->request = $request;

        return $this;
    }


    public function sendRequest()
    {
        CardPayLogger::log($this->request);

        $ch = curl_init($this->requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);

        $ch_error = curl_error($ch);

        if (!empty($ch_error)) {
            throw new CardPayCommunicatorException("Api Request error: {$ch_error}");
        }

        curl_close($ch);

        CardPayLogger::log($response);

        return $response;
    }

    public function setResponse($response)
    {
        CardPayXmlValidator::validate($response, "Api Response");
        $this->response = $response;

        return $this;
    }

    public function decodeResponseXml()
    {
        $this->responseXmlObject = simplexml_load_string($this->response);

        return $this;
    }

    public function getResponseXmlAttributes()
    {
        return CardPayXmlHelper::getXmlAttributes($this->responseXmlObject);
    }

    public function getResponseXmlObject()
    {
        return $this->responseXmlObject;
    }
}