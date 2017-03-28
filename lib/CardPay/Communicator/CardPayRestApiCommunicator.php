<?php

namespace CardPay\Communicator;

use CardPay\Core\CardPayConfig;
use CardPay\Exception\CardPayCommunicatorException;
use CardPay\Exception\CardPayResponseException;
use CardPay\Log\CardPayLogger;
use CardPay\Validation\CardPayJsonValidator;
use CardPay\Validation\CardPayRequestValidator;
use CardPay\Validation\CardPayUrlValidator;

class CardPayRestApiCommunicator
{
    /**
     * @var CardPayConfig
     */
    public $config;

    private $requestUrl;
    private $request;

    private $response;

    private $responseJsonObject;

    private function generateAuthorizationToken()
    {
        $login = $this->config->getRestApiLogin();
        $password = $this->config->getRestApiPassword();

        return base64_encode("{$login}:{$password}");
    }

    public function setRequestUrl($requestUrl)
    {
        CardPayUrlValidator::validate($requestUrl, "requestUrl");
        $this->requestUrl = $requestUrl;

        return $this;
    }

    public function setRequest($request)
    {
        CardPayRequestValidator::validate($request, "REST API Request");
        $this->request = $request;

        return $this;
    }


    public function sendRequest()
    {
        CardPayLogger::log($this->requestUrl . $this->request);

        $ch = curl_init($this->requestUrl . $this->request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Basic " . $this->generateAuthorizationToken()
        ));
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
        CardPayJsonValidator::validate($response, "REST API Response");
        $this->response = $response;

        return $this;
    }

    public function decodeResponseJson()
    {
        $this->responseJsonObject = json_decode($this->response);

        return $this;
    }

    public function validateResponseJson()
    {
        $responseJsonObject = $this->getResponseJsonObject();

        if (!isset($responseJsonObject->data)) {
            throw new CardPayResponseException("Incorrect structure of REST API Response json");
        }

        return true;
    }

    public function getResponseJsonObject()
    {
        return $this->responseJsonObject;
    }
}