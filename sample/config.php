<?php

use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;

$cardPayConfig = new CardPayConfig(CardPayEndpoint::TEST);

return $cardPayConfig
    /**
     * Unique merchant’s wallet ID used by the CardPay payment system
     * Depending on integration method (Payment page mode or Gateway mode)
     *
     * @required
     */
    ->setWalletId(1234)
    /**
     * Merchant's Secret Word
     *
     * @required
     */
    ->setSecretKey("YourSecretWord")
    /**
     * User login. It is the same as for Payment Manager
     *
     * @required for refund
     */
    ->setClientLogin("YourLoginForPaymentManager")
    /**
     * SHA-256 HEX-encoded digest generated from the same password used in Payment Manager
     *
     * @required for refund
     */
    ->setClientPasswordSHA256("YourPasswordForPaymentManagerEncodedWithSHA256")
    /**
     * User login. It is the same as for Payment Manager
     * Ask support to create new user for report
     *
     * @required for reports
     */
    ->setRestApiLogin("YourLoginForUsingRestApi")
    /**
     * User password. It is the same as for Payment Manager
     *
     * @required for reports
     */
    ->setRestApiPassword("YourPasswordForUsingRestApi")
    /**
     * Path to log file
     *
     * @required
     */
    ->setLogFilePath(__DIR__ . "/cardpay.log");