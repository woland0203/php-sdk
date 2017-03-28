<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayIntegerValidator;

trait CardPayDeclineCodeAttribute
{
    static $DECLINE_CODE_DESC = [
        1 => "System malfunction",
        2 => "Cancelled by customer",
        3 => "Declined by Antifraud",
        4 => "Declined by 3-D Secure",
        5 => "Only 3-D Secure transactions are allowed",
        6 => "3-D Secure availability is unknown",
        7 => "Limit reached",
        10 => "Declined by bank (reason not specified)",
        11 => "Common decline by bank",
        12 => "Invalid 3-D Secure response",
        13 => "Insufficient funds",
        14 => "Card limit reached",
        15 => "Incorrect card data",
        16 => "Declined by bank’s antifraud",
        17 => "Bank’s malfunction",
        18 => "Connection problem",
    ];

    private $declineCode;

    public function setDeclineCode($declineCode)
    {
        CardPayIntegerValidator::validate($declineCode, "Decline code", false);

        $this->declineCode = intval($declineCode);

        return $this;
    }

    public function getDeclineCode()
    {
        return $this->declineCode;
    }

    public function getDeclineCodeDesc()
    {
        if(isset(self::$DECLINE_CODE_DESC[$this->declineCode])){
            return self::$DECLINE_CODE_DESC[$this->declineCode];
        }

        return null;
    }
}