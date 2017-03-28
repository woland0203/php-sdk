# PHP SDK for CardPay API

![Logo](https://www.cardpay.com/site/templates/images/cardpay-aq-logo.png)

## API Documentation

   * [ CardPay API Page ](https://integration.cardpay.com/api/)


## Prerequisites

   - PHP 5.6 or above
   - [curl](http://php.net/manual/en/book.curl.php), [xml](http://php.net/manual/en/book.xml.php)
   - [openssl](http://php.net/manual/en/book.openssl.php) extensions must be enabled for gateway mode

## Installing

With composer:

```bash
composer require cardpay/php-sdk
```

## Usage

### Table of contents

1. [Configuration](#1-configuration)
2. [Payment page mode](#2-payment-page-mode)
    1. [Simple payment](#21-simple-payment)
    2. [Authorized payment](#22-authorized-payment)
    3. [Recurring payment](#23-recurring-payment)
    4. [Pay with token](#24-pay-with-token)
    5. [Getting orderXML and SHA512](#25-getting-orderxml-and-sha512)
    6. [Adding shipping address](#26-adding-shipping-address)
    7. [Adding shopping cart items](#27-adding-shopping-cart-items)
3. [Gateway mode](#3-gateway-mode)
    1. [Simple payment](#31-simple-payment)
    2. [Authorized payment](#32-authorized-payment)
    3. [Recurring payment](#33-recurring-payment)
    4. [Pay with token](#34-pay-with-token)
    5. [Getting orderXML and SHA512](#35-getting-orderxml-and-sha512)
    6. [Adding shipping address](#36-adding-shipping-address)
    7. [Adding billing address](#37-adding-billing-address)
    8. [Adding shopping cart items](#38-adding-shopping-cart-items)
4. [Callback](#4-callback)
5. [Change order status](#5-change-order-status)
    1. [Capture authorized payment](#51-capture-authorized-payment)
    2. [Void authorized payment](#52-void-authorized-payment)
    3. [Refund payment](#53-refund-payment)
6. [Payment reports](#6-payment-reports)
    1. [Get list of payments](#61-get-list-of-payments)
    2. [State of payment](#62-state-of-payment)
7. [Refund reports](#7-refund-reports)
    1. [Get the list of refunds](#71-get-the-list-of-refunds)
    2. [State of refund](#72-state-of-refund)

### 1. Configuration 

```bash
~$ vim config.php
```

```php
<?php

use \CardPay\Core\CardPayConfig;

return (new CardPayConfig(CardPayMode::TEST))
    ->setWalletId(1234)
    ->setSecretKey('YourSecretWord')
    ->setClientLogin("YourLoginForPaymentManager")
    ->setClientPasswordSHA256("YourPasswordForPaymentManagerEncodedWithSHA256")
    ->setRestApiLogin("YourLoginForUsingRestApi")
    ->setRestApiPassword("YourPasswordForUsingRestApi")
    ->setLogFilepath(__DIR__.'/cardpay.log');
```


### 2. Payment page mode

Payment Page Mode is used when Merchant chooses to use our payment page.
Customer is redirected from merchant’s website to our payment page to enter card details.
Customer data entered on our payment page is protected and managed by CardPay and certified to and complies with PCI DSS standard.
All customer data is sent via secure connection.

#### 2.1. Simple payment

```php
<?php

use \CardPay\Core\CardPayPaymentPageMode;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayPaymentPageMode = new CardPayPaymentPageMode();

    $cardPayPaymentPageMode
        ->setConfig($config)
        ->setOrderId("1234567890")
        ->setDescription("Payment description")
        ->setCurrency("USD")
        ->setAmount(100)
        ->setEmail("payment@your-domain.com")
        ->setCustomerId('John Doe')
        ->setIsTwoPhase(false)
        ->setLocale("en")
        ->setNote("Payment note")
        ->setReturnUrl("https://www.your-domain.com/return_page.php");

    echo $cardPayPaymentPageMode->getSimpleForm();

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}
```

#### 2.2. Authorized payment

##### 1st step

The amount will not be captured but only blocked.

```php
...

    $cardPayPaymentPageMode
        ->setConfig($config)
        ->setOrderId("1234567890")
        ...
        ->setIsTwoPhase(true);

    echo $cardPayPaymentPageMode->getSimpleForm();

...
```

##### 2nd step

Use change order status

#### 2.3. Recurring payment

You can begin recurring by sending usual Order with the attribute “recurring_begin” having value “true”
in it and then repeat payments from the same card without asking the cardholder to enter card details again.
To do this you need to get the value of “recurring_id” attribute from the Payment Result XML or find it in “Recurring Billing” section of Payment Manager.
When you continue recurring with “recurring_id” Payment Page is not displayed because the same card is used as when recurring began. 

##### 1st step

The payment can be repeated later using recurring_id from response.

```php
...

    $cardPayPaymentPageMode
        ->setConfig($config)
        ->setOrderId("1234567890")
        ...
        ->setRecurringBegin(true);

    echo $cardPayPaymentPageMode->getSimpleForm();

...
```

##### 2nd step

Repeating payment sent before.

```php
...

    $cardPayPaymentPageMode
        ->setConfig($config)
        ->setOrderId("1234567890")
        ...
        ->setRecurringId(12345);

    echo $cardPayPaymentPageMode->getSimpleForm();

...
```

#### 2.4. Pay with token

Card Token feature is almost the same as Recurring, but the difference is that in this case each payment is made
with Cardholder present and requires CVV2/CVC2 and 3-D Secure if available.
You can obtain Card Token by sending usual Order with the attribute “generate_card_token” having value “true” in it.
The generated Card Token is sent only in a Callback and only if payment was successful. Each time token is requested, the new one is generated even for the same card.
To use Card Token you can send it in “card_token” attribute, so customer will not have to enter card details again, only CVV2/CVC2 and pass 3-D Secure if needed.

##### 1st step

A Card Token will be generated and returned in the response or in the notification XMLs.

```php
...

    $cardPayPaymentPageMode
        ->setConfig($config)
        ->setOrderId("1234567890")
        ...
        ->setGenerateCardToken(true);

    echo $cardPayPaymentPageMode->getSimpleForm();

...
```

##### 2nd step

Card Token used instead of card information.

```php
...

    $cardPayPaymentPageMode
        ->setConfig($config)
        ->setOrderId("1234567890")
        ...
        ->setCardToken(123456789);

    echo $cardPayPaymentPageMode->getSimpleForm();

...
```

#### 2.5. Getting orderXML and SHA512

```php
...

    $cardPayPaymentPageMode
        ->setConfig($config)
        ->setOrderId("1234567890")
        ...

    $base64Encode = true;
    echo $cardPayPaymentPageMode->getOrderXML($base64Encode);
    
    echo $cardPayPaymentPageMode->getSHA512();

...
```

#### 2.6. Adding shipping address

```php
...

    $cardPayAddress = new CardPayAddress();
    $cardPayAddress
        ->setCountry("USA")
        ->setState("NY")
        ->setZip("1234")
        ->setCity("New York")
        ->setStreet("Central Park, Big tree, Second branch")
        ->setPhone("+11123455678");

    $cardPayPaymentPageMode->setShipping($cardPayAddress);

...
```

#### 2.7. Adding shopping cart items

```php
...

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

    $cardPayPaymentPageMode->setItems([
        $cardPayItemOne,
        $cardPayItemTwo,
        $cardPayItemThree,
    ]);

...
```

#### Example

```bash
vim .sample/paymentPageMode.php
```

### 3. Gateway mode

In Gateway Mode the Customer enters credit card data on the Merchant’s website and order is sent
from Merchant to CardPay server-to-server. In this case the Merchant has to collect
cardholder data on his website that must be PCI DSS certified for that and then
send it as a POST request to the CardPay Payment Endpoint.

In response to Merchant’s request, CardPay server sends URL the Customer should be redirected to.
When payment is complete, customer will be redirected back to the shop to one of the predefined URLs.
Additionally, callbacks and/or email notifications will be sent.

#### 3.1. Simple payment

```php
<?php

use CardPay\Core\CardPayCard;
use CardPay\Core\CardPayGatewayMode;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayGatewayMode = new CardPayGatewayMode();
    $cardPayGatewayMode
        ->setConfig($config);
        ->setOrderId("1234567890")
        ->setDescription("Payment description")
        ->setCurrency("USD")
        ->setAmount(100)
        ->setEmail("payment@your-domain.com")
        ->setCustomerId("John Doe")
        ->setIsTwoPhase(false)
        ->setNote("Payment note")
        ->setReturnUrl("https://www.your-domain.com/return_page.php");

    $cardPayCard = new CardPayCard();
    $cardPayCard
        ->setCardNumber("4000000000000077")
        ->setCardholderName("John Doe")
        ->setExpirationDate(
            "2025",
            "12")
        ->setCvc("123");

    $cardPayGatewayMode->setCard($cardPayCard);

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
```

#### 3.2. Authorized payment

##### 1st step

The amount will not be captured but only blocked.

```php
...

    $cardPayGatewayMode
        ->setOrderId("1234567890")
        ...
        ->setIsTwoPhase(true);

    $cardPayGatewayMode
        ->sendRequest()
        ->parseResponse();

    header("Location: " . $cardPayGatewayMode->getRedirectUrl());

...
```

##### 2nd step

Use change order status

#### 3.3. Recurring payment

You can begin recurring by sending usual Order with the attribute “recurring_begin” having value “true”
in it and then repeat payments from the same card without asking the cardholder to enter card details again.
To do this you need to get the value of “recurring_id” attribute from the Payment Result XML or find it in “Recurring Billing” section of Payment Manager.
When you continue recurring with “recurring_id” you can omit “order/card” tag in orderXML because the same card is used as when recurring began.
Also instead of redirect XML for recurring continue Payment Result XML will be sent in response.

##### 1st step

The payment can be repeated later using recurring_id from response.

```php
...

    $cardPayGatewayMode
        ->setOrderId("1234567890")
        ...
        ->setRecurringBegin(true);

    $cardPayGatewayMode
        ->sendRequest()
        ->parseResponse();

    header("Location: " . $cardPayGatewayMode->getRedirectUrl());

...
```

##### 2nd step

Repeating payment sent before.

```php
...

    $cardPayGatewayMode
        ->setOrderId("1234567890")
        ...
        ->setRecurringId(12345);

    $cardPayGatewayMode
        ->sendRequest()
        ->parseResponse();

    header("Location: " . $cardPayGatewayMode->getRedirectUrl());

...
```

#### 3.4. Pay with token

Card Token feature is almost the same as Recurring, but the difference is that in this case each payment is made
with Cardholder present and requires CVV2/CVC2 and 3-D Secure if available.
You can obtain Card Token by sending usual Order with the attribute “generate_card_token” having value “true” in it.
The generated Card Token is sent only in a Callback and only if payment was successful. Each time token is requested, the new one is generated even for the same card.
To use Card Token you can send it in “card_token” attribute, in this case “order/card” tag must contain only “cvv” field.

##### 1st step

A Card Token will be generated and returned in the response or in the notification XMLs.

```php
...

    $cardPayGatewayMode
        ->setOrderId("1234567890")
        ...
        ->setGenerateCardToken(true);

    $cardPayGatewayMode
        ->sendRequest()
        ->parseResponse();

    header("Location: " . $cardPayGatewayMode->getRedirectUrl());

...
```

##### 2nd step

Card Token used instead of card information.

```php
...

    $cardPayGatewayMode
        ->setOrderId("1234567890")
        ...
        ->setCardToken(123456789);

    $cardPayGatewayMode
        ->sendRequest()
        ->parseResponse();

    header("Location: " . $cardPayGatewayMode->getRedirectUrl());

...
```

#### 3.5. Getting orderXML and SHA512

```php
...

    $cardPayGatewayMode
        ->setOrderId("1234567890")
        ...

    $base64Encode = true;
    echo $cardPayGatewayMode->getOrderXML($base64Encode);
    
    echo $cardPayGatewayMode->getSHA512();

...
```

#### 3.6. Adding shipping address

```php
...

    $cardPayAddress = new CardPayAddress();
    $cardPayAddress
        ->setCountry("USA")
        ->setState("NY")
        ->setZip("1234")
        ->setCity("New York")
        ->setStreet("Central Park, Big tree, Second branch")
        ->setPhone("+11123455678");

    $cardPayGatewayMode->setShipping($cardPayAddress);

...
```

#### 3.7. Adding billing address

```php
...

    $cardPayAddress = new CardPayAddress();
    $cardPayAddress
        ->setCountry("USA")
        ->setState("NY")
        ->setZip("1234")
        ->setCity("New York")
        ->setStreet("Central Park, Big tree, Second branch")
        ->setPhone("+11123455678");

    $cardPayGatewayMode->setBilling($cardPayAddress);

...
```

#### 3.8. Adding shopping cart items

```php
...

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

...
```

##### Example

```bash
~$ vim /sample/gatewayMode.php
```

### 4. Callback 

After the transaction is processed, Payment Result XML is sent to callback URL provided by merchant.
Callback URL can be set or changed only by CardPay manager.

Callback URL may contain URL Placeholders like Return URLs do, but note that values set to these placeholders are not signed
by digest and should not be used for making decisions, only for optimization.
To make any changes in order’s status you need to get status, ID and other fields from the orderXML after sha512 digest was validated.

Callbacks are always sent after received order was completed and when it’s status was changed.
If it was not delivered, callback will be repeated. Callback is marked as delivered if successful code (200) was received.
Also you can print some fixed text into your response (for example: “OK”) and notify CardPay manager
about it then callback is marked as delivered only if successful code (200) and expected text were received.

```php
<?php

use \CardPay\Core\CardPayCallback;
use \CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayCallback = new CardPayCallback();
    $cardPayCallback
        ->setConfig($config)
        ->setRequest($_REQUEST)
        ->parseRequest();

    switch (true) {
        case $cardPayCallback->isApproved():
            ...
            break;
        case $cardPayCallback->isPending():
            ...
            break;
        case $cardPayCallback->isDeclined():
        case $cardPayCallback->isVoided():
        case $cardPayCallback->isRefunded():
        case $cardPayCallback->isChargeback():
        case $cardPayCallback->isChargebackResolved():
            ...
            break;
    }

    header("HTTP/1.1 200 OK");

    echo "OK";
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());

    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
}

exit;
```

##### Example

```bash
~$ vim /sample/callback.php
```

### 5. Change order status

This method allows you to change order status. It allows to Capture 2-phase transactions, Void transactions when it’s possible or Refund part or full amount of order.

##### Authentication

To use this service you will need Payment Manager user login and SHA-256 HEX encoded digest of your password.
This service will affect only orders available for this user to be modified.

Note: It is strongly recommended to store digest of your password. Do not calculate it from your password for each request!

#### 5.1. Capture authorized payment 

```php
<?php

use CardPay\Core\CardPayChangeOrderStatus;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayChangeOrderStatus = new CardPayChangeOrderStatus($config);

    $cardPayChangeOrderStatus
        ->setConfig($config)
        ->setOrderId("756013")
        ->setCaptureStatus();

    $cardPayChangeOrderStatus
        ->sendRequest()
        ->parseResponse();

    if ($cardPayChangeOrderStatus->isExecuted()) {
        ...
    } else {
        ...
        echo $cardPayChangeOrderStatus->getDetails();
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}
```

##### Example

```bash
~$ vim /sample/changeOrderStatusCapture.php
```

#### 5.2. Void authorized payment

```php
...

    $cardPayChangeOrderStatus
        ->setConfig($config)
        ->setOrderId("756013")
        ->setVoidStatus();

    $cardPayChangeOrderStatus
        ->sendRequest()
        ->parseResponse();

    if ($cardPayChangeOrderStatus->isExecuted()) {
        ...
    } else {
        ...
        echo $cardPayChangeOrderStatus->getDetails();
    }
    
...
```

##### Example

```bash
~$ vim /sample/changeOrderStatusVoid.php
```

#### 5.3. Refund payment

```php
...

    $cardPayChangeOrderStatus
        ->setConfig($config)
        ->setOrderId("756529")
        ->setRefundStatus()
        ->setRefundReason("Out of stock");

    $cardPayChangeOrderStatus
        ->sendRequest()
        ->parseResponse();

    if ($cardPayChangeOrderStatus->isExecuted()) {
        ...
    } else {
        ...
        echo $cardPayChangeOrderStatus->getDetails();
    }
    
...
```

##### Example

```bash
~$ vim /sample/changeOrderStatusRefund.php
```

### 6. Payment reports

#### 6.1. Get list of payments

Get the list of payments for a period of time. This service will return only payments available for this user to be seen.

```php
<?php

use CardPay\Core\CardPayPaymentsReport;
use CardPay\Core\CardPayTransaction;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayPaymentsReport = new CardPayPaymentsReport();

    $cardPayPaymentsReport
        ->setConfig($config)
        ->setStartTimestampMilliseconds(strtotime("-7 days") . "000")
        ->setEndTimestampMilliseconds(time() . "000")
        //->setOrderId(123456789)
        ->setMaxCount(10000);

    $cardPayPaymentsReport
        ->sendRequest()
        ->parseResponse();

    $transactions = $cardPayPaymentsReport->getTransactions();

    /** @var CardPayTransaction $transaction */
    foreach ($transactions as $transaction) {
        switch (true) {
            case $transaction->isApproved():
                ...
                break;
            case $transaction->isInProgress():
            case $transaction->isPending():
                ...
                break;
            case $transaction->isDeclined():
            case $transaction->isVoided():
            case $transaction->isRefunded():
            case $transaction->isChargeback():
            case $transaction->isChargebackResolved():
                ...
                break;
        }
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}
```

##### Example

```bash
~$ vim /sample/paymentsReport.php
```

#### 6.2. State of payment

Use this call to get the state of the payment by it’s id.

```php
<?php

use CardPay\Core\CardPayPaymentReport;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayPaymentReport = new CardPayPaymentReport();

    $cardPayPaymentReport
        ->setConfig($config)
        ->setTransactionId(123456);

    $cardPayPaymentReport
        ->sendRequest()
        ->parseResponse();

    $transaction = $cardPayPaymentReport->getTransaction();

    switch (true) {
        case $transaction->isApproved():
            ...
            break;
        case $transaction->isInProgress():
        case $transaction->isPending():
            ...
            break;
        case $transaction->isDeclined():
        case $transaction->isVoided():
        case $transaction->isRefunded():
        case $transaction->isChargeback():
        case $transaction->isChargebackResolved():
            ...
            break;
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}
```

##### Example

```bash
~$ vim /sample/paymentReport.php
```

### 7. Refund reports

#### 7.1. Get the list of refunds

Get the list of refunds for a period of time. This service will return only refunds available for this user to be seen.

```php
<?php

use CardPay\Core\CardPayRefundsReport;
use CardPay\Core\CardPayTransaction;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayRefundsReport = new CardPayRefundsReport();

    $cardPayRefundsReport
        ->setConfig($config)
        ->setStartTimestampMilliseconds(strtotime("-7 days") . "000")
        ->setEndTimestampMilliseconds(time() . "000")
        //->setOrderId(1489398769)
        ->setMaxCount(10000);

    $cardPayRefundsReport
        ->sendRequest()
        ->parseResponse();

    $transactions = $cardPayRefundsReport->getTransactions();

    foreach ($transactions as $transaction) {
        switch (true) {
            case $transaction->isApproved():
                ...
                break;
            case $transaction->isInProgress():
                ...
                break;
            case $transaction->isDeclined():
            case $transaction->isVoided():
                ...
                break;
        }
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}
```

##### Example

```bash
~$ vim /sample/refundsReport.php
```

#### 7.2. State of refund

Use this call to get the state of the refund by it’s id.

```php
<?php

use CardPay\Core\CardPayRefundReport;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayPaymentReport = new CardPayRefundReport();

    $cardPayPaymentReport
        ->setConfig($config)
        ->setTransactionId(123456);

    $cardPayPaymentReport
        ->sendRequest()
        ->parseResponse();

    $transaction = $cardPayPaymentReport->getTransaction();

    switch (true) {
        case $transaction->isApproved():
            ...
            break;
        case $transaction->isInProgress():
            ...
            break;
        case $transaction->isDeclined():
        case $transaction->isVoided():
            ...
            break;
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}
```

##### Example

```bash
~$ vim /sample/refundReport.php
```

## Unit Testing

```bash
~$ composer update
~$ php ./vendor/bin/phpunit
```

## More help

   * [CardPay Website](https://www.cardpay.com)

