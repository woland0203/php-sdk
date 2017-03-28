<?php

use CardPay\Communicator\CardPayRefundsReportCommunicator;
use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;

class CardPayRefundsReportCommunicatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode, $restApiLogin, $restApiPassword)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig->setRestApiLogin($restApiLogin);
        $cardPayConfig->setRestApiPassword($restApiPassword);

        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $this->assertInstanceOf(CardPayRefundsReportCommunicator::class,
            $cardPayRefundsReportCommunicator->setConfig($cardPayConfig));

        $this->assertNotEmpty($cardPayConfig->getRefundsReportUrl());
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
                $faker->regexify('[0-9A-Z]{16,16}'),
            ],
            [
                CardPayEndpoint::LIVE,
                $faker->userName,
                $faker->regexify('[0-9A-Z]{16,16}'),
            ],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestUrlSuccessful
     */
    public function testSetRequestUrlSuccessful($request)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $this->assertInstanceOf(CardPayRefundsReportCommunicator::class,
            $cardPayRefundsReportCommunicator->setRequestUrl($request));
    }

    public function dataProviderSetRequestUrlSuccessful()
    {
        return [
            [CardPayEndpoint::$REFUNDS_REPORT_URLS[CardPayEndpoint::TEST]],
            [CardPayEndpoint::$REFUNDS_REPORT_URLS[CardPayEndpoint::LIVE]],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestUrlFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequestUrlFailure($request)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setRequestUrl($request);
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
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $this->assertInstanceOf(CardPayRefundsReportCommunicator::class,
            $cardPayRefundsReportCommunicator->setRequest($request));
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
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setRequest($request);
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
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $this->assertInstanceOf(CardPayRefundsReportCommunicator::class,
            $cardPayRefundsReportCommunicator->setResponse($response));
    }

    public function dataProviderSetResponseSuccessful()
    {
        return [
            ["{\"data\":[{\"id\":\"759201\",\"state\":\"COMPLETED\",\"date\":1490087733000,\"customerId\":null,\"declineReason\":null,\"declineCode\":null,\"authCode\":\"AhlYrq\",\"is3d\":false,\"currency\":\"USD\",\"amount\":100.11,\"refundedAmount\":null,\"description\":\"Test payment description\",\"note\":null,\"email\":\"payment@cardpay.com\",\"rrn\":null,\"originalOrderId\":null,\"number\":\"1490087732\"}],\"hasMore\":false}"],
            ["{\"data\":{\"type\":\"PAYMENTS\",\"id\":\"756529\",\"created\":1489398770347,\"updated\":1489743956140,\"state\":\"REFUNDED\",\"decline\":null,\"rrn\":null,\"merchantOrderId\":\"1489398769\",\"description\":\"Test payment description\"}}"]
        ];

    }

    /**
     * @dataProvider dataProviderSetResponseFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetResponseFailure($response)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setResponse($response);
    }

    public function dataProviderSetResponseFailure()
    {
        return [
            ["{\"data\":{\"id\":\"759201\",\"state\":\"COMPLETED\",\"date\":1490087733000,\"customerId\":null,\"declineReason\":null,\"declineCode\":null,\"authCode\":\"AhlYrq\",\"is3d\":false,\"currency\":\"USD\",\"amount\":100.11,\"refundedAmount\":null,\"description\":\"Test payment description\",\"note\":null,\"email\":\"payment@cardpay.com\",\"rrn\":null,\"originalOrderId\":null,\"number\":\"1490087732\"}],\"hasMore\":false}"],
            [""],
            [null]
        ];

    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testDecodeResponseJsonSuccessful($response)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setResponse($response);

        $this->assertInstanceOf(CardPayRefundsReportCommunicator::class,
            $cardPayRefundsReportCommunicator->decodeResponseJson());
    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testValidateResponseJsonSuccessful($response)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setResponse($response);

        $cardPayRefundsReportCommunicator->decodeResponseJson();

        $this->assertTrue($cardPayRefundsReportCommunicator->validateResponseJson());
    }

    /**
     * @dataProvider dataProviderValidateResponseJsonFailure
     * @expectedException \CardPay\Exception\CardPayResponseException
     */
    public function testValidateResponseJsonFailure($response)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setResponse($response);

        $cardPayRefundsReportCommunicator->decodeResponseJson();

        $cardPayRefundsReportCommunicator->validateResponseJson();
    }

    public function dataProviderValidateResponseJsonFailure()
    {
        return [
            ["{\"nondata\":[{\"id\":\"759201\",\"state\":\"COMPLETED\",\"date\":1490087733000,\"customerId\":null,\"declineReason\":null,\"declineCode\":null,\"authCode\":\"AhlYrq\",\"is3d\":false,\"currency\":\"USD\",\"amount\":100.11,\"refundedAmount\":null,\"description\":\"Test payment description\",\"note\":null,\"email\":\"payment@cardpay.com\",\"rrn\":null,\"originalOrderId\":null,\"number\":\"1490087732\"}],\"hasMore\":false}"],
        ];
    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testGetResponseJsonObjectSuccessful($response)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setResponse($response);

        $cardPayRefundsReportCommunicator->decodeResponseJson();

        $cardPayGatewayModeResponseJsonObject = $cardPayRefundsReportCommunicator->getResponseJsonObject();

        $this->assertNotEmpty($cardPayGatewayModeResponseJsonObject->data);
    }

    /**
     * @dataProvider dataProviderValidateResponseJsonFailure
     */
    public function testGetResponseJsonObjectFailure($response)
    {
        $cardPayRefundsReportCommunicator = new CardPayRefundsReportCommunicator();

        $cardPayRefundsReportCommunicator->setResponse($response);

        $cardPayRefundsReportCommunicator->decodeResponseJson();

        $cardPayGatewayModeResponseJsonObject = $cardPayRefundsReportCommunicator->getResponseJsonObject();

        $this->assertObjectNotHasAttribute("data", $cardPayGatewayModeResponseJsonObject);
    }
}
