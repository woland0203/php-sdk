<?php

use CardPay\Core\CardPayEndpoint;

class CardPayTestHelper
{
    const MODE = CardPayEndpoint::TEST;
    const WALLET_ID = 1000;
    const SECRET_KEY = "testSecretKey";
    const CLIENT_LOGIN = "test@cardpay.com";
    const CLIENT_PASSWORD_SHA256 = "9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08";
    const REST_API_LOGIN = "test@cardpay.com";
    const REST_API_PASSWORD = "testSecretPassword";
    const LOG_FILEPATH = __DIR__ . "/cardpay.log";

    public static function generateDataForFailureTests(array $haystack)
    {
        $fill_null = true;

        foreach ($haystack as $value) {
            if (is_array($value)) {
                $fill_null = false;
            }
        }

        $result = $fill_null
            ? [
                array_map(function ($value) {
                    return is_array($value)
                        ? $value
                        : 0;
                }, $haystack),
                array_map(function ($value) {
                    return is_array($value)
                        ? $value
                        : "";
                }, $haystack),
            ]
            : array();

        $keys = array_keys($haystack);

        for ($i = 0; $i < count($haystack); $i++) {
            $new = $haystack;

            if (!is_array($new[$keys[$i]])) {
                $new[$keys[$i]] = "";
                $result[] = $new;
                continue;
            }

            $sub = self::generateDataForFailureTests($new[$keys[$i]]);

            foreach ($sub as $value) {
                $result[] = array_replace($new, [$keys[$i] => $value]);
            }
        }

        return $result;
    }

    public static function normalizeString($string)
    {
        return preg_replace('/[\n\r]/', " ", $string);
    }
}
