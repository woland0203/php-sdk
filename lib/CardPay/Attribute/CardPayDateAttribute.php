<?php

namespace CardPay\Attribute;


use CardPay\Validation\CardPayDatetimeValidator;

trait CardPayDateAttribute
{
    private $date;

    public function setDate($date)
    {
        CardPayDatetimeValidator::validate($date, "Date");

        $this->date = strtotime($date);

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }
}