<?php

namespace CardPay\Attribute;

use CardPay\Core\CardPayAddress;
use CardPay\Exception\CardPayValidationException;

trait CardPayShippingAttribute
{
    /**
     * @var CardPayAddress
     */
    private $shipping;

    public function setShipping($address)
    {
        if (!($address instanceof CardPayAddress)) {
            throw new CardPayValidationException("Shipping is not a address object");
        }

        $this->shipping = $address;

        return $this;
    }

    /**
     * @return CardPayAddress
     */
    public function getShipping()
    {
        if(empty($this->shipping)){
            return null;
        }

        $this->shipping->setType(CardPayAddress::TYPE_SHIPPING);

        return $this->shipping;
    }
}