<?php

use CardPay\Communicator\CardPayChangeOrderStatusCommunicator;
use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;

class CardPayChangeOrderStatusCommunicatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode)
    {
        $cardPayConfig = new CardPayConfig($mode);

        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $this->assertInstanceOf(CardPayChangeOrderStatusCommunicator::class,
            $cardPayChangeOrderStatusCommunicator->setConfig($cardPayConfig));

         $this->assertNotEmpty($cardPayConfig->getChangeOrderStatusUrl());
    }

    public function dataProviderSetConfigSuccessful()
    {
        return [
            [
                CardPayEndpoint::TEST,
            ],
            [
                CardPayEndpoint::LIVE,
            ],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestSuccessful
     */
    public function testSetRequestSuccessful($request)
    {
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $this->assertInstanceOf(CardPayChangeOrderStatusCommunicator::class,
            $cardPayChangeOrderStatusCommunicator->setRequest($request));
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
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $cardPayChangeOrderStatusCommunicator->setRequest($request);
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
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $this->assertInstanceOf(CardPayChangeOrderStatusCommunicator::class,
            $cardPayChangeOrderStatusCommunicator->setResponse($response));
    }

    public function dataProviderSetResponseSuccessful()
    {
        return [
            ["<redirect url=\"https://sandbox.cardpay.com/MI/payments/redirect?token=Ge3Ff38C07Dheg42D82gHG6H\" />"],
            ["<order id=\"-\" number=\"1487682864\" status=\"DECLINED\" description=\"Mandatory field is missing: card/cvv\" date=\"21.02.2017 13:14:25\" is_3d=\"false\" decline_code=\"02\" decline_reason=\"Cancelled by customer\" amount=\"100.11\" currency=\"USD\" />"]
        ];

    }

    /**
     * @dataProvider dataProviderSetResponseFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetResponseFailure($response)
    {
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $cardPayChangeOrderStatusCommunicator->setResponse($response);
    }

    public function dataProviderSetResponseFailure()
    {
        return [
            ["redirect url=\"https://sandbox.cardpay.com/MI/payments/redirect?token=Ge3Ff38C07Dheg42D82gHG6H\" />"],
            ["<order id=\"-\" number=\"1487682864\" status=\"DECLINED\" description=\"Mandatory field is missing: card/cvv\" date=\"21.02.2017 13:14:25\" is_3d=\"false\" decline_code=\"02\" decline_reason=\"Cancelled by customer\" amount=\"100.11\" currency=\"USD\" /"],
            [""],
            [null]
        ];

    }

    /**
     * @dataProvider dataProviderSetResponseSuccessful
     */
    public function testDecodeResponseXmlSuccessful($response)
    {
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $cardPayChangeOrderStatusCommunicator->setResponse($response);

        $this->assertInstanceOf(CardPayChangeOrderStatusCommunicator::class,
            $cardPayChangeOrderStatusCommunicator->decodeResponseXml());
    }

    /**
     * @dataProvider dataProviderValidateResponseXmlSuccessful
     */
    public function testValidateResponseXmlSuccessful($response)
    {
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $cardPayChangeOrderStatusCommunicator->setResponse($response);

        $cardPayChangeOrderStatusCommunicator->decodeResponseXml();

        $this->assertTrue($cardPayChangeOrderStatusCommunicator->validateResponseXml());
    }

    public function dataProviderValidateResponseXmlSuccessful()
    {
        return [
            ["<response is_executed=\"yes\" refund_id=\"299151\"><order id=\"299150\" status_to=\"refund\" currency=\"USD\" refund_amount=\"42.38\" remaining_amount=\"132.54\" status=\"APPROVED\" /></response>"]
        ];
    }

    /**
     * @dataProvider dataProviderValidateResponseXmlFailure
     * @expectedException \CardPay\Exception\CardPayResponseException
     */
    public function testValidateResponseXmlFailure($response)
    {
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $cardPayChangeOrderStatusCommunicator->setResponse($response);

        $cardPayChangeOrderStatusCommunicator->decodeResponseXml();

        $cardPayChangeOrderStatusCommunicator->validateResponseXml();
    }

    public function dataProviderValidateResponseXmlFailure()
    {
        return [
            ["<order id=\"299150\" status_to=\"refund\" currency=\"USD\" refund_amount=\"42.38\" remaining_amount=\"132.54\" status=\"APPROVED\" />"],
            ["<response is_executed=\"yes\" refund_id=\"299151\"></response>"],
        ];
    }

    /**
     * @dataProvider dataProviderGetResponseXmlAttributesSuccessful
     */
    public function testGetResponseXmlAttributesSuccessful($response)
    {
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $cardPayChangeOrderStatusCommunicator->setResponse($response);

        $cardPayChangeOrderStatusCommunicator->decodeResponseXml();

        $cardPayChangeOrderStatusResponseXmlAttributes = $cardPayChangeOrderStatusCommunicator->getResponseXmlAttributes();

        $this->assertNotEmpty($cardPayChangeOrderStatusResponseXmlAttributes->is_executed);
    }

    public function dataProviderGetResponseXmlAttributesSuccessful()
    {
        return [
            ["<response is_executed=\"yes\" refund_id=\"299151\"><order id=\"299150\" status_to=\"refund\" currency=\"USD\" refund_amount=\"42.38\" remaining_amount=\"132.54\" status=\"APPROVED\" /></response>"]
        ];
    }

    /**
     * @dataProvider dataProviderGetResponseXmlAttributesFailure
     */
    public function testGetResponseXmlAttributesFailure($response)
    {
        $cardPayChangeOrderStatusCommunicator = new CardPayChangeOrderStatusCommunicator();

        $cardPayChangeOrderStatusCommunicator->setResponse($response);

        $cardPayChangeOrderStatusCommunicator->decodeResponseXml();

        $cardPayChangeOrderStatusResponseXmlAttributes = $cardPayChangeOrderStatusCommunicator->getResponseXmlAttributes();

        $this->assertObjectNotHasAttribute("is_executed", $cardPayChangeOrderStatusResponseXmlAttributes);
    }

    public function dataProviderGetResponseXmlAttributesFailure()
    {
        return [
            ["<response refund_id=\"299151\"><order id=\"299150\" status_to=\"refund\" currency=\"USD\" refund_amount=\"42.38\" remaining_amount=\"132.54\" status=\"APPROVED\" /></response>"]
        ];
    }
}
