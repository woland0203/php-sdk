<?php

use CardPay\Core\CardPayRefundReport;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayPaymentReport = new CardPayRefundReport();

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
        ->setTransactionId(758365);

    $cardPayPaymentReport
        ->sendRequest()
        ->parseResponse();

    $transaction = $cardPayPaymentReport->getTransaction();

    CardPayLogger::log(print_r([
        "isRefund" => $transaction->isRefund(),
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
             * Update refund status in your system as approval
             */

            break;
        case $transaction->isInProgress():
            /**
             * Wait final refund status like approved / decline and etc.
             */

            break;
        case $transaction->isDeclined():
        case $transaction->isVoided():
            /**
             * Update refund status in your system as declined / voided
             */

            break;
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}