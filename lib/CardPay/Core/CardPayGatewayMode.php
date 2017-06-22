<?php

namespace CardPay\Core;

use CardPay\Attribute\CardPayAmountAttribute;
use CardPay\Attribute\CardPayAuthenticationRequestAttribute;
use CardPay\Attribute\CardPayBillingAttribute;
use CardPay\Attribute\CardPayCancelUrlAttribute;
use CardPay\Attribute\CardPayCardAttribute;
use CardPay\Attribute\CardPayCardTokenAttribute;
use CardPay\Attribute\CardPayCurrencyAttribute;
use CardPay\Attribute\CardPayCustomerIdAttribute;
use CardPay\Attribute\CardPayDeclineUrlAttribute;
use CardPay\Attribute\CardPayDescriptionAttribute;
use CardPay\Attribute\CardPayEmailAttribute;
use CardPay\Attribute\CardPayGenerateCardTokenAttribute;
use CardPay\Attribute\CardPayItemsAttribute;
use CardPay\Attribute\CardPayNoteAttribute;
use CardPay\Attribute\CardPayOrderIdAttribute;
use CardPay\Attribute\CardPayRecurringBeginAttribute;
use CardPay\Attribute\CardPayRecurringIdAttribute;
use CardPay\Attribute\CardPayRedirectUrlAttribute;
use CardPay\Attribute\CardPayReturnUrlAttribute;
use CardPay\Attribute\CardPayShippingAttribute;
use CardPay\Attribute\CardPaySuccessUrlAttribute;
use CardPay\Attribute\CardPayIsTwoPhaseAttribute;
use CardPay\Communicator\CardPayGatewayModeCommunicator;
use CardPay\Security\CardPayCipher;
use CardPay\Validation\CardPayAttributeValidator;
use CardPay\Validation\CardPayConfigValidator;

