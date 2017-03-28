<?php

namespace CardPay\Communicator;

use CardPay\Core\CardPayConfig;
use CardPay\Validation\CardPayConfigValidator;

class CardPayPaymentsReportCommunicator extends CardPayRestApiCommunicator
{
    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "paymentsReportUrl");
        CardPayConfigValidator::validate($config, "restApiLogin");
        CardPayConfigValidator::validate($config, "restApiPassword");

        $this->config = $config;

        $this->setRequestUrl($this->config->getPaymentsReportUrl());

        return $this;
    }
}