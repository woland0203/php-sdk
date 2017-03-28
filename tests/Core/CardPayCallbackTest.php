<?php

use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;
use CardPay\Core\CardPayCallback;

class CardPayCallbackTest extends PHPUnit_Framework_TestCase
{
    private function getConfig(){
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        return $cardPayConfig;
    }

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

        $cardPayCallback = new CardPayCallback();

        $this->assertInstanceOf(CardPayCallback::class, $cardPayCallback->setConfig($cardPayConfig));
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
     * @dataProvider dataProviderSetConfigFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetConfigFailure($mode, $wallet_id, $secret_key, $log_filepath)
    {
        $cardPayConfig = new CardPayConfig($mode);
        $cardPayConfig
            ->setWalletId($wallet_id)
            ->setSecretKey($secret_key)
            ->setLogFilePath($log_filepath);

        $cardPayCallback = new CardPayCallback();
        $cardPayCallback->setConfig($cardPayConfig);
    }

    public function dataProviderSetConfigFailure()
    {
        return [
            [
                2,
                0,
                "",
                __DIR__,
            ],
        ];
    }

    private function createCardPayCallback()
    {
        $cardPayConfig = $this->getConfig();

        $cardPayCallback = new CardPayCallback();
        $cardPayCallback->setConfig($cardPayConfig);

        return $cardPayCallback;
    }

    /**
     * @dataProvider dataProviderSetRequestSuccessful
     */
    public function testSetRequestSuccessful($request)
    {
        $cardPayCallback = $this->createCardPayCallback();

        $this->assertInstanceOf(CardPayCallback::class,
            $cardPayCallback->setRequest($request));
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
        $cardPayCallback = $this->createCardPayCallback();

        $cardPayCallback->setRequest($request);
    }

    public function dataProviderSetRequestFailure()
    {
        return [
            [""],
            [null],
            [[]],
        ];
    }

    /**
     * @dataProvider dataProviderParseRequestSuccessful
     */
    public function testParseRequestSuccessful($request)
    {
        $cardPayCallback = $this->createCardPayCallback();

        $cardPayCallback->setRequest($request);

        $this->assertTrue($cardPayCallback->parseRequest());
    }

