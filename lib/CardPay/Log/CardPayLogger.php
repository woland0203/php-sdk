<?php

namespace CardPay\Log;

use CardPay\Exception\CardPayLoggerException;

class CardPayLogger
{
    private static $instance;

    private $filePath;

    private static function getInstance()
    {
        empty(self::$instance) && self::$instance = new self;

        return self::$instance;
    }

    public static function setFilePath($filePath)
    {
        try {
            $isContentWrote = file_put_contents($filePath, "", FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            throw new CardPayLoggerException("Log file is not writable");
        }


        if ($isContentWrote === false) {
            throw new CardPayLoggerException("Log file is not writable");
        }

        self::getInstance()->filePath = $filePath;
    }

    public static function log($data, $trace = null)
    {
        $filePath = self::getInstance()->filePath;

        if (empty($filePath)) {
            throw new CardPayLoggerException("Log file is not set");
        }

        $data = date("Y-m-d H:i:s") . "\n" . print_r($data, true);

        if (!empty($trace)) {
            $data .= "\nin $trace";
        }

        $data .= "\n";

        $isContentWrote = file_put_contents($filePath, $data, FILE_APPEND | LOCK_EX);

        if ($isContentWrote === false) {
            throw new CardPayLoggerException("Log file is not writable");
        }

        return true;
    }
}