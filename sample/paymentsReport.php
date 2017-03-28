<?php

use CardPay\Core\CardPayPaymentsReport;
use CardPay\Core\CardPayTransaction;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayPaymentsReport = new CardPayPaymentsReport();

    $cardPayPaymentsReport
        /**
         * Setting config
         *
         * @required
         */
        ->setConfig($config)
        /**
         * Epoch time in milliseconds when requested period starts (inclusive),
         * default is 24 hours before endMillis
         */
        ->setStartTimestampMilliseconds(strtotime("-7 days") . "000")
        /**
         * Epoch time in milliseconds when requested period ends (not inclusive),
         * must be less than 7 days after startMillis,
         * default is current time
         */
        ->setEndTimestampMilliseconds(time() . "000")
        /**
         * Get list of orders by Order Number
         * Order ID used by the merchant’s shopping cart
         */
        //->setOrderId(1489398769)
        /**
         * Limit number of returned orders, must be less than 10,000,
         * default is 1,000
         */
        ->setMaxCount(10000);

    $cardPayPaymentsReport
        ->sendRequest()
        ->parseResponse();

    $transactions = $cardPayPaymentsReport->getTransactions();

    /** @var CardPayTransaction $transaction */
    foreach ($transactions as $transaction) {
        CardPayLogger::log(print_r([
            "isPayment" => $transaction->isPayment(),
            "transactionId" => $transaction->getTransactionId(),
            "orderId" => $transaction->getOrderId(),
            "state" => $transaction->getState(),
            "created" => $transaction->getTransactionCreatedTimestampMilliseconds(),
            "declineReason" => $transaction->getDeclineReason(),
            "declineCode" => $transaction->getDeclineCode(),
            "declineCodeDesc" => $transaction->getDeclineCodeDesc(),
            "authCode" => $transaction->getAuthorizationCode(),
            "is3ds" => $transaction->getIs3ds(),
            "currency" => $transaction->getCurrency(),
            "amount" => $transaction->getAmount(),
            "refundedAmount" => $transaction->getRefundAmount(),
            "note" => $transaction->getNote(),
            "description" => $transaction->getDescription(),
            "email" => $transaction->getEmail(),
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
    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}