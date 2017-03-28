<?php

namespace CardPay\Core;

use CardPay\Exception\CardPayValidationException;
use CardPay\Helper\CardPayStringHelper;
use CardPay\Validation\CardPayAllowValidator;
use CardPay\Validation\CardPayAttributeValidator;
use CardPay\Validation\CardPayStringValidator;

class CardPayAddress
{
    const TYPE_BILLING = 1;
    const TYPE_SHIPPING = 2;

    private $type;

    private $country;
    private $state;
    private $zip;
    private $city;
    private $street;
    private $phone;

    public function setType($type)
    {
        CardPayAllowValidator::validate($type, [self::TYPE_BILLING, self::TYPE_SHIPPING], "Address type");

        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setCountry($country)
    {
        CardPayStringValidator::validate($country, "Country", 2, 3);

        $this->country = $country;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }


    public function setState($state)
    {
        CardPayStringValidator::validate($state, "State", 2, 20);

        $this->state = CardPayStringHelper::normalizeString($state);

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }


    public function setZip($zip)
    {
        CardPayStringValidator::validate($zip, "Zip", 2, 12);

        $this->zip = $zip;

        return $this;
    }

    public function getZip()
    {
        return $this->zip;
    }


    public function setCity($city)
    {
        CardPayStringValidator::validate($city, "City", 2, 20);

        $this->city = CardPayStringHelper::normalizeString($city);

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }


    public function setStreet($street)
    {
        CardPayStringValidator::validate($street, "Street", 2, 100);

        $this->street = CardPayStringHelper::normalizeString($street);

        return $this;
    }

    public function getStreet()
    {
        return $this->street;
    }


    public function setPhone($phone)
    {
        CardPayStringValidator::validate($phone, "Phone", 5, 20);

        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getXML()
    {
        switch($this->type){
            case self::TYPE_BILLING:
                return $this->getBillingXML();
            case self::TYPE_SHIPPING:
                return $this->getShippingXML();
            default:
                throw new CardPayValidationException("Address has not type");
        }
    }

    private function getBillingXML()
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        $xmlBilling = $xml->createElement("billing");

        CardPayAttributeValidator::validate($this, "country");
        $xmlBilling->setAttribute("country", $this->getCountry());

        empty($this->getState()) || $xmlBilling->setAttribute("state", $this->getState());

        CardPayAttributeValidator::validate($this, "zip");
        $xmlBilling->setAttribute("zip", $this->getZip());

        CardPayAttributeValidator::validate($this, "city");
        $xmlBilling->setAttribute("city", $this->getCity());

        CardPayAttributeValidator::validate($this, "street");
        $xmlBilling->setAttribute("street", $this->getStreet());

        CardPayAttributeValidator::validate($this, "phone");
        $xmlBilling->setAttribute("phone", $this->getPhone());

        return $xmlBilling;
    }

    private function getShippingXML()
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        $xmlShipping = $xml->createElement("shipping");

        CardPayAttributeValidator::validate($this, "country");
        $xmlShipping->setAttribute("country", $this->getCountry());

        empty($this->getState()) || $xmlShipping->setAttribute("state", $this->getState());

        empty($this->getZip()) || $xmlShipping->setAttribute("zip", $this->getZip());

        empty($this->getCity()) || $xmlShipping->setAttribute("city", $this->getCity());

        empty($this->getStreet()) || $xmlShipping->setAttribute("street", $this->getStreet());

        empty($this->getPhone()) || $xmlShipping->setAttribute("phone", $this->getPhone());

        return $xmlShipping;
    }
}