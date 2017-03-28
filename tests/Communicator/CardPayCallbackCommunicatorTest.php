<?php

use CardPay\Communicator\CardPayCallbackCommunicator;
use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;

class CardPayCallbackCommunicatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderSetConfigSuccessful
     */
    public function testSetConfigSuccessful($mode, $wallet_id, $secret_key, $log_filepath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setWalletId($wallet_id)
            ->setSecretKey($secret_key)
            ->setLogFilePath($log_filepath);

        $cardPayCallbackCommunicator = new CardPayCallbackCommunicator();

        $this->assertInstanceOf(CardPayCallbackCommunicator::class,
            $cardPayCallbackCommunicator->setConfig($cardPayConfig));

        $this->assertNotEmpty($cardPayConfig->getSecretKey());
    }

    public function dataProviderSetConfigSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                CardPayEndpoint::TEST,
                $faker->randomNumber(5),
                $faker->shuffle("merchant_secret_key"),
                __DIR__ . "/cardpay.log",
            ],
            [
                CardPayEndpoint::LIVE,
                $faker->randomNumber(5),
                $faker->shuffle("merchant_secret_key"),
                __DIR__ . "/cardpay.log",
            ],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestSuccessful
     */
    public function testSetRequestSuccessful($request)
    {
        $cardPayCallbackCommunicator = new CardPayCallbackCommunicator();

        $this->assertInstanceOf(CardPayCallbackCommunicator::class,
            $cardPayCallbackCommunicator->setRequest($request));
    }

    public function dataProviderSetRequestSuccessful()
    {
        return [
            [["a" => "b"]],
        ];

    }

    /**
     * @dataProvider dataProviderSetRequestFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequestFailure($request)
    {
        $cardPayCallbackCommunicator = new CardPayCallbackCommunicator();

        $cardPayCallbackCommunicator->setRequest($request);
    }

    public function dataProviderSetRequestFailure()
    {
        return [
            [""],
            [null],
            [[]],
        ];
    }

    private function createCardPayCallbackCommunicator()
    {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayCallbackCommunicator = new CardPayCallbackCommunicator();
        $cardPayCallbackCommunicator->setConfig($cardPayConfig);

        return $cardPayCallbackCommunicator;
    }

    /**
     * @dataProvider dataProviderValidateRequestSuccessful
     */
    public function testValidateRequestSuccessful($request)
    {
        $cardPayCallbackCommunicator = $this->createCardPayCallbackCommunicator();

        $cardPayCallbackCommunicator->setRequest($request);

        $this->assertInstanceOf(CardPayCallbackCommunicator::class,
            $cardPayCallbackCommunicator->validateRequest());
    }

    public function dataProviderValidateRequestSuccessful()
    {
        return [
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5IiBzdGF0dXM9IkFQUFJPVkVEIiBkZXNjcmlwdGlvbj0iQ09ORklSTUVEIiBkYXRlPSIxMy4wMy4yMDE3IDA5OjUyOjUwIiBpc18zZD0idHJ1ZSIgYXBwcm92YWxfY29kZT0iYWJhYmFiIiBhbW91bnQ9IjEwMC4xMSIgY3VycmVuY3k9IlVTRCIgLz4=",
                    "sha512" => "33f096b3d5f5d20eff8763809ded05adbfcd2431f148b5c0a184745bdfa0ea6dd36818552c6611c27bc19e8eb4422ad1b7bf8f0059a85933c121bb884504880e"
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderValidateRequestFailureStructure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testValidateRequestFailureStructure($request, $expectedException)
    {
        $this->expectException($expectedException);

        $cardPayCallbackCommunicator = $this->createCardPayCallbackCommunicator();

        $cardPayCallbackCommunicator->setRequest($request);

        $cardPayCallbackCommunicator->validateRequest();
    }

    public function dataProviderValidateRequestFailureStructure()
    {
        return [
            [
                [
                    "orderXML" => "",
                    "sha512" => "33f096b3d5f5d20eff8763809ded05adbfcd2431f148b5c0a184745bdfa0ea6dd36818552c6611c27bc19e8eb4422ad1b7bf8f0059a85933c121bb884504880e"
                ],
                \CardPay\Exception\CardPayValidationException::class
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5IiBzdGF0dXM9IkFQUFJPVkVEIiBkZXNjcmlwdGlvbj0iQ09ORklSTUVEIiBkYXRlPSIxMy4wMy4yMDE3IDA5OjUyOjUwIiBpc18zZD0idHJ1ZSIgYXBwcm92YWxfY29kZT0iYWJhYmFiIiBhbW91bnQ9IjEwMC4xMSIgY3VycmVuY3k9IlVTRCIgLz4=",
                    "sha512" => ""
                ],
                \CardPay\Exception\CardPayValidationException::class
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5IiBzdGF0dXM9IkFQUFJPVkVEIiBkZXNjcmlwdGlvbj0iQ09ORklSTUVEIiBkYXRlPSIxMy4wMy4yMDE3IDA5OjUyOjUwIiBpc18zZD0idHJ1ZSIgYXBwcm92YWxfY29kZT0iYWJhYmFiIiBhbW91bnQ9IjEwMC4xMSIgY3VycmVuY3k9IlVTRCIgLz4=",
                    "sha512" => "32f096b3d5f5d20eff8763809ded05adbfcd2431f148b5c0a184745bdfa0ea6dd36818552c6611c27bc19e8eb4422ad1b7bf8f0059a85933c121bb884504880e"
                ],
                \CardPay\Exception\CardPayCipherException::class
            ],
        ];
    }

    /**
     * @dataProvider dataProviderValidateRequestSuccessful
     */
    public function testDecodeRequestXmlSuccessful($request)
    {
        $cardPayCallbackCommunicator = $this->createCardPayCallbackCommunicator();

        $cardPayCallbackCommunicator
            ->setRequest($request)
            ->validateRequest();

        $this->assertInstanceOf(CardPayCallbackCommunicator::class,
            $cardPayCallbackCommunicator->decodeRequestXml());
    }

    /**
     * @dataProvider dataProviderValidateRequestSuccessful
     */
    public function testValidateRequestXmlSuccessful($request)
    {
        $cardPayCallbackCommunicator = $this->createCardPayCallbackCommunicator();

        $cardPayCallbackCommunicator
            ->setRequest($request)
            ->validateRequest()
            ->decodeRequestXml();

        $this->assertTrue($cardPayCallbackCommunicator->validateRequestXml());
    }

    /**
     * @dataProvider dataProviderValidateRequestXmlFailure
     * @expectedException \CardPay\Exception\CardPayCallbackException
     */
    public function testValidateRequestXmlFailure($request)
    {
        $cardPayCallbackCommunicator = $this->createCardPayCallbackCommunicator();

        $cardPayCallbackCommunicator
            ->setRequest($request)
            ->validateRequest()
            ->decodeRequestXml();

        $cardPayCallbackCommunicator->validateRequestXml();
    }

    public function dataProviderValidateRequestXmlFailure()
    {
        return [
            [
                [
                    "orderXML" => "PG5vbm9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5IiBzdGF0dXM9IkFQUFJPVkVEIiBkZXNjcmlwdGlvbj0iQ09ORklSTUVEIiBkYXRlPSIxMy4wMy4yMDE3IDA5OjUyOjUwIiBpc18zZD0idHJ1ZSIgYXBwcm92YWxfY29kZT0iYWJhYmFiIiBhbW91bnQ9IjEwMC4xMSIgY3VycmVuY3k9IlVTRCIgLz4=",
                    "sha512" => "4ec18790064b803cc6ed789baebf9bbd66f58eab4ee7de391cf3a2b018b3db6a68f2c61809e671e68fd1ac229ac6620805df3d748117996502afbbf3a421eaec"
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderValidateRequestSuccessful
     */
    public function testGetRequestXmlAttributesSuccessful($request)
    {
        $cardPayCallbackCommunicator = $this->createCardPayCallbackCommunicator();

        $cardPayCallbackCommunicator
            ->setRequest($request)
            ->validateRequest()
            ->decodeRequestXml()
            ->validateRequestXml();

        $cardPayCallbackRequestXmlAttributes = $cardPayCallbackCommunicator->getRequestXmlAttributes();

        $this->assertNotEmpty($cardPayCallbackRequestXmlAttributes->status);
    }

    /**
     * @dataProvider dataProviderGetRequestXmlAttributesFailure
     */
    public function testGetRequestXmlAttributesFailure($request)
    {
        $cardPayCallbackCommunicator = $this->createCardPayCallbackCommunicator();

        $cardPayCallbackCommunicator
            ->setRequest($request)
            ->validateRequest()
            ->decodeRequestXml()
            ->validateRequestXml();

        $cardPayCallbackRequestXmlAttributes = $cardPayCallbackCommunicator->getRequestXmlAttributes();

        $this->assertObjectNotHasAttribute("status", $cardPayCallbackRequestXmlAttributes);
    }

    public function dataProviderGetRequestXmlAttributesFailure()
    {
        return [
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5IiBkZXNjcmlwdGlvbj0iQ09ORklSTUVEIiBkYXRlPSIxMy4wMy4yMDE3IDA5OjUyOjUwIiBpc18zZD0idHJ1ZSIgYXBwcm92YWxfY29kZT0iYWJhYmFiIiBhbW91bnQ9IjEwMC4xMSIgY3VycmVuY3k9IlVTRCIgLz4=",
                    "sha512" => "e3df999051eb0c411e62a323e49f471aa5aa47dbeee1aeff29ee4841c74ab3da657aaa5201f75a9ca44858374489c6767dcdaa97292b2f0228e93b1d4d826857"
                ]
            ],
        ];
    }
}
