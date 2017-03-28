<?php

use CardPay\Core\CardPayChangeOrderStatus;
use CardPay\Exception\CardPayLoggerException;
use CardPay\Log\CardPayLogger;

try {
    $config = require_once(__DIR__ . "/config.php");

    $cardPayChangeOrderStatus = new CardPayChangeOrderStatus($config);

    $cardPayChangeOrderStatus
        /**
         * Setting config
         *
         * @required
         */
        ->setConfig($config)
        /**
         * Order ID  to be changed
         *
         * @required
         */
        ->setOrderId("756529")
        /**
         * Change status to Refund
         *
         * @required
         */
        ->setRefundStatus()
        /**
         * Amount to be refunded. Total refund when not sent
         */
        //->setRefundAmount(100.10)
        /**
         * Reason of refunding
         *
         * @required
         */
        ->setRefundReason("Out of stock");

    /**
     * Sending request and parsing response
     */
    $cardPayChangeOrderStatus
        ->sendRequest()
        ->parseResponse();

    if ($cardPayChangeOrderStatus->isExecuted()) {
        /**
         * Order status has been changed successfully
         *
         * Wait callback for Updating order status in your system
         */

    } else {
        /**
         * An error has occurred. Look to details
         */
        echo $cardPayChangeOrderStatus->getDetails();

    }

    exit;
} catch (CardPayLoggerException $e) {
    exit("{$e->getMessage()} in {$e->getTraceAsString()}");
} catch (\Exception $e) {
    CardPayLogger::log($e->getMessage(), $e->getTraceAsString());
}