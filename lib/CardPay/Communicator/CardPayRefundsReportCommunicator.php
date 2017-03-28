<?php

namespace CardPay\Communicator;

use CardPay\Core\CardPayConfig;
use CardPay\Validation\CardPayConfigValidator;

class CardPayRefundsReportCommunicator extends CardPayRestApiCommunicator
{
    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "refundsReportUrl");
        CardPayConfigValidator::validate($config, "restApiLogin");
        CardPayConfigValidator::validate($config, "restApiPassword");

        $this->config = $config;

        $this->setRequestUrl($this->config->getRefundsReportUrl());

        return $this;
    }
}