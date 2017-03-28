<?php

namespace CardPay\Core;

use CardPay\Helper\CardPayStringHelper;
use CardPay\Validation\CardPayAmountValidator;
use CardPay\Validation\CardPayAttributeValidator;
use CardPay\Validation\CardPayIntegerValidator;
use CardPay\Validation\CardPayStringValidator;

class CardPayItem
{
    private $name;
    private $description;
    private $count;
    private $price;

    public function setName($name)
    {
        CardPayStringValidator::validate($name, "Item Name", 2, 50);

        $this->name = CardPayStringHelper::normalizeString($name);

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }


    public function setDescription($description)
    {
        CardPayStringValidator::validate($description, "Item Description", 2, 200);

        $this->description = CardPayStringHelper::normalizeString($description);

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }


    public function setCount($count)
    {
        CardPayIntegerValidator::validate($count, "Item Count");

        $this->count = $count;

        return $this;
    }

    public function getCount()
    {
        return $this->count;
    }


    public function setPrice($price)
    {
        CardPayAmountValidator::validate($price, "Item Price");

        $this->price = $price;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }


    public function getXML()
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        $xmlItem = $xml->createElement("item");

        CardPayAttributeValidator::validate($this, "name");
        $xmlItem->setAttribute("name", $this->getName());

        empty($this->getDescription()) || $xmlItem->setAttribute("description", $this->getDescription());

        empty($this->getCount()) || $xmlItem->setAttribute("count", $this->getCount());

        empty($this->getPrice()) || $xmlItem->setAttribute("price", $this->getPrice());

        return $xmlItem;
    }
}