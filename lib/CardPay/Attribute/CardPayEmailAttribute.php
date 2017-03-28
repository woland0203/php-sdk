<?php

namespace CardPay\Attribute;

use CardPay\Validation\CardPayEmailValidator;

trait CardPayEmailAttribute
{
    private $email;

    public function setEmail($email)
    {
        CardPayEmailValidator::validate($email, "Email");

        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }
}