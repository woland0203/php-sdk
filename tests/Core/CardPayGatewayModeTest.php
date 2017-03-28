<?php

use CardPay\Core\CardPayAddress;
use CardPay\Core\CardPayCard;
use CardPay\Core\CardPayConfig;
use CardPay\Core\CardPayEndpoint;
use CardPay\Core\CardPayGatewayMode;
use CardPay\Core\CardPayItem;

class CardPayGatewayModeTest extends PHPUnit_Framework_TestCase
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

        $cardPayGatewayMode = new CardPayGatewayMode();

        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setConfig($cardPayConfig));
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

        $cardPayGatewayMode = new CardPayGatewayMode();
        $cardPayGatewayMode->setConfig($cardPayConfig);
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
        $email,
        $card_data = array(),
        $billing_data = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayGatewayMode = new CardPayGatewayMode();
        $cardPayGatewayMode->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setOrderId($order_id));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setDescription($description));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setCurrency($currency));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setAmount($amount));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setEmail($email));

        $card = new CardPayCard();
        $card
            ->setCardNumber($card_data["card_number"])
            ->setCardholderName($card_data["cardholder_name"])
            ->setExpirationDate($card_data["expiration_date_year"], $card_data["expiration_date_month"])
            ->setCvc($card_data["cvc"]);

        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setCard($card));

        $address = new CardPayAddress();
        $address
            ->setCountry($billing_data["country"])
            ->setState($billing_data["state"])
            ->setZip($billing_data["zip"])
            ->setCity($billing_data["city"])
            ->setStreet($billing_data["street"])
            ->setPhone($billing_data["phone"]);

        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setBilling($address));
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
                [
                    "card_number" => $faker->creditCardNumber,
                    "cardholder_name" => $faker->name,
                    "expiration_date_year" => $faker->year,
                    "expiration_date_month" => $faker->month,
                    "cvc" => $faker->regexify('[0-9]{3,3}')
                ],
                [
                    "country" => $faker->countryCode,
                    "state" => $faker->state,
                    "zip" => $faker->postcode,
                    "city" => substr($faker->city, 0, 20),
                    "street" => $faker->address,
                    "phone" => $faker->e164PhoneNumber,
                ],
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
        $email,
        $card_data = array(),
        $billing_data = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayGatewayMode = new CardPayGatewayMode();
        $cardPayGatewayMode->setConfig($cardPayConfig);

        $cardPayGatewayMode->setOrderId($order_id);
        $cardPayGatewayMode->setDescription($description);
        $cardPayGatewayMode->setCurrency($currency);
        $cardPayGatewayMode->setAmount($amount);
        $cardPayGatewayMode->setEmail($email);

        $card = new CardPayCard();
        $card
            ->setCardNumber($card_data["card_number"])
            ->setCardholderName($card_data["cardholder_name"])
            ->setExpirationDate($card_data["expiration_date_year"], $card_data["expiration_date_month"])
            ->setCvc($card_data["cvc"]);

        $cardPayGatewayMode->setCard($card);

        $address = new CardPayAddress();
        $address
            ->setCountry($billing_data["country"])
            ->setState($billing_data["state"])
            ->setZip($billing_data["zip"])
            ->setCity($billing_data["city"])
            ->setStreet($billing_data["street"])
            ->setPhone($billing_data["phone"]);

        $cardPayGatewayMode->setBilling($address);
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
        $note,
        $return_url,
        $success_url,
        $decline_url,
        $shipping_data = array(),
        $item_list = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayGatewayMode = new CardPayGatewayMode();
        $cardPayGatewayMode->setConfig($cardPayConfig);

        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setCustomerId($customer_id));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setIsTwoPhase($two_phase));
        $this->assertInstanceOf(CardPayGatewayMode::class,
            $cardPayGatewayMode->setRecurringBegin($recurring_begin));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setRecurringId($recurring_id));
        $this->assertInstanceOf(CardPayGatewayMode::class,
            $cardPayGatewayMode->setGenerateCardToken($generate_card_token));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setCardToken($card_token));
        $this->assertInstanceOf(CardPayGatewayMode::class,
            $cardPayGatewayMode->setAuthenticationRequest($authentication_request));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setNote($note));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setReturnUrl($return_url));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setSuccessUrl($success_url));
        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setDeclineUrl($decline_url));

        $address = new CardPayAddress();
        $address
            ->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setShipping($address));

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

        $this->assertInstanceOf(CardPayGatewayMode::class, $cardPayGatewayMode->setItems($items));
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
                $faker->text(100),
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
        $note,
        $return_url,
        $success_url,
        $decline_url,
        $shipping_data = array(),
        $item_list = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayGatewayMode = new CardPayGatewayMode();
        $cardPayGatewayMode->setConfig($cardPayConfig);

        $cardPayGatewayMode->setCustomerId($customer_id);
        $cardPayGatewayMode->setIsTwoPhase($two_phase);
        $cardPayGatewayMode->setRecurringBegin($recurring_begin);
        $cardPayGatewayMode->setRecurringId($recurring_id);
        $cardPayGatewayMode->setGenerateCardToken($generate_card_token);
        $cardPayGatewayMode->setCardToken($card_token);
        $cardPayGatewayMode->setAuthenticationRequest($authentication_request);
        $cardPayGatewayMode->setNote($note);
        $cardPayGatewayMode->setReturnUrl($return_url);
        $cardPayGatewayMode->setSuccessUrl($success_url);
        $cardPayGatewayMode->setDeclineUrl($decline_url);

        $address = new CardPayAddress();
        $address->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $cardPayGatewayMode->setShipping($address);

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

        $cardPayGatewayMode->setItems($items);
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
        $note,
        $return_url,
        $success_url,
        $decline_url,
        $card_data = array(),
        $billing_data = array(),
        $shipping_data = array(),
        $item_list = array()
    ) {
        $cardPayConfig = new CardPayConfig(CardPayTestHelper::MODE);
        $cardPayConfig
            ->setWalletId(CardPayTestHelper::WALLET_ID)
            ->setSecretKey(CardPayTestHelper::SECRET_KEY)
            ->setLogFilePath(CardPayTestHelper::LOG_FILEPATH);

        $cardPayGatewayMode = new CardPayGatewayMode();
        $cardPayGatewayMode
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
            ->setNote($note)
            ->setSuccessUrl($success_url)
            ->setDeclineUrl($decline_url)
            ->setReturnUrl($return_url);

        $card = new CardPayCard();
        $card
            ->setCardNumber($card_data["card_number"])
            ->setCardholderName($card_data["cardholder_name"])
            ->setExpirationDate($card_data["expiration_date_year"], $card_data["expiration_date_month"])
            ->setCvc($card_data["cvc"]);

        $expiration_date = implode("/", [
            substr("0" . $card_data["expiration_date_month"], -2),
            substr("20" . $card_data["expiration_date_year"], -4)
        ]);

        $cardPayGatewayMode->setCard($card);

        $billing_address = new CardPayAddress();
        $billing_address
            ->setCountry($billing_data["country"])
            ->setState($billing_data["state"])
            ->setZip($billing_data["zip"])
            ->setCity($billing_data["city"])
            ->setStreet($billing_data["street"])
            ->setPhone($billing_data["phone"]);

        $cardPayGatewayMode->setBilling($billing_address);

        $shipping_address = new CardPayAddress();
        $shipping_address
            ->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $cardPayGatewayMode->setShipping($shipping_address);

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

        $cardPayGatewayMode->setItems($items);

        $walletId = CardPayTestHelper::WALLET_ID;

        $customer_id = CardPayTestHelper::normalizeString($customer_id);

        $billing_data["city"] = CardPayTestHelper::normalizeString($billing_data["city"]);
        $billing_data["street"] = CardPayTestHelper::normalizeString($billing_data["street"]);

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
            . ">"
            ."<card num=\"{$card_data["card_number"]}\" holder=\"{$card_data["cardholder_name"]}\" expires=\"{$expiration_date}\" cvv=\"{$card_data["cvc"]}\"></card>"
            ."<billing country=\"{$billing_data["country"]}\" state=\"{$billing_data["state"]}\" zip=\"{$billing_data["zip"]}\" city=\"{$billing_data["city"]}\" street=\"{$billing_data["street"]}\" phone=\"{$billing_data["phone"]}\"></billing>"
            ."<shipping country=\"{$shipping_data["country"]}\" state=\"{$shipping_data["state"]}\" zip=\"{$shipping_data["zip"]}\" city=\"{$shipping_data["city"]}\" street=\"{$shipping_data["street"]}\" phone=\"{$shipping_data["phone"]}\"></shipping>"
            ."<items>"
            ."<item name=\"{$item_list[0]["name"]}\" description=\"{$item_list[0]["description"]}\" count=\"{$item_list[0]["count"]}\" price=\"{$item_list[0]["price"]}\"></item>"
            ."<item name=\"{$item_list[1]["name"]}\" description=\"{$item_list[1]["description"]}\" count=\"{$item_list[1]["count"]}\" price=\"{$item_list[1]["price"]}\"></item>"
            ."</items>"
            ."</order>";

        $this->assertXmlStringEqualsXmlString($expected, $cardPayGatewayMode->getOrderXML());
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
                $faker->text(100),
                $faker->url,
                $faker->url,
                $faker->url,
                [
                    "card_number" => $faker->creditCardNumber,
                    "cardholder_name" => $faker->name,
                    "expiration_date_year" => $faker->year,
                    "expiration_date_month" => $faker->month,
                    "cvc" => $faker->regexify('[0-9]{3,3}')
                ],
                [
                    "country" => $faker->countryCode,
                    "state" => $faker->state,
                    "zip" => $faker->postcode,
                    "city" => substr($faker->city, 0, 20),
                    "street" => $faker->streetAddress,
                    "phone" => $faker->e164PhoneNumber,
                ],
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
        $note,
        $return_url,
        $card_data = array(),
        $billing_data = array(),
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

        $cardPayGatewayMode = new CardPayGatewayMode();
        $cardPayGatewayMode
            ->setConfig($cardPayConfig)
            ->setOrderId($order_id)
            ->setDescription($description)
            ->setCurrency($currency)
            ->setAmount($amount)
            ->setEmail($email)
            ->setCustomerId($customer_id)
            ->setNote($note)
            ->setReturnUrl($return_url);

        $card = new CardPayCard();
        $card
            ->setCardNumber($card_data["card_number"])
            ->setCardholderName($card_data["cardholder_name"])
            ->setExpirationDate($card_data["expiration_date_year"], $card_data["expiration_date_month"])
            ->setCvc($card_data["cvc"]);

        $cardPayGatewayMode->setCard($card);

        $billing_address = new CardPayAddress();
        $billing_address
            ->setCountry($billing_data["country"])
            ->setState($billing_data["state"])
            ->setZip($billing_data["zip"])
            ->setCity($billing_data["city"])
            ->setStreet($billing_data["street"])
            ->setPhone($billing_data["phone"]);

        $cardPayGatewayMode->setBilling($billing_address);

        $shipping_address = new CardPayAddress();
        $shipping_address
            ->setCountry($shipping_data["country"])
            ->setState($shipping_data["state"])
            ->setZip($shipping_data["zip"])
            ->setCity($shipping_data["city"])
            ->setStreet($shipping_data["street"])
            ->setPhone($shipping_data["phone"]);

        $cardPayGatewayMode->setShipping($shipping_address);

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

        $cardPayGatewayMode->setItems($items);

        $this->assertEquals($order_xml_base64, $cardPayGatewayMode->getOrderXML(true));
        $this->assertEquals($order_xml_sha512, $cardPayGatewayMode->getSHA512());
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
                "Qui libero animi ratione qui alias. Voluptatibus quasi est dicta. Sit aliquam cum ex rerum et cum.",
                "https://greenholt.com/sapiente-et-ratione-fuga-sit-in-voluptas-distinctio",
                [
                    "card_number" => "6011563329486311",
                    "cardholder_name" => "Mr. Jameson Feil",
                    "expiration_date_year" => "2020",
                    "expiration_date_month" => "09",
                    "cvc" => "057"
                ],
                [
                    "country" => "CX",
                    "state" => "Alaska",
                    "zip" => "63145",
                    "city" => "Port Yvonneview",
                    "street" => "75234 Schulist Valley Apt. 320\nWest Asia, MD 06676-6523",
                    "phone" => "+1659349328616",
                ],
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
                "PG9yZGVyIHdhbGxldF9pZD0iMTAwMCIgbnVtYmVyPSJkZHdvOGRtIiBkZXNjcmlwdGlvbj0iRXN0IHRvdGFtIHF1aWJ1c2RhbSBjdWxwYSBkb2xvcmVtIGFyY2hpdGVjdG8gdm9sdXB0YXRlbSIgY3VycmVuY3k9IlVTRCIgYW1vdW50PSI3ODEwNzAuODMiIGVtYWlsPSJqbWVkaHVyc3RAaG90bWFpbC5jb20iIGN1c3RvbWVyX2lkPSJFbGVub3JhIFdhbHRlciIgbm90ZT0iUXVpIGxpYmVybyBhbmltaSByYXRpb25lIHF1aSBhbGlhcy4gVm9sdXB0YXRpYnVzIHF1YXNpIGVzdCBkaWN0YS4gU2l0IGFsaXF1YW0gY3VtIGV4IHJlcnVtIGV0IGN1bS4iIHJldHVybl91cmw9Imh0dHBzOi8vZ3JlZW5ob2x0LmNvbS9zYXBpZW50ZS1ldC1yYXRpb25lLWZ1Z2Etc2l0LWluLXZvbHVwdGFzLWRpc3RpbmN0aW8iPjxjYXJkIG51bT0iNjAxMTU2MzMyOTQ4NjMxMSIgaG9sZGVyPSJNci4gSmFtZXNvbiBGZWlsIiBleHBpcmVzPSIwOS8yMDIwIiBjdnY9IjA1NyI+PC9jYXJkPjxiaWxsaW5nIGNvdW50cnk9IkNYIiBzdGF0ZT0iQWxhc2thIiB6aXA9IjYzMTQ1IiBjaXR5PSJQb3J0IFl2b25uZXZpZXciIHN0cmVldD0iNzUyMzQgU2NodWxpc3QgVmFsbGV5IEFwdC4gMzIwIFdlc3QgQXNpYSwgTUQgMDY2NzYtNjUyMyIgcGhvbmU9IisxNjU5MzQ5MzI4NjE2Ij48L2JpbGxpbmc+PHNoaXBwaW5nIGNvdW50cnk9IktZIiBzdGF0ZT0iVGVubmVzc2VlIiB6aXA9IjAyMjA1LTEwODQiIGNpdHk9Ikthc3NhbmRyYWNoZXN0ZXIiIHN0cmVldD0iNzQzOTcgU2lncmlkIExvYWYgSmFycmV0dGJlcmcsIFNEIDU5MjM4IiBwaG9uZT0iKzg4ODkyODYyNzQ3OTUiPjwvc2hpcHBpbmc+PGl0ZW1zPjxpdGVtIG5hbWU9Ik1hZ25pIGF1dCBuZWNlc3NpdGF0aWJ1cyBhdXRlbSBlc3QuIiBkZXNjcmlwdGlvbj0iRG9sb3JlcyBhdXRlbSBtYXhpbWUgZXQgYXQuIEluIG1hZ25hbSBxdWkgbmFtIHF1b3MgaXVzdG8gZG9sb3Jlcy4gRG9sb3IgZXQgdm9sdXB0YXRlcyBldCBldCB2ZWwgc2VkLiBVdCBuYW0gbW9sZXN0aWFlIG5lc2NpdW50IGFkaXBpc2NpLiBVdCB2b2x1cHRhcyBldW0gYWQgYWxpcXVpZC4iIGNvdW50PSI2IiBwcmljZT0iNjY4NTQxLjg2Ij48L2l0ZW0+PGl0ZW0gbmFtZT0iVm9sdXB0YXRlbSBlb3MgYXV0ZW0gdmVuaWFtIGRvbG9yIGV0IG9jY2FlY2F0aS4iIGRlc2NyaXB0aW9uPSJFdCBhbmltaSB2b2x1cHRhcyBlYXF1ZSBuZXF1ZSBhcmNoaXRlY3RvLiBFdCBxdWFzaSBhY2N1c2FtdXMgdm9sdXB0YXRlIGVhIHF1aWRlbS4gSWxsdW0gY3VtIGFjY3VzYW50aXVtIG9wdGlvIGVuaW0gcmVwZWxsZW5kdXMuIFBlcnNwaWNpYXRpcyB1dCB2b2x1cHRhdGlidXMgY3VscGEgYXV0IGlzdGUgZGlzdGluY3RpbyBxdWlzLiIgY291bnQ9IjIiIHByaWNlPSIyMTk0LjA0Ij48L2l0ZW0+PC9pdGVtcz48L29yZGVyPg==",
                "e128652b6ec11cc815e8a18acc0bd661afc0819ef29b906647a5d4f2df76e1a9f2bb365eb7e7833c84139522f7cc45dae1c27aa50886552e800a8234e802f14e",
            ],
        ];
    }


}
