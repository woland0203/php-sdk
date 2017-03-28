<?php

use CardPay\Core\CardPayAddress;
use CardPay\Core\CardPayCard;
use CardPay\Core\CardPayGatewayMode;
use CardPay\Core\CardPayItem;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayGatewayMode = new CardPayGatewayMode();
    $cardPayGatewayMode
        /**
         * Setting config
         *
         * @required
         */
        ->setConfig($config);

    /**
     * Show simple form for cardholder
     */
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo $cardPayGatewayMode->getSimpleForm();

        exit;
    }

    $cardPayGatewayMode
        /**
         * Order ID used by the merchant’s shopping cart
         *
         * @required
         */
        ->setOrderId(time())
        /**
         * Description of product/service being sold
         *
         * @required
         */
        ->setDescription("Test payment description")
        /**
         * ISO 4217 currency code
         *
         * @required
         */
        ->setCurrency("USD")
        /**
         * The total order amount in selected currency with dot as a decimal separator, must be less than a million
         *
         * @required
         */
        ->setAmount(100.11)
        /**
         * Customer’s e-mail address
         *
         * @required
         */
        ->setEmail("payment@your-domain.com")
        /**
         * Customer’s ID in the merchant’s system
         */
        //->setCustomerId("John Doe")
        /**
         * If set to “true”, the amount will not be captured but only blocked.
         */
        ->setIsTwoPhase(false)
        /**
         * If set to “true”, the payment can be repeated later using recurring_id from response.
         */
        //->setRecurringBegin(true)
        /**
         * Repeating payment sent before.
         */
        //->setRecurringId(12345)
        /**
         * If set to “true”, a Card Token will be generated and returned in the response or in the notification XMLs
         */
        //->setGenerateCardToken(true)
        /**
         * Card Token used instead of card information
         */
        //->setCardToken(123456789)
        /**
         * If set to “true”, amount must not be present in request, no payment will be made,
         * only cardholder authentication will be performed. Also can be used to generate Card Token.
         */
        //->setAuthenticationRequest(true)
        /**
         * Note about the order that will not be displayed to customer
         */
        //->setNote("Test payment note")
        /**
         * Overrides default success URL only.
         */
        //->setSuccessUrl("http://www.your-domain.com/success_page.php")
        /**
         * Overrides default decline URL only.
         */
        //->setDeclineUrl("http://www.your-domain.com/decline_page.php")
        /**
         * Overrides default success URL and decline URL.
         */
        ->setReturnUrl("http://www.your-domain.com/return_page.php");

    /**
     * Setting Card data for using below
     */
    $cardPayCard = new CardPayCard();
    $cardPayCard
        ->setCardNumber($_POST[CardPayGatewayMode::FIELD_CARD_NUMBER])
        ->setCardholderName($_POST[CardPayGatewayMode::FIELD_CARDHOLDER_NAME])
        ->setExpirationDate(
            $_POST[CardPayGatewayMode::FIELD_EXPIRATION_DATE_YEAR],
            $_POST[CardPayGatewayMode::FIELD_EXPIRATION_DATE_MONTH])
        ->setCvc($_POST[CardPayGatewayMode::FIELD_CVC]);

    /**
     * Represents a payment card.
     */
    $cardPayGatewayMode->setCard($cardPayCard);

    /**
     * Setting Address data for using below
     */
    $cardPayAddress = new CardPayAddress();
    $cardPayAddress
        ->setCountry("USA")
        ->setState("NY")
        ->setZip("1234")
        ->setCity("New York")
        ->setStreet("Central Park, Big tree, Second branch")
        ->setPhone("+11123455678");

    /**
     * Represents an address associated with the card.
     *
     * @required
     */
    $cardPayGatewayMode->setBilling($cardPayAddress);

    /**
     * Represents an address where the order will be delivered to.
     */
    $cardPayGatewayMode->setShipping($cardPayAddress);

    /**
     * List of order positions (items in the shopping cart).
     */
    $cardPayItemOne = new CardPayItem();
    $cardPayItemOne
        ->setName("Product 1")
        ->setDescription("Best product 1")
        ->setCount(2)
        ->setPrice("10.11");

    $cardPayItemTwo = new CardPayItem();
    $cardPayItemTwo
        ->setName("Product 2")
        ->setDescription("Best product 2")
        ->setCount(3)
        ->setPrice("20.11");

    $cardPayItemThree = new CardPayItem();
    $cardPayItemThree
        ->setName("Product 3")
        ->setDescription("Best product 3")
        ->setCount(1)
        ->setPrice("30.11");

    $cardPayGatewayMode->setItems([
        $cardPayItemOne,
        $cardPayItemTwo,
        $cardPayItemThree,
    ]);

    $cardPayGatewayMode
        ->sendRequest()
        ->parseResponse();

    header("Location: " . $cardPayGatewayMode->getRedirectUrl());

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}