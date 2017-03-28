<?php

use \CardPay\Core\CardPayCallback;
use \CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayCallback = new CardPayCallback();
    $cardPayCallback
        /**
         * Setting config
         *
         * @required
         */
        ->setConfig($config)
        /**
         * Setting request from CardPay
         *
         * @required
         */
        ->setRequest($_REQUEST)
        /**
         * Parsing request
         *
         * @required
         */
        ->parseRequest();

    CardPayLogger::log(print_r([
        "transactionId" => $cardPayCallback->getTransactionId(),
        "refundId" => $cardPayCallback->getRefundId(),
        "orderId" => $cardPayCallback->getOrderId(),
        "status" => $cardPayCallback->getStatus(),
        "description" => $cardPayCallback->getDescription(),
        "date" => $cardPayCallback->getDate(),
        "customerId" => $cardPayCallback->getCustomerId(),
        "cardBin" => $cardPayCallback->getCardBin(),
        "cardNumber" => $cardPayCallback->getCardNumber(),
        "cardholderName" => $cardPayCallback->getCardholderName(),
        "declineReason" => $cardPayCallback->getDeclineReason(),
        "declineCode" => $cardPayCallback->getDeclineCode(),
        "declineCodeDesc" => $cardPayCallback->getDeclineCodeDesc(),
        "authCode" => $cardPayCallback->getAuthorizationCode(),
        "is3ds" => $cardPayCallback->getIs3ds(),
        "currency" => $cardPayCallback->getCurrency(),
        "amount" => $cardPayCallback->getAmount(),
        "recurringId" => $cardPayCallback->getRecurringId(),
        "refundedAmount" => $cardPayCallback->getRefundedAmount(),
        "note" => $cardPayCallback->getNote(),
        "customerIp" => $cardPayCallback->getCustomerIp(),
    ], 1));

    CardPayLogger::log($cardPayCallback->getStatus());

    switch (true) {
        case $cardPayCallback->isApproved():
            /**
             * Update order status in your system as approval
             */

            break;
        case $cardPayCallback->isPending():
            /**
             * Wait final order status like approved / decline / refund and etc.
             */

            break;
        case $cardPayCallback->isDeclined():
        case $cardPayCallback->isVoided():
        case $cardPayCallback->isRefunded():
        case $cardPayCallback->isChargeback():
        case $cardPayCallback->isChargebackResolved():
            /**
             * Update order status in your system as declined / voided / refunded / chargeback / chargeback resolved
             */

            break;
    }

    header("HTTP/1.1 200 OK");

    echo "OK";
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());

    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
}

exit;