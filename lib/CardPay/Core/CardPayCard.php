<?php

namespace CardPay\Core;

use CardPay\Validation\CardPayAttributeValidator;
use CardPay\Validation\CardPayStringValidator;

class CardPayCard
{
    private $cardNumber;
    private $cardholderName;
    private $expirationDate;
    private $cvc;

    public function setCardNumber($cardNumber)
    {
        CardPayStringValidator::validate($cardNumber, "Card Number", 13, 19);

        $this->cardNumber = $cardNumber;

        return $this;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }


    public function setCardholderName($cardholderName)
    {
        CardPayStringValidator::validate($cardholderName, "Cardholder Name", 1, 50);

        $this->cardholderName = $cardholderName;

        return $this;
    }

    public function getCardholderName()
    {
        return $this->cardholderName;
    }


    public function setExpirationDate($expirationDateYear, $expirationDateMonth)
    {
        CardPayStringValidator::validate($expirationDateYear, "Expiration Date Year", 2, 4);
        CardPayStringValidator::validate($expirationDateMonth, "Expiration Date Month", 1, 2);

        $this->expirationDate = implode("/", [
            substr("0" . $expirationDateMonth, -2),
            substr("20" . $expirationDateYear, -4)
        ]);

        return $this;
    }

    public function getExpirationDate()
    {
        return $this->expirationDate;
    }


    public function setCvc($cvc)
    {
        CardPayStringValidator::validate($cvc, "CVC", 3, 3);

        $this->cvc = $cvc;

        return $this;
    }

    public function getCvc()
    {
        return $this->cvc;
    }


    public function getXML()
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        $xmlCard = $xml->createElement("card");

        CardPayAttributeValidator::validate($this, "cardNumber");
        $xmlCard->setAttribute("num", $this->getCardNumber());

        CardPayAttributeValidator::validate($this, "cardholderName");
        $xmlCard->setAttribute("holder", $this->getCardholderName());

        CardPayAttributeValidator::validate($this, "expirationDate");
        $xmlCard->setAttribute("expires", $this->getExpirationDate());

        CardPayAttributeValidator::validate($this, "cvc");
        $xmlCard->setAttribute("cvv", $this->getCvc());

        return $xmlCard;
    }
}