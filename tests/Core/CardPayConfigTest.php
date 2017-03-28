<?php

use \CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;

class CardPayConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderCreateObjectSuccessful
     */
    public function testCreateObjectSuccessful($mode)
    {
        $this->assertInstanceOf(CardPayConfig::class, new CardPayConfig($mode));
    }

    public function dataProviderCreateObjectSuccessful()
    {
        return [
            [0],
            [1],
        ];
    }

    /**
     * @dataProvider dataProviderCreateObjectFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testCreateObjectFailure($mode)
    {
        $this->assertInstanceOf(CardPayConfig::class, new CardPayConfig($mode));
    }

    public function dataProviderCreateObjectFailure()
    {
        return [
            [2],
        ];
    }

    /**
     * @dataProvider dataProviderExistingGatewayUrlSuccessful
     */
    public function testExistingGatewayUrlSuccessful($mode)
    {
        $this->assertTrue(isset(CardPayEndpoint::$GATEWAY_URLS[$mode]));
    }

    public function dataProviderExistingGatewayUrlSuccessful()
    {
        return [
            [CardPayEndpoint::TEST],
            [CardPayEndpoint::LIVE],
        ];
    }


    /**
     * @dataProvider dataProviderExistingGatewayUrlFailure
     */
    public function testExistingGatewayUrlFailure($mode_key)
    {
        $this->assertFalse(isset(CardPayEndpoint::$GATEWAY_URLS[$mode_key]));
    }

    public function dataProviderExistingGatewayUrlFailure()
    {
        return [
            [2],
        ];
    }

    /**
     * @dataProvider dataProviderSetGatewayUrlSuccess
     */
    public function testSetGatewayUrlSuccess($url)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setGatewayUrl($url));
    }

    public function dataProviderSetGatewayUrlSuccess()
    {
        return [
            [CardPayEndpoint::$GATEWAY_URLS[CardPayEndpoint::TEST]],
            [CardPayEndpoint::$GATEWAY_URLS[CardPayEndpoint::LIVE]],
        ];
    }

    /**
     * @dataProvider dataProviderSetGatewayUrlFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetGatewayUrlFailure($url)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setGatewayUrl($url);
    }

    public function dataProviderSetGatewayUrlFailure()
    {
        return [
            [""],
            ["wrong-url"],
            ["ftp://wrong-url.com"],
        ];
    }

    /**
     * @dataProvider dataProviderSetChangeOrderStatusUrlSuccess
     */
    public function testSetChangeOrderStatusSuccess($url)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setChangeOrderStatusUrl($url));
    }

    public function dataProviderSetChangeOrderStatusUrlSuccess()
    {
        return [
            [CardPayEndpoint::$CHANGE_ORDER_STATUS_URLS[CardPayEndpoint::TEST]],
            [CardPayEndpoint::$CHANGE_ORDER_STATUS_URLS[CardPayEndpoint::LIVE]],
        ];
    }

    /**
     * @dataProvider dataProviderSetGatewayUrlFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetChangeOrderStatusUrlFailure($url)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setGatewayUrl($url);
    }

    /**
     * @dataProvider dataProviderSetPaymentsReportUrlSuccess
     */
    public function testSetPaymentsReportSuccess($url)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setPaymentsReportUrl($url));
    }

    public function dataProviderSetPaymentsReportUrlSuccess()
    {
        return [
            [CardPayEndpoint::$PAYMENTS_REPORT_URLS[CardPayEndpoint::TEST]],
            [CardPayEndpoint::$PAYMENTS_REPORT_URLS[CardPayEndpoint::LIVE]],
        ];
    }

    /**
     * @dataProvider dataProviderSetGatewayUrlFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetPaymentsReportUrlFailure($url)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setGatewayUrl($url);
    }

    /**
     * @dataProvider dataProviderSetRefundsReportUrlSuccess
     */
    public function testSetRefundsReportSuccess($url)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setRefundsReportUrl($url));
    }

    public function dataProviderSetRefundsReportUrlSuccess()
    {
        return [
            [CardPayEndpoint::$REFUNDS_REPORT_URLS[CardPayEndpoint::TEST]],
            [CardPayEndpoint::$REFUNDS_REPORT_URLS[CardPayEndpoint::LIVE]],
        ];
    }

    /**
     * @dataProvider dataProviderSetGatewayUrlFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRefundsReportUrlFailure($url)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setGatewayUrl($url);
    }

    /**
     * @dataProvider dataProviderSetWalletIdSuccess
     */
    public function testSetWalletIdSuccess($wallet_id)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setWalletId($wallet_id));
    }

    public function dataProviderSetWalletIdSuccess()
    {
        return [
            [1],
            [12345],
            [1234556789],
        ];
    }

    /**
     * @dataProvider dataProviderSetWalletIdFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetWalletIdFailure($wallet_id)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setWalletId($wallet_id);
    }

    public function dataProviderSetWalletIdFailure()
    {
        return [
            [""],
            [0],
            ["string"],
        ];
    }

    /**
     * @dataProvider dataProviderSetSecretKeySuccess
     */
    public function testSetSecretKeySuccess($secret_key)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setSecretKey($secret_key));
    }

    public function dataProviderSetSecretKeySuccess()
    {
        return [
            ["merchant_secret_key"],
            ["$^#gf$$*hHH23"],
            ["123456789012"],
        ];
    }

    /**
     * @dataProvider dataProviderSetSecretKeyFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetSecretKeyFailure($secret_key)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setSecretKey($secret_key);
    }

    public function dataProviderSetSecretKeyFailure()
    {
        return [
            [""],
            [123456789012],
            ["1234567890"],
        ];
    }

    /**
     * @dataProvider dataProviderSetClientLoginSuccess
     */
    public function testSetClientLoginSuccess($client_login)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setClientLogin($client_login));
    }

    public function dataProviderSetClientLoginSuccess()
    {
        return [
            ["test@cardpay.com"],
            ["testtest"],
        ];
    }

    /**
     * @dataProvider dataProviderSetClientLoginFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetClientLoginFailure($client_login)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setClientLogin($client_login);
    }

    public function dataProviderSetClientLoginFailure()
    {
        return [
            [""],
            [null],
            [123456789012],
        ];
    }

    /**
     * @dataProvider dataProviderSetClientPasswordSHA256Success
     */
    public function testSetClientPasswordSHA256Success($clientPasswordSHA256)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setClientPasswordSHA256($clientPasswordSHA256));
    }

    public function dataProviderSetClientPasswordSHA256Success()
    {
        return [
            ["9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08"],
            ["37268335dd6931045bdcdf92623ff819a64244b53d0e746d438797349d4da578"],
        ];
    }

    /**
     * @dataProvider dataProviderSetClientPasswordSHA256Failure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetClientPasswordSHA256Failure($clientPasswordSHA256)
    {
        $CardPayConfig = new CardPayConfig();

        $CardPayConfig->setClientPasswordSHA256($clientPasswordSHA256);
    }

    public function dataProviderSetClientPasswordSHA256Failure()
    {
        return [
            [""],
            [null],
            [123456789012],
            ["6abaaee568d9c844e3c54cc01e9ddb62464384f7a9961c5f47e009acad52c2"],
            ["6abaaee568d9c844e3c54cc01e9ddb62464384f7a9961c5f47e009acad52c23df4f"],
        ];
    }

    /**
     * @dataProvider dataProviderSetRestApiLoginSuccess
     */
    public function testSetRestApiLoginSuccess($restApiLogin)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setRestApiLogin($restApiLogin));
    }

    public function dataProviderSetRestApiLoginSuccess()
    {
        return [
            ["test@cardpay.com"],
            ["testtest"],
        ];
    }

    /**
     * @dataProvider dataProviderSetRestApiLoginFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRestApiLoginFailure($restApiLogin)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setRestApiLogin($restApiLogin);
    }

    public function dataProviderSetRestApiLoginFailure()
    {
        return [
            [""],
            [null],
            [123456789012],
        ];
    }

    /**
     * @dataProvider dataProviderSetRestApiPasswordSuccess
     */
    public function testSetRestApiPasswordSuccess($restApiPassword)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setRestApiPassword($restApiPassword));
    }

    public function dataProviderSetRestApiPasswordSuccess()
    {
        return [
            ["9f86d081884"],
            ["37268335dd6931045"],
        ];
    }

    /**
     * @dataProvider dataProviderSetRestApiPasswordFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRestApiPasswordFailure($restApiPassword)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setRestApiPassword($restApiPassword);
    }

    public function dataProviderSetRestApiPasswordFailure()
    {
        return [
            [""],
            [null],
            [123456789012],
        ];
    }

    /**
     * @dataProvider dataProviderSetLogFilepathSuccess
     */
    public function testSetLogFilepathSuccess($filepath)
    {
        $cardPayConfig = new CardPayConfig();

        $this->assertInstanceOf(CardPayConfig::class, $cardPayConfig->setLogFilePath($filepath));

        unlink($filepath);
    }

    public function dataProviderSetLogFilepathSuccess()
    {
        return [
            [__DIR__ . "/_cardpay.log"],
        ];
    }

    /**
     * @dataProvider dataProviderSetLogFilepathFailure
     * @expectedException \CardPay\Exception\CardPayLoggerException
     */
    public function testSetLogFilepathFailure($filepath)
    {
        $cardPayConfig = new CardPayConfig();

        $cardPayConfig->setLogFilePath($filepath);
    }

    public function dataProviderSetLogFilepathFailure()
    {
        return [
            [__DIR__ . "/"],
            [__DIR__ . "/tmp/"],
            [__DIR__ . "/tmp/path/to/logs/cardpay.log"],
        ];
    }
}
