<?php

use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;
use CardPay\Core\CardPayPaymentReport;

class CardPayPaymentReportTest extends PHPUnit_Framework_TestCase
{
    private function getConfig()
    {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setRestApiLogin(CardPayTestHelper::REST_API_LOGIN)
            ->setRestApiPassword(CardPayTestHelper::REST_API_PASSWORD)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        return $cardPayConfig;
    }

    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode, $restApiLogin, $restApiPassword, $logFilePath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setRestApiLogin($restApiLogin)
            ->setRestApiPassword($restApiPassword)
            ->setLogFilePath($logFilePath);

        $cardPayPaymentReport = new CardPayPaymentReport();

        $this->assertInstanceOf(CardPayPaymentReport::class, $cardPayPaymentReport->setConfig($cardPayConfig));
    }

    public function dataProviderSetConfigSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                CardPayEndpoint::TEST,
                $faker->userName,
                $faker->regexify('[0-9A-Z]{16,16}'),
                __DIR__ . "/cardpay.log",
            ],
            [
                CardPayEndpoint::LIVE,
                $faker->userName,
                $faker->regexify('[0-9A-Z]{16,16}'),
                __DIR__ . "/cardpay.log",
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetConfigFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetConfigFailure($mode, $restApiLogin, $restApiPassword, $logFilePath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setRestApiLogin($restApiLogin)
            ->setRestApiPassword($restApiPassword);

        if (empty($logFilePath)) {
            $this->expectException(CardPay\Exception\CardPayLoggerException::class);
            $cardPayConfig->setLogFilePath($logFilePath);
        }

        $cardPayPaymentReport = new CardPayPaymentReport();
        $cardPayPaymentReport->setConfig($cardPayConfig);
    }

    public function dataProviderSetConfigFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetConfigSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsSuccessful
     */
    public function testSetRequiredFieldsSuccessful(
        $transactionId
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentReport = new CardPayPaymentReport();
        $cardPayPaymentReport->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayPaymentReport::class,
            $cardPayPaymentReport->setTransactionId($transactionId));
    }

    public function dataProviderSetRequiredFieldsSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->randomNumber(6),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequiredFieldsFailure(
        $transactionId
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentReport = new CardPayPaymentReport();
        $cardPayPaymentReport->setConfig($cardPayConfig);

        $cardPayPaymentReport->setTransactionId($transactionId);
    }

    public function dataProviderSetRequiredFieldsFailure()
    {
        return [
            [""],
            [null],
        ];
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsSuccessful
     */
    public function testGetUrnParamsSuccessful(
        $transactionId
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentReport = new CardPayPaymentReport();
        $cardPayPaymentReport
            ->setConfig($cardPayConfig)
            ->setTransactionId($transactionId);

        $expected = [
            "transactionId" => $transactionId,
        ];

        $this->assertEquals($expected, $cardPayPaymentReport->getQueryUrn());
    }

    /**
     * @dataProvider dataProviderGetQueryParamsFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testGetQueryUrnFailure(
        $transactionId
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentReport = new CardPayPaymentReport();
        $cardPayPaymentReport
            ->setConfig($cardPayConfig)
            ->setTransactionId($transactionId);

        $cardPayPaymentReport->getQueryUrn();
    }

    public function dataProviderGetQueryParamsFailure()
    {
        return [
            [""],
            [null],
        ];
    }
}
