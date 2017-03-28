<?php

namespace CardPay\Helper;

class CardPayXmlHelper
{
    public static function getXmlAttributes($xml_object)
    {
        $attributes = array();

        foreach ($xml_object->attributes() as $attr => $value) {
            $attributes[$attr] = $value->__toString();
        }

        return json_decode(json_encode($attributes));
    }
}