class CardPayGatewayMode
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
        CardPayNoteAttribute,
        CardPayReturnUrlAttribute,
        CardPaySuccessUrlAttribute,
        CardPayDeclineUrlAttribute,
        CardPayCardAttribute,
        CardPayShippingAttribute,
        CardPayBillingAttribute,
        CardPayItemsAttribute,
        CardPayRedirectUrlAttribute;

    const FIELD_CARD_NUMBER = "cardNumber";
    const FIELD_CARDHOLDER_NAME = "cardholderName";
    const FIELD_EXPIRATION_DATE_YEAR = "expirationDateYear";
    const FIELD_EXPIRATION_DATE_MONTH = "expirationDateMonth";
    const FIELD_CVC = "cvc";

    /** @var CardPayConfig */
    private $config;

    private $orderXml;

    /**
     * @var CardPayGatewayModeCommunicator
     */
    private $communicator;

    public function setConfig(CardPayConfig $config)
    {
        CardPayConfigValidator::validate($config, "walletId");
        CardPayConfigValidator::validate($config, "secretKey");

        $this->config = $config;

        $this->communicator = new CardPayGatewayModeCommunicator();
        $this->communicator->setConfig($this->config);

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

        empty($this->getGenerateCardToken()) || $xmlOrder->setAttribute("generate_card_token",
            $this->getGenerateCardToken());

        empty($this->getCardToken()) || $xmlOrder->setAttribute("card_token", $this->getCardToken());

        empty($this->getAuthenticationRequest()) || $xmlOrder->setAttribute("authentication_request",
            $this->getAuthenticationRequest());

        empty($this->getNote()) || $xmlOrder->setAttribute("note", $this->getNote());

        empty($this->getReturnUrl()) || $xmlOrder->setAttribute("return_url", $this->getReturnUrl());

        empty($this->getSuccessUrl()) || $xmlOrder->setAttribute("success_url", $this->getSuccessUrl());

        empty($this->getDeclineUrl()) || $xmlOrder->setAttribute("decline_url", $this->getDeclineUrl());

        if(empty($this->getRecurringId())){
            CardPayAttributeValidator::validate($this, "card");
            $xmlOrder->appendChild($xml->importNode($this->getCard()->getXML(), true));

            CardPayAttributeValidator::validate($this, "billing");
            $xmlOrder->appendChild($xml->importNode($this->getBilling()->getXML(), true));
        }

        if (!empty($this->getShipping())) {
            $xmlOrder->appendChild($xml->importNode($this->getShipping()->getXML(), true));
        }

        if (!empty($this->getItems())) {
            $xml_items = $xml->createElement("items");

            /** @var CardPayItem $item */
            foreach ($this->getItems() as $item) {
                $xml_items->appendChild($xml->importNode($item->getXML(), true));
            }

            $xmlOrder->appendChild($xml_items);
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

    public function sendRequest()
    {
        $params = [
            "orderXML" => $this->getOrderXML(true),
            "sha512" => $this->getSHA512(),
        ];

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

        $this->setRedirectUrl(isset($responseXmlAttributes->url)
            ? $responseXmlAttributes->url : null);

        return true;
    }

    public function getSimpleForm()
    {
        $html = new \DOMDocument("1.0", "utf-8");
        $form = $html->createElement("form");

        $form->setAttribute("method", "POST");
        $form->setAttribute("action", $_SERVER["REQUEST_URI"]);

        $formCardNumberLabel = $html->createElement("label");
        $formCardNumberLabel->textContent = "Card Number: ";
        $form->appendChild($formCardNumberLabel);

        $formCardNumberInput = $html->createElement("input");
        $formCardNumberInput->setAttribute("name", self::FIELD_CARD_NUMBER);
        $formCardNumberInput->setAttribute("type", "string");
        $form->appendChild($formCardNumberInput);

        $form->appendChild($html->createElement("br"));

        $formCardholderNameLabel = $html->createElement("label");
        $formCardholderNameLabel->textContent = "Cardholder Name: ";
        $form->appendChild($formCardholderNameLabel);

        $formCardholderNameInput = $html->createElement("input");
        $formCardholderNameInput->setAttribute("name", self::FIELD_CARDHOLDER_NAME);
        $formCardholderNameInput->setAttribute("type", "string");
        $form->appendChild($formCardholderNameInput);

        $form->appendChild($html->createElement("br"));

        $formExpirationDateLabel = $html->createElement("label");
        $formExpirationDateLabel->textContent = "Expiration Date: ";
        $form->appendChild($formExpirationDateLabel);

        $formExpirationDateYearSelect = $html->createElement("select");
        $formExpirationDateYearSelect->setAttribute("name", self::FIELD_EXPIRATION_DATE_YEAR);

        for ($expirationDateYear = date("Y"); $expirationDateYear <= date("Y", strtotime("+10 year")); $expirationDateYear++) {
            $formExpirationDateYearOption = $html->createElement("option");
            $formExpirationDateYearOption->setAttribute("value", $expirationDateYear);
            $formExpirationDateYearOption->textContent = $expirationDateYear;
            $formExpirationDateYearSelect->appendChild($formExpirationDateYearOption);
        }

        $form->appendChild($formExpirationDateYearSelect);

        $formExpirationDateMonthSelect = $html->createElement("select");
        $formExpirationDateMonthSelect->setAttribute("name", self::FIELD_EXPIRATION_DATE_MONTH);

        for ($expirationDateMonth = 1; $expirationDateMonth <= 12; $expirationDateMonth++) {
            $expirationDateMonthValue = substr("0" . $expirationDateMonth, -2);

            $formExpirationDateMonthOption = $html->createElement("option");
            $formExpirationDateMonthOption->setAttribute("value", $expirationDateMonthValue);
            $formExpirationDateMonthOption->textContent = $expirationDateMonthValue;
            $formExpirationDateMonthSelect->appendChild($formExpirationDateMonthOption);
        }

        $form->appendChild($formExpirationDateMonthSelect);

        $form->appendChild($html->createElement("br"));

        $formCvcLabel = $html->createElement("label");
        $formCvcLabel->textContent = "CVV2/CVC2: ";
        $form->appendChild($formCvcLabel);

        $formCvcInput = $html->createElement("input");
        $formCvcInput->setAttribute("name", self::FIELD_CVC);
        $formCvcInput->setAttribute("type", "string");
        $form->appendChild($formCvcInput);

        $form->appendChild($html->createElement("br"));

        $formSubmitButton = $html->createElement("input");
        $formSubmitButton->setAttribute("type", "submit");
        $formSubmitButton->setAttribute("value", "Pay now");
        $form->appendChild($formSubmitButton);

        return $html->saveHTML($form);
    }
}