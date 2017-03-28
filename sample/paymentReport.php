<?php

use CardPay\Core\CardPayPaymentReport;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayPaymentReport = new CardPayPaymentReport();

    $cardPayPaymentReport
        /**
         * Setting config
         *
         * @required
         */
        ->setConfig($config)
        /**
         * ID assigned to the order in CardPay
         *
         * @required
         */
        ->setTransactionId(756529);

    $cardPayPaymentReport
        ->sendRequest()
        ->parseResponse();

    $transaction = $cardPayPaymentReport->getTransaction();

    CardPayLogger::log(print_r([
        "isPayment" => $transaction->isPayment(),
        "transactionId" => $transaction->getTransactionId(),
        "orderId" => $transaction->getOrderId(),
        "state" => $transaction->getState(),
        "created" => $transaction->getTransactionCreatedTimestampMilliseconds(),
        "updated" => $transaction->getTransactionUpdatedTimestampMilliseconds(),
        "declineCode" => $transaction->getDeclineCode(),
        "declineCodeDesc" => $transaction->getDeclineCodeDesc(),
        "description" => $transaction->getDescription(),
        "rrn" => $transaction->getRRN(),
    ], 1));

    switch (true) {
        case $transaction->isApproved():
            /**
             * Update order status in your system as approval
             */

            break;
        case $transaction->isInProgress():
        case $transaction->isPending():
            /**
             * Wait final order status like approved / decline / refund and etc.
             */

            break;
        case $transaction->isDeclined():
        case $transaction->isVoided():
        case $transaction->isRefunded():
        case $transaction->isChargeback():
        case $transaction->isChargebackResolved():
            /**
             * Update order status in your system as declined / voided / refunded / chargeback / chargeback resolved
             */

            break;
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}