<?php

namespace CardPay\Attribute;

use CardPay\Core\CardPayAddress;
use CardPay\Exception\CardPayValidationException;

trait CardPayBillingAttribute
{
    /**
     * @var CardPayAddress
     */
    private $billing;

    public function setBilling($address)
    {
        if (!($address instanceof CardPayAddress)) {
            throw new CardPayValidationException("Billing is not a billing object");
        }

        $this->billing = $address;

        return $this;
    }

    /**
     * @return CardPayAddress
     */
    public function getBilling()
    {
        $this->billing->setType(CardPayAddress::TYPE_BILLING);

        return $this->billing;
    }
}