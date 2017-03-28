<?php

use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;
use CardPay\Core\CardPayPaymentsReport;

class CardPayPaymentsReportTest extends PHPUnit_Framework_TestCase
{
    private function getConfig()
    {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setRestApiLogin(CardPayTestHelper::REST_API_LOGIN)
            ->setRestApiPassword(CardPayTestHelper::REST_API_PASSWORD)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        return $cardPayConfig;
    }

    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode, $walletId, $restApiLogin, $restApiPassword, $logFilePath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setWalletId($walletId)
            ->setRestApiLogin($restApiLogin)
            ->setRestApiPassword($restApiPassword)
            ->setLogFilePath($logFilePath);

        $cardPayPaymentsReport = new CardPayPaymentsReport();

        $this->assertInstanceOf(CardPayPaymentsReport::class, $cardPayPaymentsReport->setConfig($cardPayConfig));
    }

    public function dataProviderSetConfigSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                CardPayEndpoint::TEST,
                $faker->randomNumber(6),
                $faker->userName,
                $faker->regexify('[0-9A-Z]{16,16}'),
                __DIR__ . "/cardpay.log",
            ],
            [
                CardPayEndpoint::LIVE,
                $faker->randomNumber(6),
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
    public function testSetConfigFailure($mode, $walletId, $restApiLogin, $restApiPassword, $logFilePath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setWalletId($walletId)
            ->setRestApiLogin($restApiLogin)
            ->setRestApiPassword($restApiPassword);

        if (empty($logFilePath)) {
            $this->expectException(CardPay\Exception\CardPayLoggerException::class);
            $cardPayConfig->setLogFilePath($logFilePath);
        }

        $cardPayPaymentsReport = new CardPayPaymentsReport();
        $cardPayPaymentsReport->setConfig($cardPayConfig);
    }

    public function dataProviderSetConfigFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetConfigSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderSetNonRequiredFieldsSuccessful
     */
    public function testSetNonRequiredFieldsSuccessful(
        $startTimestampMilliseconds,
        $endTimestampMilliseconds,
        $orderId,
        $maxCount
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentsReport = new CardPayPaymentsReport();
        $cardPayPaymentsReport->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayPaymentsReport::class,
            $cardPayPaymentsReport->setStartTimestampMilliseconds($startTimestampMilliseconds));
        $this->assertInstanceOf(CardPayPaymentsReport::class,
            $cardPayPaymentsReport->setEndTimestampMilliseconds($endTimestampMilliseconds));
        $this->assertInstanceOf(CardPayPaymentsReport::class,
            $cardPayPaymentsReport->setOrderId($orderId));
        $this->assertInstanceOf(CardPayPaymentsReport::class,
            $cardPayPaymentsReport->setMaxCount($maxCount));
    }

    public function dataProviderSetNonRequiredFieldsSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                strtotime("-7 days") . "000",
                time() . "000",
                $faker->regexify('[0-9A-Z]{2,20}'),
                $faker->randomElement([1, 500, 10000]),
            ],
            [
                strtotime("-14 days") . "000",
                strtotime("-7 days") . "000",
                $faker->regexify('[0-9A-Z]{2,20}'),
                $faker->randomElement([1, 500, 10000]),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetNonRequiredFieldsFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetNonRequiredFieldsFailure(
        $startTimestampMilliseconds,
        $endTimestampMilliseconds,
        $orderId,
        $maxCount
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentsReport = new CardPayPaymentsReport();
        $cardPayPaymentsReport->setConfig($cardPayConfig);

        $cardPayPaymentsReport->setStartTimestampMilliseconds($startTimestampMilliseconds);
        $cardPayPaymentsReport->setEndTimestampMilliseconds($endTimestampMilliseconds);
        $cardPayPaymentsReport->setOrderId($orderId);
        $cardPayPaymentsReport->setMaxCount($maxCount);
    }

    public function dataProviderSetNonRequiredFieldsFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetNonRequiredFieldsSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderSetNonRequiredFieldsSuccessful
     */
    public function testGetQueryParamsSuccessful(
        $startTimestampMilliseconds,
        $endTimestampMilliseconds,
        $orderId,
        $maxCount
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentsReport = new CardPayPaymentsReport();
        $cardPayPaymentsReport
            ->setConfig($cardPayConfig)
            ->setStartTimestampMilliseconds($startTimestampMilliseconds)
            ->setEndTimestampMilliseconds($endTimestampMilliseconds)
            ->setOrderId($orderId)
            ->setMaxCount($maxCount);

        $expected = [
            "walletId" => CardPayTestHelper::WALLET_ID,
            "startMillis" => $startTimestampMilliseconds,
            "endMillis" => $endTimestampMilliseconds,
            "number" => $orderId,
            "maxCount" => $maxCount,
        ];

        $this->assertEquals($expected, $cardPayPaymentsReport->getQueryParams());
    }

    /**
     * @dataProvider dataProviderGetQueryParamsFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testGetQueryParamsFailure(
        $startTimestampMilliseconds,
        $endTimestampMilliseconds
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayPaymentsReport = new CardPayPaymentsReport();
        $cardPayPaymentsReport
            ->setConfig($cardPayConfig)
            ->setStartTimestampMilliseconds($startTimestampMilliseconds)
            ->setEndTimestampMilliseconds($endTimestampMilliseconds);

        $cardPayPaymentsReport->getQueryParams();
    }

    public function dataProviderGetQueryParamsFailure()
    {
        return [
            [
                strtotime("-8 days") . "000",
                time() . "000",
            ],
            [
                strtotime("-24 days") . "000",
                strtotime("-7 days") . "000",
            ],
        ];
    }
}