    public function dataProviderParseRequestSuccessful()
    {
        return [
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5MCIgc3RhdHVzPSJBUFBST1ZFRCIgZGVzY3JpcHRpb249IkNPTkZJUk1FRCIgZGF0ZT0iMDYuMDUuMjAxNiAxMzozODowMCIgaXNfM2Q9ImZhbHNlIiBhcHByb3ZhbF9jb2RlPSJhYmNERUYiIGFtb3VudD0iMTAwLjExIiBjdXJyZW5jeT0iVVNEIiAvPg==",
                    "sha512" => "ce3af61601c658892d5cca0ae1413105ce4be631fd70d3ec60c93448c8eebe19dd4897a89d343fa5a36fc32df092796b26410528f29fe958632ce94cf7ad40ae"
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProviderParseRequestFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testParseRequestFailure($request, $expectedExceptionMessage)
    {
        $this->expectExceptionMessage($expectedExceptionMessage);

        $cardPayCallback = $this->createCardPayCallback();

        $cardPayCallback->setRequest($request);

        $cardPayCallback->parseRequest();
    }

    public function dataProviderParseRequestFailure()
    {
        return [
            [
                [
                    "orderXML" => "PG9yZGVyIG51bWJlcj0iMTIzNDU2Nzg5MCIgc3RhdHVzPSJBUFBST1ZFRCIgZGVzY3JpcHRpb249IkNPTkZJUk1FRCIgZGF0ZT0iMDYuMDUuMjAxNiAxMzozODowMCIgaXNfM2Q9ImZhbHNlIiBhcHByb3ZhbF9jb2RlPSJhYmNERUYiIGFtb3VudD0iMTAwLjExIiBjdXJyZW5jeT0iVVNEIiAvPg==",
                    "sha512" => "1672d01e561073b39e49556baffc11e32c2b61f57b044f3af57f82cd1bca4f77c2988053de789a274b10b9f4cabc01bdc4a890f3aaf16e27cf37ce566196a22d"
                ],
                "'Transaction id' has not a valid integer value"
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIHN0YXR1cz0iQVBQUk9WRUQiIGRlc2NyaXB0aW9uPSJDT05GSVJNRUQiIGRhdGU9IjA2LjA1LjIwMTYgMTM6Mzg6MDAiIGlzXzNkPSJmYWxzZSIgYXBwcm92YWxfY29kZT0iYWJjREVGIiBhbW91bnQ9IjEwMC4xMSIgY3VycmVuY3k9IlVTRCIgLz4=",
                    "sha512" => "247b9990a0c2c8bc9796bd189f2c2b8b8b0d375dbcca21c3298b38bac43be61a99c78705739e0516851e6e9d0d2a40149db4830f69ba73972b1806a5d5d6e1a8"
                ],
                "'Order id' has not a valid length. Minimum 1"
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5MCIgZGVzY3JpcHRpb249IkNPTkZJUk1FRCIgZGF0ZT0iMDYuMDUuMjAxNiAxMzozODowMCIgaXNfM2Q9ImZhbHNlIiBhcHByb3ZhbF9jb2RlPSJhYmNERUYiIGFtb3VudD0iMTAwLjExIiBjdXJyZW5jeT0iVVNEIiAvPg==",
                    "sha512" => "a085ee7278d82d9472fac6e2acac4c9224c2909e291e6a1ebc40b92c8f6cfafb43037e1f17902f82c85b4ae360b43909e6bb003897d7e939934f547ed202fa8a"
                ],
                "'Status' has not a valid length. Minimum 1"
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5MCIgc3RhdHVzPSJBUFBST1ZFRCIgZGF0ZT0iMDYuMDUuMjAxNiAxMzozODowMCIgaXNfM2Q9ImZhbHNlIiBhcHByb3ZhbF9jb2RlPSJhYmNERUYiIGFtb3VudD0iMTAwLjExIiBjdXJyZW5jeT0iVVNEIiAvPg==",
                    "sha512" => "45c8c9397f2391aad2fbb4d82b701f663b67e22d5a172046b759f645357dc95ff62cc5ece0c4c6afc8fe385f27c0d33141b46cb3594f03ab798a651b200d79ce"
                ],
                "'Description' has not a valid length. Minimum 1"
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5MCIgc3RhdHVzPSJBUFBST1ZFRCIgZGVzY3JpcHRpb249IkNPTkZJUk1FRCIgc18zZD0iZmFsc2UiIGFwcHJvdmFsX2NvZGU9ImFiY0RFRiIgYW1vdW50PSIxMDAuMTEiIGN1cnJlbmN5PSJVU0QiIC8+",
                    "sha512" => "d914fd81af727a3529fc8b72cc452aa0a77c0a0ebe1417dfe2bbac2901b1e9e7d7e0c39b811ff1d690f519d5c5216138567fb478ae33f7fd96adc744b1771821"
                ],
                "'Date' has not a valid datetime value"
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5MCIgc3RhdHVzPSJBUFBST1ZFRCIgZGVzY3JpcHRpb249IkNPTkZJUk1FRCIgZGF0ZT0iMDYuMDUuMjAxNiAxMzozODowMCIgYXBwcm92YWxfY29kZT0iYWJjREVGIiBhbW91bnQ9IjEwMC4xMSIgY3VycmVuY3k9IlVTRCIgLz4=",
                    "sha512" => "9744aef0557c4f988827f324a81a57b8c08fbb441cedf1f8d1707c36cadf4ae3984ab161b4abaecfc52ccbe0ab4998c6aa03fc0a1a56693c9ad7410fc1897476"
                ],
                "'Is 3ds' has not a valid value"
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5MCIgc3RhdHVzPSJBUFBST1ZFRCIgZGVzY3JpcHRpb249IkNPTkZJUk1FRCIgZGF0ZT0iMDYuMDUuMjAxNiAxMzozODowMCIgaXNfM2Q9ImZhbHNlIiBhcHByb3ZhbF9jb2RlPSJhYmNERUYiIGFtb3VudD0iMTAwLjExIiAvPg==",
                    "sha512" => "d8521f6ee9ba9f23ecb8a34f422d6f4ff519aa7ae9c943d89defc726d652eabb2bfcae6324b139b5ca006523c37be1385662de9769653063f202c1230e4c772a"
                ],
                "'Currency' has not a valid length. Minimum 3"
            ],
            [
                [
                    "orderXML" => "PG9yZGVyIGlkPSIxMjM0NTYiIG51bWJlcj0iMTIzNDU2Nzg5MCIgc3RhdHVzPSJBUFBST1ZFRCIgZGVzY3JpcHRpb249IkNPTkZJUk1FRCIgZGF0ZT0iMDYuMDUuMjAxNiAxMzozODowMCIgaXNfM2Q9ImZhbHNlIiBhcHByb3ZhbF9jb2RlPSJhYmNERUYiIGN1cnJlbmN5PSJVU0QiIC8+",
                    "sha512" => "a5751bde33b064f91761683a434bdfbf6001e5b3f0b227b2c7ac4925c3103ce52dcfaecd39408cf4ad639a577f8a058bf6c2aaa7ae9bdba28fb133721966dc3d"
                ],
                "'Amount' has not a valid amount value"
            ],
        ];
    }

}
