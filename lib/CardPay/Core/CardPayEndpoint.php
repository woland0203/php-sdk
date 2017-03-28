<?php

namespace CardPay\Core;

class CardPayEndpoint
{
    const TEST = 0;
    const LIVE = 1;

    static $GATEWAY_URLS = array(
        CardPayEndpoint::TEST => "https://sandbox.cardpay.com/MI/cardpayment.html",
        CardPayEndpoint::LIVE => "https://cardpay.com/MI/cardpayment.html",
    );

    static $CHANGE_ORDER_STATUS_URLS = array(
        CardPayEndpoint::TEST => "https://sandbox.cardpay.com/MI/service/order-change-status",
        CardPayEndpoint::LIVE => "https://cardpay.com/MI/service/order-change-status",
    );

    static $PAYMENTS_REPORT_URLS = array(
        CardPayEndpoint::TEST => "https://sandbox.cardpay.com/MI/api/v2/payments",
        CardPayEndpoint::LIVE => "https://cardpay.com/MI/api/v2/payments",
    );

    static $REFUNDS_REPORT_URLS = array(
        CardPayEndpoint::TEST => "https://sandbox.cardpay.com/MI/api/v2/refunds",
        CardPayEndpoint::LIVE => "https://cardpay.com/MI/api/v2/refunds",
    );
}