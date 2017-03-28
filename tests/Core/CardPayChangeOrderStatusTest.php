<?php

use CardPay\Attribute\CardPayStatusToAttribute;
use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;
use CardPay\Core\CardPayChangeOrderStatus;

class CardPayChangeOrderStatusTest extends PHPUnit_Framework_TestCase
{
    private function getConfig(){
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setClientLogin(CardPayTestHelper::CLIENT_LOGIN)
            ->setClientPasswordSHA256(CardPayTestHelper::CLIENT_PASSWORD_SHA256)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        return $cardPayConfig;
    }

    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode, $client_login, $client_password_sha256, $logFilePath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setClientLogin($client_login)
            ->setClientPasswordSHA256($client_password_sha256)
            ->setLogFilePath($logFilePath);

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();

        $this->assertInstanceOf(CardPayChangeOrderStatus::class, $cardPayChangeOrderStatus->setConfig($cardPayConfig));
    }

    public function dataProviderSetConfigSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                CardPayEndpoint::TEST,
                $faker->userName,
                $faker->regexify('[0-9A-Z]{64,64}'),
                __DIR__ . "/cardpay.log",
            ],
            [
                CardPayEndpoint::LIVE,
                $faker->userName,
                $faker->regexify('[0-9A-Z]{64,64}'),
                __DIR__ . "/cardpay.log",
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetConfigFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetConfigFailure($mode, $client_login, $client_password_sha256, $logFilePath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setClientLogin($client_login)
            ->setClientPasswordSHA256($client_password_sha256);

        if(empty($logFilePath)){
            $this->expectException(CardPay\Exception\CardPayLoggerException::class);
            $cardPayConfig->setLogFilePath($logFilePath);
        }

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus->setConfig($cardPayConfig);
    }

    public function dataProviderSetConfigFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetConfigSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsSuccessful
     */
    public function testSetRequiredFieldsSuccessful(
        $order_id,
        $status_to
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayChangeOrderStatus::class, $cardPayChangeOrderStatus->setOrderId($order_id));
        $this->assertInstanceOf(CardPayChangeOrderStatus::class, $cardPayChangeOrderStatus->setStatusTo($status_to));
    }

    public function dataProviderSetRequiredFieldsSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                CardPayStatusToAttribute::$STATUS_CAPTURE,
            ],
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                CardPayStatusToAttribute::$STATUS_VOID,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequiredFieldsFailure(
        $order_id,
        $status_to
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus->setConfig($cardPayConfig);

        $cardPayChangeOrderStatus->setOrderId($order_id);
        $cardPayChangeOrderStatus->setStatusTo($status_to);
    }

    public function dataProviderSetRequiredFieldsFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetRequiredFieldsSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsInRefundCaseSuccessful
     */
    public function testSetRequiredFieldsInRefundCaseSuccessful(
        $order_id,
        $reason
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayChangeOrderStatus::class, $cardPayChangeOrderStatus->setOrderId($order_id));
        $this->assertInstanceOf(CardPayChangeOrderStatus::class,
            $cardPayChangeOrderStatus->setStatusTo(CardPayStatusToAttribute::$STATUS_REFUND));
        $this->assertInstanceOf(CardPayChangeOrderStatus::class, $cardPayChangeOrderStatus->setRefundReason($reason));
    }

    public function dataProviderSetRequiredFieldsInRefundCaseSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                $faker->text(2048)
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsInRefundCaseFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequiredFieldsInRefundCaseFailure(
        $order_id,
        $reason
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus->setConfig($cardPayConfig);

        $cardPayChangeOrderStatus->setOrderId($order_id);
        $cardPayChangeOrderStatus->setStatusTo(CardPayStatusToAttribute::$STATUS_REFUND);
        $cardPayChangeOrderStatus->setRefundReason($reason);
    }

    public function dataProviderSetRequiredFieldsInRefundCaseFailure()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                null
            ],
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                ""
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetNonRequiredFieldsInRefundCaseSuccessful
     */
    public function testSetNonRequiredFieldsInRefundCaseSuccessful(
        $amount
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus->setConfig($cardPayConfig);

        $cardPayChangeOrderStatus->setStatusTo(CardPayStatusToAttribute::$STATUS_REFUND);

        $this->assertInstanceOf(CardPayChangeOrderStatus::class,
            $cardPayChangeOrderStatus->setRefundAmount($amount));
    }

    public function dataProviderSetNonRequiredFieldsInRefundCaseSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->randomFloat(2, 0.01, 999999.99),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetNonRequiredFieldsInRefundCaseFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetNonRequiredFieldsInRefundCaseFailure(
        $amount
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus->setConfig($cardPayConfig);

        $cardPayChangeOrderStatus->setStatusTo(CardPayStatusToAttribute::$STATUS_REFUND);

        $cardPayChangeOrderStatus->setRefundAmount($amount);
    }

    public function dataProviderSetNonRequiredFieldsInRefundCaseFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetNonRequiredFieldsInRefundCaseSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderGetQueryParamsSuccessful
     */
    public function testGetQueryParamsSuccessful(
        $order_id,
        $status_to,
        $amount,
        $reason
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();
        $cardPayChangeOrderStatus
            ->setConfig($cardPayConfig)
            ->setOrderId($order_id)
            ->setStatusTo($status_to)
            ->setRefundAmount($amount)
            ->setRefundReason($reason);

        $expected = [
            "client_login" => CardPayTestHelper::CLIENT_LOGIN,
            "client_password" => CardPayTestHelper::CLIENT_PASSWORD_SHA256,
            "id" => $order_id,
            "status_to" => $status_to,
        ];

        if($status_to == CardPayStatusToAttribute::$STATUS_REFUND){
            $expected["amount"] = $amount;
            $expected["reason"] = CardPayTestHelper::normalizeString($reason);
        }

        $this->assertEquals($expected, $cardPayChangeOrderStatus->getQueryParams());
    }

    public function dataProviderGetQueryParamsSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                CardPayStatusToAttribute::$STATUS_CAPTURE,
                $faker->randomFloat(2, 0.01, 999999.99),
                $faker->text(2048),
            ],
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                CardPayStatusToAttribute::$STATUS_VOID,
                $faker->randomFloat(2, 0.01, 999999.99),
                $faker->text(2048),
            ],
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                CardPayStatusToAttribute::$STATUS_REFUND,
                $faker->randomFloat(2, 0.01, 999999.99),
                $faker->text(2048),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetQueryParamsFailure
     * @expectedException \CardPay\Exception\CardPayAttributeException
     */
    public function testGetQueryParamsFailure(
        $order_id,
        $status_to,
        $amount,
        $reason
    ) {
        $cardPayConfig = $this->getConfig();

        $cardPayChangeOrderStatus = new CardPayChangeOrderStatus();

        $cardPayChangeOrderStatus
            ->setConfig($cardPayConfig);

        empty($order_id) || $cardPayChangeOrderStatus->setOrderId($order_id);
        empty($status_to) || $cardPayChangeOrderStatus->setStatusTo($status_to);
        empty($amount) || $cardPayChangeOrderStatus->setRefundAmount($amount);
        empty($reason) || $cardPayChangeOrderStatus->setRefundReason($reason);

        $cardPayChangeOrderStatus->getQueryParams();
    }

    public function dataProviderGetQueryParamsFailure()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                null,
                CardPayStatusToAttribute::$STATUS_CAPTURE,
                $faker->randomFloat(2, 0.01, 999999.99),
                $faker->text(2048),
            ],
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                null,
                $faker->randomFloat(2, 0.01, 999999.99),
                $faker->text(2048),
            ],
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                CardPayStatusToAttribute::$STATUS_REFUND,
                $faker->randomFloat(2, 0.01, 999999.99),
                null,
            ],
        ];
    }
}
