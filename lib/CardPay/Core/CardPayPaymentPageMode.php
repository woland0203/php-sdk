<?php

namespace CardPay\Core;

use CardPay\Attribute\CardPayAmountAttribute;
use CardPay\Attribute\CardPayAuthenticationRequestAttribute;
use CardPay\Attribute\CardPayCancelUrlAttribute;
use CardPay\Attribute\CardPayCardTokenAttribute;
use CardPay\Attribute\CardPayCurrencyAttribute;
use CardPay\Attribute\CardPayCustomerIdAttribute;
use CardPay\Attribute\CardPayDeclineUrlAttribute;
use CardPay\Attribute\CardPayDescriptionAttribute;
use CardPay\Attribute\CardPayEmailAttribute;
use CardPay\Attribute\CardPayGenerateCardTokenAttribute;
use CardPay\Attribute\CardPayItemsAttribute;
use CardPay\Attribute\CardPayLocaleAttribute;
use CardPay\Attribute\CardPayNoteAttribute;
use CardPay\Attribute\CardPayOrderIdAttribute;
use CardPay\Attribute\CardPayRecurringBeginAttribute;
use CardPay\Attribute\CardPayRecurringIdAttribute;
use CardPay\Attribute\CardPayReturnUrlAttribute;
use CardPay\Attribute\CardPayShippingAttribute;
use CardPay\Attribute\CardPaySuccessUrlAttribute;
use CardPay\Attribute\CardPayIsTwoPhaseAttribute;
use CardPay\Security\CardPayCipher;
use CardPay\Validation\CardPayAttributeValidator;
use CardPay\Validation\CardPayConfigValidator;

class CardPayPaymentPageMode
{
    use CardPayOrderIdAttribute,
        CardPayDescriptionAttribute,
        CardPayCurrencyAttribute,
        CardPayAmountAttribute,
        CardPayEmailAttribute,
        CardPayCustomerIdAttribute,
        CardPayIsTwoPhaseAttribute,
        CardPayRecurringBeginAttribute,
        CardPayRecurringIdAttribute,
        CardPayGenerateCardTokenAttribute,
        CardPayCardTokenAttribute,
        CardPayAuthenticationRequestAttribute,
        CardPayLocaleAttribute,
        CardPayNoteAttribute,
        CardPayReturnUrlAttribute,
        CardPaySuccessUrlAttribute,
        CardPayDeclineUrlAttribute,
        CardPayCancelUrlAttribute,
        CardPayShippingAttribute,
        CardPayItemsAttribute;

    private $config;

    private $orderXml;

    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "gatewayUrl");
        CardPayConfigValidator::validate($config, "walletId");
        CardPayConfigValidator::validate($config, "secretKey");

        $this->config = $config;

        return $this;
    }

    private function createOrderXML()
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        $xmlOrder = $xml->createElement("order");

        $xmlOrder->setAttribute("wallet_id", $this->config->getWalletId());

        CardPayAttributeValidator::validate($this, "orderId");
        $xmlOrder->setAttribute("number", $this->getOrderId());

        CardPayAttributeValidator::validate($this, "description");
        $xmlOrder->setAttribute("description", $this->getDescription());

        CardPayAttributeValidator::validate($this, "currency");
        $xmlOrder->setAttribute("currency", $this->getCurrency());

        CardPayAttributeValidator::validate($this, "amount");
        $xmlOrder->setAttribute("amount", $this->getAmount());

        CardPayAttributeValidator::validate($this, "email");
        $xmlOrder->setAttribute("email", $this->getEmail());

        empty($this->getCustomerId()) || $xmlOrder->setAttribute("customer_id", $this->getCustomerId());

        empty($this->getIsTwoPhase()) || $xmlOrder->setAttribute("is_two_phase", $this->getIsTwoPhase());

        empty($this->getRecurringBegin()) || $xmlOrder->setAttribute("recurring_begin", $this->getRecurringBegin());

        empty($this->getRecurringId()) || $xmlOrder->setAttribute("recurring_id", $this->getRecurringId());

        empty($this->getGenerateCardToken()) || $xmlOrder->setAttribute("generate_card_token", $this->getGenerateCardToken());

        empty($this->getCardToken()) || $xmlOrder->setAttribute("card_token", $this->getCardToken());

        empty($this->getAuthenticationRequest()) || $xmlOrder->setAttribute("authentication_request", $this->getAuthenticationRequest());

        empty($this->getLocale()) || $xmlOrder->setAttribute("locale", $this->getLocale());

        empty($this->getNote()) || $xmlOrder->setAttribute("note", $this->getNote());

        empty($this->getReturnUrl()) || $xmlOrder->setAttribute("return_url", $this->getReturnUrl());

        empty($this->getSuccessUrl()) || $xmlOrder->setAttribute("success_url", $this->getSuccessUrl());

        empty($this->getDeclineUrl()) || $xmlOrder->setAttribute("decline_url", $this->getDeclineUrl());

        empty($this->getCancelUrl()) || $xmlOrder->setAttribute("cancel_url", $this->getCancelUrl());

        if (!empty($this->getShipping())) {
            $xmlOrder->appendChild($xml->importNode($this->getShipping()->getXML(), true));
        }

        if (!empty($this->getItems())) {
            $xmlItems = $xml->createElement("items");

            /** @var CardPayItem $item */
            foreach ($this->getItems() as $item) {
                $xmlItems->appendChild($xml->importNode($item->getXML(), true));
            }

            $xmlOrder->appendChild($xmlItems);
        }

        return $xml->saveXML($xmlOrder, LIBXML_NOEMPTYTAG);
    }

    public function getOrderXML($base64 = false)
    {
        if (empty($this->orderXml)) {
            $this->orderXml = $this->createOrderXML();
        }

        return $base64 ? base64_encode($this->orderXml) : $this->orderXml;
    }

    public function getSHA512()
    {
        $cipher = new CardPayCipher($this->config->getSecretKey());

        return $cipher->signature($this->getOrderXML());
    }

    public function getSimpleForm()
    {
        $html = new \DOMDocument("1.0", "utf-8");
        $form = $html->createElement("form");

        $form->setAttribute("method", "POST");
        $form->setAttribute("action", $this->config->getGatewayUrl());

        $formOrderXMLInput = $html->createElement("input");
        $formOrderXMLInput->setAttribute("name", "orderXML");
        $formOrderXMLInput->setAttribute("type", "hidden");
        $formOrderXMLInput->setAttribute("value", $this->getOrderXML(true));
        $form->appendChild($formOrderXMLInput);

        $formSha512Input = $html->createElement("input");
        $formSha512Input->setAttribute("name", "sha512");
        $formSha512Input->setAttribute("type", "hidden");
        $formSha512Input->setAttribute("value", $this->getSHA512());
        $form->appendChild($formSha512Input);

        $formSubmitButton = $html->createElement("input");
        $formSubmitButton->setAttribute("type", "submit");
        $formSubmitButton->setAttribute("value", "Go to payment page");
        $form->appendChild($formSubmitButton);

        return $html->saveHTML($form);
    }
}