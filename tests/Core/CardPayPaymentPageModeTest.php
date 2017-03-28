<?php

use CardPay\Core\CardPayAddress;
use \CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;
use \CardPay\Core\CardPayPaymentPageMode;
use \CardPay\Core\CardPayItem;

class CardPayPaymentPageModeTest extends PHPUnit_Framework_TestCase
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

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();

        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setConfig($cardPayConfig));
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

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();
        $cardPayPaymentPageMode->setConfig($cardPayConfig);
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

    /**
     * @dataProvider dataProviderSetRequiredFieldsSuccessful
     */
    public function testSetRequiredFieldsSuccessful(
        $order_id,
        $description,
        $currency,
        $amount,
        $email
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();
        $cardPayPaymentPageMode->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setOrderId($order_id));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setDescription($description));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setCurrency($currency));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setAmount($amount));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setEmail($email));
    }

    public function dataProviderSetRequiredFieldsSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                $faker->text(200),
                $faker->randomElement(["USD", "EUR", "RUB"]),
                $faker->randomFloat(2, 0.01, 999999.99),
                $faker->email,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetRequiredFieldsFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetRequiredFieldsFailure(
        $order_id,
        $description,
        $currency,
        $amount,
        $email
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();
        $cardPayPaymentPageMode->setConfig($cardPayConfig);

        $cardPayPaymentPageMode->setOrderId($order_id);
        $cardPayPaymentPageMode->setDescription($description);
        $cardPayPaymentPageMode->setCurrency($currency);
        $cardPayPaymentPageMode->setAmount($amount);
        $cardPayPaymentPageMode->setEmail($email);
    }

    public function dataProviderSetRequiredFieldsFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetRequiredFieldsSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderSetNonRequiredFieldsSuccessful
     */
    public function testSetNonRequiredFieldsSuccessful(
        $customer_id,
        $two_phase,
        $recurring_begin,
        $recurring_id,
        $generate_card_token,
        $card_token,
        $authentication_request,
        $locale,
        $note,
        $return_url,
        $success_url,
        $decline_url,
        $cancel_url,
        $shipping_data = array(),
        $item_list = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();
        $cardPayPaymentPageMode->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setCustomerId($customer_id));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setIsTwoPhase($two_phase));
        $this->assertInstanceOf(CardPayPaymentPageMode::class,
            $cardPayPaymentPageMode->setRecurringBegin($recurring_begin));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setRecurringId($recurring_id));
        $this->assertInstanceOf(CardPayPaymentPageMode::class,
            $cardPayPaymentPageMode->setGenerateCardToken($generate_card_token));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setCardToken($card_token));
        $this->assertInstanceOf(CardPayPaymentPageMode::class,
            $cardPayPaymentPageMode->setAuthenticationRequest($authentication_request));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setLocale($locale));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setNote($note));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setReturnUrl($return_url));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setSuccessUrl($success_url));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setDeclineUrl($decline_url));
        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setCancelUrl($cancel_url));

        $address = new CardPayAddress();
        $address
            ->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setShipping($address));

        $items = array();
        foreach ($item_list as $item_data) {
            $item = new CardPayItem();
            $item
                ->setName($item_data["name"])
                ->setDescription($item_data["description"])
                ->setCount($item_data["count"])
                ->setPrice($item_data["price"]);

            $items[] = $item;
        }

        $this->assertInstanceOf(CardPayPaymentPageMode::class, $cardPayPaymentPageMode->setItems($items));
    }

    public function dataProviderSetNonRequiredFieldsSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->name,
                $faker->boolean,
                $faker->boolean,
                $faker->regexify('[\w\d]{32}'),
                $faker->boolean,
                $faker->regexify('[\w\d]{32}'),
                $faker->boolean,
                $faker->randomElement(["en", "ru", "cy"]),
                $faker->text(100),
                $faker->url,
                $faker->url,
                $faker->url,
                $faker->url,
                [
                    "country" => $faker->countryCode,
                    "state" => $faker->state,
                    "zip" => $faker->postcode,
                    "city" => substr($faker->city, 0, 20),
                    "street" => $faker->address,
                    "phone" => $faker->e164PhoneNumber,
                ],
                [
                    [
                        "name" => $faker->text(50),
                        "description" => $faker->text(200),
                        "count" => $faker->randomDigitNotNull,
                        "price" => $faker->randomFloat(2, 0.01, 999999.99),
                    ],
                    [
                        "name" => $faker->text(50),
                        "description" => $faker->text(200),
                        "count" => $faker->randomDigitNotNull,
                        "price" => $faker->randomFloat(2, 0.01, 999999.99),
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetNonRequiredFieldsFailure
     * @expectedException \CardPay\Exception\CardPayValidationException
     */
    public function testSetNonRequiredFieldsFailure(
        $customer_id,
        $two_phase,
        $recurring_begin,
        $recurring_id,
        $generate_card_token,
        $card_token,
        $authentication_request,
        $locale,
        $note,
        $return_url,
        $success_url,
        $decline_url,
        $cancel_url,
        $shipping_data = array(),
        $item_list = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();
        $cardPayPaymentPageMode->setConfig($cardPayConfig);

        $cardPayPaymentPageMode->setCustomerId($customer_id);
        $cardPayPaymentPageMode->setIsTwoPhase($two_phase);
        $cardPayPaymentPageMode->setRecurringBegin($recurring_begin);
        $cardPayPaymentPageMode->setRecurringId($recurring_id);
        $cardPayPaymentPageMode->setGenerateCardToken($generate_card_token);
        $cardPayPaymentPageMode->setCardToken($card_token);
        $cardPayPaymentPageMode->setAuthenticationRequest($authentication_request);
        $cardPayPaymentPageMode->setLocale($locale);
        $cardPayPaymentPageMode->setNote($note);
        $cardPayPaymentPageMode->setReturnUrl($return_url);
        $cardPayPaymentPageMode->setSuccessUrl($success_url);
        $cardPayPaymentPageMode->setDeclineUrl($decline_url);
        $cardPayPaymentPageMode->setCancelUrl($cancel_url);

        $address = new CardPayAddress();
        $address->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $cardPayPaymentPageMode->setShipping($address);

        $items = array();
        foreach ($item_list as $item_data) {
            $item = new CardPayItem();
            $item
                ->setName($item_data["name"])
                ->setDescription($item_data["description"])
                ->setCount($item_data["count"])
                ->setPrice($item_data["price"]);

            $items[] = $item;
        }

        $cardPayPaymentPageMode->setItems($items);
    }

    public function dataProviderSetNonRequiredFieldsFailure()
    {
        return CardPayTestHelper::generateDataForFailureTests($this->dataProviderSetNonRequiredFieldsSuccessful()[0]);
    }

    /**
     * @dataProvider dataProviderGetOrderXMLSuccessful
     */
    public function testGetOrderXMLSuccessful(
        $order_id,
        $description,
        $currency,
        $amount,
        $email,
        $customer_id,
        $two_phase,
        $recurring_begin,
        $recurring_id,
        $generate_card_token,
        $card_token,
        $authentication_request,
        $locale,
        $note,
        $return_url,
        $success_url,
        $decline_url,
        $cancel_url,
        $shipping_data = array(),
        $item_list = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();
        $cardPayPaymentPageMode
            ->setConfig($cardPayConfig)
            ->setOrderId($order_id)
            ->setDescription($description)
            ->setCurrency($currency)
            ->setAmount($amount)
            ->setEmail($email)
            ->setCustomerId($customer_id)
            ->setIsTwoPhase($two_phase)
            ->setRecurringBegin($recurring_begin)
            ->setRecurringId($recurring_id)
            ->setGenerateCardToken($generate_card_token)
            ->setCardToken($card_token)
            ->setAuthenticationRequest($authentication_request)
            ->setLocale($locale)
            ->setNote($note)
            ->setReturnUrl($return_url)
            ->setSuccessUrl($success_url)
            ->setDeclineUrl($decline_url)
            ->setCancelUrl($cancel_url);

        $shipping_address = new CardPayAddress();
        $shipping_address
            ->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $cardPayPaymentPageMode->setShipping($shipping_address);

        $items = array();
        foreach ($item_list as $item_data) {
            $item = new CardPayItem();
            $item
                ->setName($item_data["name"])
                ->setDescription($item_data["description"])
                ->setCount($item_data["count"])
                ->setPrice($item_data["price"]);

            $items[] = $item;
        }

        $cardPayPaymentPageMode->setItems($items);

        $walletId = CardPayTestHelper::WALLET_ID;

        $customer_id = CardPayTestHelper::normalizeString($customer_id);

        $shipping_data["city"] = CardPayTestHelper::normalizeString($shipping_data["city"]);
        $shipping_data["street"] = CardPayTestHelper::normalizeString($shipping_data["street"]);

        $expected = "<order wallet_id=\"{$walletId}\"
				number=\"{$order_id}\"
				description=\"{$description}\"
				currency=\"{$currency}\"
				amount=\"{$amount}\"
				email=\"{$email}\""
            . (!empty($customer_id)
                ? " customer_id=\"{$customer_id}\""
                : "")
            . (!empty($two_phase)
                ? " is_two_phase=\"{$two_phase}\""
                : "")
            . (!empty($recurring_begin)
                ? " recurring_begin=\"{$recurring_begin}\""
                : "")
            . (!empty($recurring_id)
                ? " recurring_id=\"{$recurring_id}\""
                : "")
            . (!empty($generate_card_token)
                ? " generate_card_token=\"{$generate_card_token}\""
                : "")
            . (!empty($card_token)
                ? " card_token=\"{$card_token}\""
                : "")
            . (!empty($authentication_request)
                ? " authentication_request=\"{$authentication_request}\""
                : "")
            . (!empty($locale)
                ? " locale=\"{$locale}\""
                : "")
            . (!empty($note)
                ? " note=\"{$note}\""
                : "")
            . (!empty($return_url)
                ? " return_url=\"{$return_url}\""
                : "")
            . (!empty($success_url)
                ? " success_url=\"{$success_url}\""
                : "")
            . (!empty($decline_url)
                ? " decline_url=\"{$decline_url}\""
                : "")
            . (!empty($cancel_url)
                ? " cancel_url=\"{$cancel_url}\""
                : "")
            . ">"
            . "<shipping country=\"{$shipping_data["country"]}\" state=\"{$shipping_data["state"]}\" zip=\"{$shipping_data["zip"]}\" city=\"{$shipping_data["city"]}\" street=\"{$shipping_data["street"]}\" phone=\"{$shipping_data["phone"]}\"></shipping>"
            . "<items>"
            . "<item name=\"{$item_list[0]["name"]}\" description=\"{$item_list[0]["description"]}\" count=\"{$item_list[0]["count"]}\" price=\"{$item_list[0]["price"]}\"></item>"
            . "<item name=\"{$item_list[1]["name"]}\" description=\"{$item_list[1]["description"]}\" count=\"{$item_list[1]["count"]}\" price=\"{$item_list[1]["price"]}\"></item>"
            . "</items>"
            . "</order>";

        $this->assertXmlStringEqualsXmlString($expected, $cardPayPaymentPageMode->getOrderXML());
    }

    public function dataProviderGetOrderXMLSuccessful()
    {
        $faker = \Faker\Factory::create();

        return [
            [
                $faker->regexify('[0-9A-Z]{2,20}'),
                $faker->text(200),
                $faker->randomElement(["USD", "EUR", "RUB"]),
                $faker->randomFloat(2, 0.01, 999999.99),
                $faker->email,
                $faker->name,
                $faker->boolean,
                $faker->boolean,
                $faker->regexify('[\w\d]{32}'),
                $faker->boolean,
                $faker->regexify('[\w\d]{32}'),
                $faker->boolean,
                $faker->randomElement(["en", "ru", "cy"]),
                $faker->text(100),
                $faker->url,
                $faker->url,
                $faker->url,
                $faker->url,
                [
                    "country" => $faker->countryCode,
                    "state" => $faker->state,
                    "zip" => $faker->postcode,
                    "city" => substr($faker->city, 0, 20),
                    "street" => $faker->streetAddress,
                    "phone" => $faker->e164PhoneNumber,
                ],
                [
                    [
                        "name" => $faker->text(50),
                        "description" => $faker->text(200),
                        "count" => $faker->randomDigitNotNull,
                        "price" => $faker->randomFloat(2, 0.01, 999999.99),
                    ],
                    [
                        "name" => $faker->text(50),
                        "description" => $faker->text(200),
                        "count" => $faker->randomDigitNotNull,
                        "price" => $faker->randomFloat(2, 0.01, 999999.99),
                    ],
                ],
            ],
        ];
    }


    /**
     * @dataProvider dataProviderValidOrderXMLSuccessful
     */
    public function testValidOrderXMLSuccessful(
        $order_id,
        $description,
        $currency,
        $amount,
        $email,
        $customer_id,
        $locale,
        $note,
        $return_url,
        $shipping_data = array(),
        $item_list = array(),
        $order_xml_base64,
        $order_xml_sha512
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayPaymentPageMode = new CardPayPaymentPageMode();
        $cardPayPaymentPageMode
            ->setConfig($cardPayConfig)
            ->setOrderId($order_id)
            ->setDescription($description)
            ->setCurrency($currency)
            ->setAmount($amount)
            ->setEmail($email)
            ->setCustomerId($customer_id)
            ->setLocale($locale)
            ->setNote($note)
            ->setReturnUrl($return_url);

        $shipping_address = new CardPayAddress();
        $shipping_address
            ->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $cardPayPaymentPageMode->setShipping($shipping_address);

        $items = array();
        foreach ($item_list as $item_data) {
            $item = new CardPayItem();
            $item
                ->setName($item_data["name"])
                ->setDescription($item_data["description"])
                ->setCount($item_data["count"])
                ->setPrice($item_data["price"]);

            $items[] = $item;
        }

        $cardPayPaymentPageMode->setItems($items);

        $this->assertEquals($order_xml_base64, $cardPayPaymentPageMode->getOrderXML(true));
        $this->assertEquals($order_xml_sha512, $cardPayPaymentPageMode->getSHA512());
    }

    public function dataProviderValidOrderXMLSuccessful()
    {
        return [
            [
                "ddwo8dm",
                "Est totam quibusdam culpa dolorem architecto voluptatem",
                "USD",
                "781070.83",
                "jmedhurst@hotmail.com",
                "Elenora Walter",
                "en",
                "Qui libero animi ratione qui alias. Voluptatibus quasi est dicta. Sit aliquam cum ex rerum et cum.",
                "https://greenholt.com/sapiente-et-ratione-fuga-sit-in-voluptas-distinctio",
                [
                    "country" => "KY",
                    "state" => "Tennessee",
                    "zip" => "02205-1084",
                    "city" => "Kassandrachester",
                    "street" => "74397 Sigrid Loaf\nJarrettberg, SD 59238",
                    "phone" => "+8889286274795",
                ],
                [
                    [
                        "name" => "Magni aut necessitatibus autem est.",
                        "description" => "Dolores autem maxime et at. In magnam qui nam quos iusto dolores. Dolor et voluptates et et vel sed. Ut nam molestiae nesciunt adipisci. Ut voluptas eum ad aliquid.",
                        "count" => "6",
                        "price" => "668541.86",
                    ],
                    [
                        "name" => "Voluptatem eos autem veniam dolor et occaecati.",
                        "description" => "Et animi voluptas eaque neque architecto. Et quasi accusamus voluptate ea quidem. Illum cum accusantium optio enim repellendus. Perspiciatis ut voluptatibus culpa aut iste distinctio quis.",
                        "count" => "2",
                        "price" => "2194.04",
                    ],
                ],
                "PG9yZGVyIHdhbGxldF9pZD0iMTAwMCIgbnVtYmVyPSJkZHdvOGRtIiBkZXNjcmlwdGlvbj0iRXN0IHRvdGFtIHF1aWJ1c2RhbSBjdWxwYSBkb2xvcmVtIGFyY2hpdGVjdG8gdm9sdXB0YXRlbSIgY3VycmVuY3k9IlVTRCIgYW1vdW50PSI3ODEwNzAuODMiIGVtYWlsPSJqbWVkaHVyc3RAaG90bWFpbC5jb20iIGN1c3RvbWVyX2lkPSJFbGVub3JhIFdhbHRlciIgbG9jYWxlPSJlbiIgbm90ZT0iUXVpIGxpYmVybyBhbmltaSByYXRpb25lIHF1aSBhbGlhcy4gVm9sdXB0YXRpYnVzIHF1YXNpIGVzdCBkaWN0YS4gU2l0IGFsaXF1YW0gY3VtIGV4IHJlcnVtIGV0IGN1bS4iIHJldHVybl91cmw9Imh0dHBzOi8vZ3JlZW5ob2x0LmNvbS9zYXBpZW50ZS1ldC1yYXRpb25lLWZ1Z2Etc2l0LWluLXZvbHVwdGFzLWRpc3RpbmN0aW8iPjxzaGlwcGluZyBjb3VudHJ5PSJLWSIgc3RhdGU9IlRlbm5lc3NlZSIgemlwPSIwMjIwNS0xMDg0IiBjaXR5PSJLYXNzYW5kcmFjaGVzdGVyIiBzdHJlZXQ9Ijc0Mzk3IFNpZ3JpZCBMb2FmIEphcnJldHRiZXJnLCBTRCA1OTIzOCIgcGhvbmU9Iis4ODg5Mjg2Mjc0Nzk1Ij48L3NoaXBwaW5nPjxpdGVtcz48aXRlbSBuYW1lPSJNYWduaSBhdXQgbmVjZXNzaXRhdGlidXMgYXV0ZW0gZXN0LiIgZGVzY3JpcHRpb249IkRvbG9yZXMgYXV0ZW0gbWF4aW1lIGV0IGF0LiBJbiBtYWduYW0gcXVpIG5hbSBxdW9zIGl1c3RvIGRvbG9yZXMuIERvbG9yIGV0IHZvbHVwdGF0ZXMgZXQgZXQgdmVsIHNlZC4gVXQgbmFtIG1vbGVzdGlhZSBuZXNjaXVudCBhZGlwaXNjaS4gVXQgdm9sdXB0YXMgZXVtIGFkIGFsaXF1aWQuIiBjb3VudD0iNiIgcHJpY2U9IjY2ODU0MS44NiI+PC9pdGVtPjxpdGVtIG5hbWU9IlZvbHVwdGF0ZW0gZW9zIGF1dGVtIHZlbmlhbSBkb2xvciBldCBvY2NhZWNhdGkuIiBkZXNjcmlwdGlvbj0iRXQgYW5pbWkgdm9sdXB0YXMgZWFxdWUgbmVxdWUgYXJjaGl0ZWN0by4gRXQgcXVhc2kgYWNjdXNhbXVzIHZvbHVwdGF0ZSBlYSBxdWlkZW0uIElsbHVtIGN1bSBhY2N1c2FudGl1bSBvcHRpbyBlbmltIHJlcGVsbGVuZHVzLiBQZXJzcGljaWF0aXMgdXQgdm9sdXB0YXRpYnVzIGN1bHBhIGF1dCBpc3RlIGRpc3RpbmN0aW8gcXVpcy4iIGNvdW50PSIyIiBwcmljZT0iMjE5NC4wNCI+PC9pdGVtPjwvaXRlbXM+PC9vcmRlcj4=",
                "affb8d10f6991a541300bdd5e2097f9118711db1603b25a6b085bfcf834defe2d11db84a6ccbae655f2782314fd7a8c56767e96355d675ea07bc63b3038ebb1f",
            ],
        ];
    }
}
