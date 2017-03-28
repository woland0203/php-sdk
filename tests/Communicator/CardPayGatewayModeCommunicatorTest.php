<?php

use CardPay\Communicator\CardPayGatewayModeCommunicator;
use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;

class CardPayGatewayModeCommunicatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode)
    {
        $cardPayConfig = new CardPayConfig($mode);

        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $this->assertInstanceOf(CardPayGatewayModeCommunicator::class,
            $cardPayGatewayModeCommunicator->setConfig($cardPayConfig));

         $this->assertNotEmpty($cardPayConfig->getGatewayUrl());
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
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $this->assertInstanceOf(CardPayGatewayModeCommunicator::class,
            $cardPayGatewayModeCommunicator->setRequest($request));
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
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $cardPayGatewayModeCommunicator->setRequest($request);
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
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $this->assertInstanceOf(CardPayGatewayModeCommunicator::class,
            $cardPayGatewayModeCommunicator->setResponse($response));
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
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $cardPayGatewayModeCommunicator->setResponse($response);
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
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $cardPayGatewayModeCommunicator->setResponse($response);

        $this->assertInstanceOf(CardPayGatewayModeCommunicator::class,
            $cardPayGatewayModeCommunicator->decodeResponseXml());
    }

    /**
     * @dataProvider dataProviderValidateResponseXmlSuccessful
     */
    public function testValidateResponseXmlSuccessful($response)
    {
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $cardPayGatewayModeCommunicator->setResponse($response);

        $cardPayGatewayModeCommunicator->decodeResponseXml();

        $this->assertTrue($cardPayGatewayModeCommunicator->validateResponseXml());
    }

    public function dataProviderValidateResponseXmlSuccessful()
    {
        return [
            ["<redirect url=\"https://sandbox.cardpay.com/MI/payments/redirect?token=Ge3Ff38C07Dheg42D82gHG6H\" />"]
        ];
    }

    /**
     * @dataProvider dataProviderValidateResponseXmlFailure
     * @expectedException \CardPay\Exception\CardPayResponseException
     */
    public function testValidateResponseXmlFailure($response)
    {
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $cardPayGatewayModeCommunicator->setResponse($response);

        $cardPayGatewayModeCommunicator->decodeResponseXml();

        $cardPayGatewayModeCommunicator->validateResponseXml();
    }

    public function dataProviderValidateResponseXmlFailure()
    {
        return [
            ["<order id=\"-\" number=\"1487682864\" status=\"DECLINED\" description=\"Mandatory field is missing: card/cvv\" date=\"21.02.2017 13:14:25\" is_3d=\"false\" decline_code=\"02\" decline_reason=\"Cancelled by customer\" amount=\"100.11\" currency=\"USD\" />"]
        ];
    }

    /**
     * @dataProvider dataProviderGetResponseXmlAttributesSuccessful
     */
    public function testGetResponseXmlAttributesSuccessful($response)
    {
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $cardPayGatewayModeCommunicator->setResponse($response);

        $cardPayGatewayModeCommunicator->decodeResponseXml();

        $cardPayGatewayModeResponseXmlAttributes = $cardPayGatewayModeCommunicator->getResponseXmlAttributes();

        $this->assertNotEmpty($cardPayGatewayModeResponseXmlAttributes->url);
    }

    public function dataProviderGetResponseXmlAttributesSuccessful()
    {
        return [
            ["<redirect url=\"https://sandbox.cardpay.com/MI/payments/redirect?token=Ge3Ff38C07Dheg42D82gHG6H\" />"],
        ];
    }

    /**
     * @dataProvider dataProviderGetResponseXmlAttributesFailure
     */
    public function testGetResponseXmlAttributesFailure($response)
    {
        $cardPayGatewayModeCommunicator = new CardPayGatewayModeCommunicator();

        $cardPayGatewayModeCommunicator->setResponse($response);

        $cardPayGatewayModeCommunicator->decodeResponseXml();

        $cardPayGatewayModeResponseXmlAttributes = $cardPayGatewayModeCommunicator->getResponseXmlAttributes();

        $this->assertObjectNotHasAttribute("url", $cardPayGatewayModeResponseXmlAttributes);
    }

    public function dataProviderGetResponseXmlAttributesFailure()
    {
        return [
            ["<redirect some_url=\"https://sandbox.cardpay.com/MI/payments/redirect?token=Ge3Ff38C07Dheg42D82gHG6H\" />"],
            ["<order redirect_url=\"https://sandbox.cardpay.com/MI/payments/redirect?token=Ge3Ff38C07Dheg42D82gHG6H\" />"],
        ];
    }
}
