<?php

namespace CardPay\Core;

use CardPay\Attribute\CardPayAuthorizationCodeAttribute;
use CardPay\Attribute\CardPayIs3dsAttribute;
use CardPay\Attribute\CardPayAmountAttribute;
use CardPay\Attribute\CardPayApprovalCodeAttribute;
use CardPay\Attribute\CardPayCardBinAttribute;
use CardPay\Attribute\CardPayCardholderNameAttribute;
use CardPay\Attribute\CardPayCardNumberAttribute;
use CardPay\Attribute\CardPayCardTokenAttribute;
use CardPay\Attribute\CardPayCurrencyAttribute;
use CardPay\Attribute\CardPayCustomerIdAttribute;
use CardPay\Attribute\CardPayCustomerIpAttribute;
use CardPay\Attribute\CardPayDateAttribute;
use CardPay\Attribute\CardPayDeclineCodeAttribute;
use CardPay\Attribute\CardPayDeclineReasonAttribute;
use CardPay\Attribute\CardPayDescriptionAttribute;
use CardPay\Attribute\CardPayNoteAttribute;
use CardPay\Attribute\CardPayOrderIdAttribute;
use CardPay\Attribute\CardPayRecurringIdAttribute;
use CardPay\Attribute\CardPayRefundedAmountAttribute;
use CardPay\Attribute\CardPayRefundIdAttribute;
use CardPay\Attribute\CardPayStatusAttribute;
use CardPay\Attribute\CardPayTransactionIdAttribute;
use CardPay\Communicator\CardPayCallbackCommunicator;

class CardPayCallback
{
    use CardPayTransactionIdAttribute,
        CardPayRefundIdAttribute,
        CardPayOrderIdAttribute,
        CardPayStatusAttribute,
        CardPayDescriptionAttribute,
        CardPayDateAttribute,
        CardPayIs3dsAttribute,
        CardPayAmountAttribute,
        CardPayCurrencyAttribute,
        CardPayCustomerIdAttribute,
        CardPayCardBinAttribute,
        CardPayCardNumberAttribute,
        CardPayCardholderNameAttribute,
        CardPayDeclineReasonAttribute,
        CardPayDeclineCodeAttribute,
        CardPayAuthorizationCodeAttribute,
        CardPayRecurringIdAttribute,
        CardPayRefundedAmountAttribute,
        CardPayNoteAttribute,
        CardPayCustomerIpAttribute,
        CardPayCardTokenAttribute;

    /** @var CardPayConfig */
    private $config;

    /** @var CardPayCallbackCommunicator */
    private $communicator;

    public function setConfig(CardPayConfig $config)
    {
        $this->config = $config;

        $this->communicator = new CardPayCallbackCommunicator();
        $this->communicator->setConfig($config);

        return $this;
    }

    public function setRequest($request)
    {
        $this->communicator->setRequest($request);

        return $this;
    }

    public function parseRequest()
    {
        $this->communicator->validateRequest();

        $this->communicator->decodeRequestXml();

        $this->communicator->validateRequestXml();

        $orderXmlAttributes = $this->communicator->getRequestXmlAttributes();

        $this->setTransactionId(isset($orderXmlAttributes->id)
            ? $orderXmlAttributes->id : null);

        $this->setOrderId(isset($orderXmlAttributes->number)
            ? $orderXmlAttributes->number : null);

        $this->setStatus(isset($orderXmlAttributes->status)
            ? $orderXmlAttributes->status : null);

        $this->setDescription(isset($orderXmlAttributes->description)
            ? $orderXmlAttributes->description : null);

        $this->setDate(isset($orderXmlAttributes->date)
            ? $orderXmlAttributes->date : null);

        $this->setIs3ds(isset($orderXmlAttributes->is_3d)
            ? $orderXmlAttributes->is_3d : null);

        $this->setCurrency(isset($orderXmlAttributes->currency)
            ? $orderXmlAttributes->currency : null);

        $this->setAmount(isset($orderXmlAttributes->amount)
            ? $orderXmlAttributes->amount : null);

        empty($orderXmlAttributes->customer_id) || $this->setCustomerId($orderXmlAttributes->customer_id);

        empty($orderXmlAttributes->card_bin) || $this->setCardBin($orderXmlAttributes->card_bin);
        empty($orderXmlAttributes->card_num) || $this->setCardNumber($orderXmlAttributes->card_num);
        empty($orderXmlAttributes->card_holder) || $this->setCardholderName($orderXmlAttributes->card_holder);

        if ($this->isApproved()) {
            empty($orderXmlAttributes->approval_code) || $this->setAuthorizationCode($orderXmlAttributes->approval_code);
        }

        if ($this->isDeclined()) {
            empty($orderXmlAttributes->decline_reason) || $this->setDeclineReason($orderXmlAttributes->decline_reason);
            empty($orderXmlAttributes->decline_code) || $this->setDeclineCode($orderXmlAttributes->decline_code);
        }

        empty($orderXmlAttributes->recurring_id) || $this->setRecurringId($orderXmlAttributes->recurring_id);

        if ($this->isRefunded()) {
            $this->setRefundId($orderXmlAttributes->refund_id
                ?: null);
            empty($orderXmlAttributes->refunded) || $this->setRefundedAmount($orderXmlAttributes->refunded);
        }

        empty($orderXmlAttributes->note) || $this->setNote($orderXmlAttributes->note);
        empty($orderXmlAttributes->ip) || $this->setCustomerIp($orderXmlAttributes->ip);
        empty($orderXmlAttributes->card_token) || $this->setCardToken($orderXmlAttributes->card_token);

        return true;
    }
}