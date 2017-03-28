<?php

use CardPay\Communicator\CardPayPaymentsReportCommunicator;
use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;

class CardPayPaymentsReportCommunicatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode, $restApiLogin, $restApiPassword)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig->setRestApiLogin($restApiLogin);
        $cardPayConfig->setRestApiPassword($restApiPassword);

        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $this->assertInstanceOf(CardPayPaymentsReportCommunicator::class,
            $cardPayPaymentsReportCommunicator->setConfig($cardPayConfig));

        $this->assertNotEmpty($cardPayConfig->getPaymentsReportUrl());
        $this->assertNotEmpty($cardPayConfig->getRestApiLogin());
        $this->assertNotEmpty($cardPayConfig->getRestApiPassword());
    }

    public function dataProviderSetConfigSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                CardPayEndpoint::TEST,
                $faker->userName,
                $faker->regexify('[0-9A-Z]{64,64}'),
            ],
            [
                CardPayEndpoint::LIVE,
                $faker->userName,
                $faker->regexify('[0-9A-Z]{64,64}'),
            ],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestUrlSuccessful
     */
    public function testSetRequestUrlSuccessful($request)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $this->assertInstanceOf(CardPayPaymentsReportCommunicator::class,
            $cardPayPaymentsReportCommunicator->setRequestUrl($request));
    }

    public function dataProviderSetRequestUrlSuccessful()
    {
        return [
            [CardPayEndpoint::$PAYMENTS_REPORT_URLS[CardPayEndpoint::TEST]],
            [CardPayEndpoint::$PAYMENTS_REPORT_URLS[CardPayEndpoint::LIVE]],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestUrlFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequestUrlFailure($request)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setRequestUrl($request);
    }

    public function dataProviderSetRequestUrlFailure()
    {
        return [
            [""],
            [null],
        ];
    }


    /**
     * @dataProvider dataProviderSetRequestSuccessful
     */
    public function testSetRequestSuccessful($request)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $this->assertInstanceOf(CardPayPaymentsReportCommunicator::class,
            $cardPayPaymentsReportCommunicator->setRequest($request));
    }

    public function dataProviderSetRequestSuccessful()
    {
        return [
            ["qwerty"],
            [http_build_query(["a" => "b"])],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequestFailure($request)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setRequest($request);
    }

    public function dataProviderSetRequestFailure()
    {
        return [
            [""],
            [null],
        ];
    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testSetResponseSuccessful($response)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $this->assertInstanceOf(CardPayPaymentsReportCommunicator::class,
            $cardPayPaymentsReportCommunicator->setResponse($response));
    }

    public function dataProviderSetResponseSuccessful()
    {
        return [
            ["{\"data\":[{\"id\":\"758365\",\"state\":\"COMPLETED\",\"date\":1489743956000,\"customerId\":null,\"declineReason\":null,\"declineCode\":null,\"authCode\":null,\"is3d\":false,\"currency\":\"USD\",\"amount\":100.11,\"refundedAmount\":null,\"description\":\"Out of stock\",\"note\":null,\"email\":\"payment@cardpay.com\",\"rrn\":null,\"originalOrderId\":\"756529\",\"number\":\"1489398769\"}],\"hasMore\":false}"],
            ["{\"data\":{\"type\":\"REFUNDS\",\"id\":\"758365\",\"created\":1489743956106,\"updated\":1489743956141,\"state\":\"COMPLETED\",\"decline\":null,\"rrn\":null,\"merchantOrderId\":\"1489398769\",\"description\":\"Out of stock\"}}"]
        ];

    }

    /**
     * @dataProvider dataProviderSetResponseFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetResponseFailure($response)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setResponse($response);
    }

    public function dataProviderSetResponseFailure()
    {
        return [
            ["{\"data\":{\"id\":\"758365\",\"state\":\"COMPLETED\",\"date\":1489743956000,\"customerId\":null,\"declineReason\":null,\"declineCode\":null,\"authCode\":null,\"is3d\":false,\"currency\":\"USD\",\"amount\":100.11,\"refundedAmount\":null,\"description\":\"Out of stock\",\"note\":null,\"email\":\"payment@cardpay.com\",\"rrn\":null,\"originalOrderId\":\"756529\",\"number\":\"1489398769\"}],\"hasMore\":false}"],
            [""],
            [null]
        ];

    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testDecodeResponseJsonSuccessful($response)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setResponse($response);

        $this->assertInstanceOf(CardPayPaymentsReportCommunicator::class,
            $cardPayPaymentsReportCommunicator->decodeResponseJson());
    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testValidateResponseJsonSuccessful($response)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setResponse($response);

        $cardPayPaymentsReportCommunicator->decodeResponseJson();

        $this->assertTrue($cardPayPaymentsReportCommunicator->validateResponseJson());
    }

    /**
     * @dataProvider dataProviderValidateResponseJsonFailure
     * @expectedException \CardPay\Exception\CardPayResponseException
     */
    public function testValidateResponseJsonFailure($response)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setResponse($response);

        $cardPayPaymentsReportCommunicator->decodeResponseJson();

        $cardPayPaymentsReportCommunicator->validateResponseJson();
    }

    public function dataProviderValidateResponseJsonFailure()
    {
        return [
            ["{\"nondata\":[{\"id\":\"758365\",\"state\":\"COMPLETED\",\"date\":1489743956000,\"customerId\":null,\"declineReason\":null,\"declineCode\":null,\"authCode\":null,\"is3d\":false,\"currency\":\"USD\",\"amount\":100.11,\"refundedAmount\":null,\"description\":\"Out of stock\",\"note\":null,\"email\":\"payment@cardpay.com\",\"rrn\":null,\"originalOrderId\":\"756529\",\"number\":\"1489398769\"}],\"hasMore\":false}"],
        ];
    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testGetResponseJsonObjectSuccessful($response)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setResponse($response);

        $cardPayPaymentsReportCommunicator->decodeResponseJson();

        $cardPayGatewayModeResponseJsonObject = $cardPayPaymentsReportCommunicator->getResponseJsonObject();

        $this->assertNotEmpty($cardPayGatewayModeResponseJsonObject->data);
    }

    /**
     * @dataProvider dataProviderValidateResponseJsonFailure
     */
    public function testGetResponseJsonObjectFailure($response)
    {
        $cardPayPaymentsReportCommunicator = new CardPayPaymentsReportCommunicator();

        $cardPayPaymentsReportCommunicator->setResponse($response);

        $cardPayPaymentsReportCommunicator->decodeResponseJson();

        $cardPayGatewayModeResponseJsonObject = $cardPayPaymentsReportCommunicator->getResponseJsonObject();

        $this->assertObjectNotHasAttribute("data", $cardPayGatewayModeResponseJsonObject);
    }
}
