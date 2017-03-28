<?php

namespace CardPay\Core;

use CardPay\Attribute\CardPayAmountAttribute;
use CardPay\Attribute\CardPayAuthorizationCodeAttribute;
use CardPay\Attribute\CardPayCurrencyAttribute;
use CardPay\Attribute\CardPayCustomerIdAttribute;
use CardPay\Attribute\CardPayDeclineCodeAttribute;
use CardPay\Attribute\CardPayDeclineReasonAttribute;
use CardPay\Attribute\CardPayDescriptionAttribute;
use CardPay\Attribute\CardPayEmailAttribute;
use CardPay\Attribute\CardPayIs3dsAttribute;
use CardPay\Attribute\CardPayNoteAttribute;
use CardPay\Attribute\CardPayOrderIdAttribute;
use CardPay\Attribute\CardPayRefundAmountAttribute;
use CardPay\Attribute\CardPayRefundedTransactionIdAttribute;
use CardPay\Attribute\CardPayRRNAttribute;
use CardPay\Attribute\CardPayStateAttribute;
use CardPay\Attribute\CardPayTransactionIdAttribute;
use CardPay\Attribute\CardPayTransactionCreatedTimestampAttribute;
use CardPay\Attribute\CardPayTransactionUpdatedTimestampAttribute;
use CardPay\Attribute\CardPayTypeAttribute;

class CardPayTransaction
{
    use CardPayTypeAttribute,
        CardPayTransactionIdAttribute,
        CardPayOrderIdAttribute,
        CardPayStateAttribute,
        CardPayTransactionCreatedTimestampAttribute,
        CardPayTransactionUpdatedTimestampAttribute,
        CardPayCustomerIdAttribute,
        CardPayDeclineReasonAttribute,
        CardPayDeclineCodeAttribute,
        CardPayAuthorizationCodeAttribute,
        CardPayIs3dsAttribute,
        CardPayCurrencyAttribute,
        CardPayAmountAttribute,
        CardPayRefundAmountAttribute,
        CardPayNoteAttribute,
        CardPayDescriptionAttribute,
        CardPayEmailAttribute,
        CardPayRRNAttribute,
        CardPayRefundedTransactionIdAttribute;
}