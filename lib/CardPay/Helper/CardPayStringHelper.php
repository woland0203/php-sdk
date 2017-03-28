<?php

namespace CardPay\Helper;

class CardPayStringHelper
{
    public static function subLineBreak($string)
    {
        return preg_replace('/[\n\r]/', " ", $string);
    }

    public static function normalizeString($string)
    {
        $string = self::subLineBreak($string);

        return $string;
    }
}