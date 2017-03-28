<?php

namespace CardPay\Core;

use CardPay\Log\CardPayLogger;
use CardPay\Validation\CardPayAllowValidator;
use CardPay\Validation\CardPayIntegerValidator;
use CardPay\Validation\CardPaySecretValidator;
use CardPay\Validation\CardPayStringValidator;
use CardPay\Validation\CardPayUrlValidator;

class CardPayConfig
{
    private $mode;

    private $gatewayUrl;
    private $changeOrderStatusUrl;
    private $paymentsReportUrl;
    private $refundsReportUrl;

    private $walletId;
    private $secretKey;
    private $clientLogin;
    private $clientPasswordSha256;

    private $restApiLogin;
    private $restApiPassword;

    public function __construct($mode = CardPayEndpoint::LIVE)
    {
        CardPayAllowValidator::validate($mode, [CardPayEndpoint::TEST, CardPayEndpoint::LIVE], "mode");

        $this->mode = $mode;

        $this->setGatewayUrl(CardPayEndpoint::$GATEWAY_URLS[$this->mode]);
        $this->setChangeOrderStatusUrl(CardPayEndpoint::$CHANGE_ORDER_STATUS_URLS[$this->mode]);

        $this->setPaymentsReportUrl(CardPayEndpoint::$PAYMENTS_REPORT_URLS[$this->mode]);
        $this->setRefundsReportUrl(CardPayEndpoint::$REFUNDS_REPORT_URLS[$this->mode]);
    }

    public function setGatewayUrl($gatewayUrl)
    {
        CardPayUrlValidator::validate($gatewayUrl, "gatewayUrl");

        $this->gatewayUrl = $gatewayUrl;

        return $this;
    }

    public function getGatewayUrl()
    {
        return $this->gatewayUrl;
    }

    public function setChangeOrderStatusUrl($changeOrderStatusUrl)
    {
        CardPayUrlValidator::validate($changeOrderStatusUrl, "changeOrderStatusUrl");

        $this->changeOrderStatusUrl = $changeOrderStatusUrl;

        return $this;
    }

    public function getChangeOrderStatusUrl()
    {
        return $this->changeOrderStatusUrl;
    }

    public function setPaymentsReportUrl($paymentsReportUrl)
    {
        CardPayUrlValidator::validate($paymentsReportUrl, "paymentsReportUrl");

        $this->paymentsReportUrl = $paymentsReportUrl;

        return $this;
    }

    public function getPaymentsReportUrl()
    {
        return $this->paymentsReportUrl;
    }

    public function setRefundsReportUrl($refundsReportUrl)
    {
        CardPayUrlValidator::validate($refundsReportUrl, "refundsReportUrl");

        $this->refundsReportUrl = $refundsReportUrl;

        return $this;
    }

    public function getRefundsReportUrl()
    {
        return $this->refundsReportUrl;
    }

    public function setWalletId($walletId)
    {
        CardPayIntegerValidator::validate($walletId, "walletId");

        $this->walletId = $walletId;

        return $this;
    }

    public function getWalletId()
    {
        return $this->walletId;
    }

    public function setSecretKey($secretKey)
    {
        CardPaySecretValidator::validate($secretKey, "secretKey");

        $this->secretKey = $secretKey;

        return $this;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function setClientLogin($clientLogin)
    {
        CardPayStringValidator::validate($clientLogin, "clientLogin", 1, 200);

        $this->clientLogin = $clientLogin;

        return $this;
    }

    public function getClientLogin()
    {
        return $this->clientLogin;
    }

    public function setClientPasswordSHA256($clientPasswordSha256)
    {
        CardPayStringValidator::validate($clientPasswordSha256, "clientPasswordSha256", 64, 64);

        $this->clientPasswordSha256 = $clientPasswordSha256;

        return $this;
    }

    public function getClientPasswordSHA256()
    {
        return $this->clientPasswordSha256;
    }

    public function setRestApiLogin($restApiLogin)
    {
        CardPayStringValidator::validate($restApiLogin, "restApiLogin", 1, 200);

        $this->restApiLogin = $restApiLogin;

        return $this;
    }

    public function getRestApiLogin()
    {
        return $this->restApiLogin;
    }

    public function setRestApiPassword($restApiPassword)
    {
        CardPayStringValidator::validate($restApiPassword, "restApiPassword", 1, 64);

        $this->restApiPassword = $restApiPassword;

        return $this;
    }

    public function getRestApiPassword()
    {
        return $this->restApiPassword;
    }

    public function setLogFilePath($filePath)
    {
        CardPayLogger::setFilePath($filePath);

        return $this;
    }
}