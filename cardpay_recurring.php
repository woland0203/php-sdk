<?php
/**
 * Sample Cardpay API call using recurring payment settings
 */

require_once 'Cardpay/CardpayAPI.php';
$config = require 'Cardpay/config.php';

//order settings
$order = array(
    'wallet_id'=>$config['wallet_id'],
    'number'=>'1234',
    'amount'=>'1523.45',
    'email'=>'admin@myshop.com',
    'description'=>'sample description',//optional
    'is_two_phase'=>'0',//optional
    //'currency'=>'USD',//optional
    'locale'=>'en',//optional

    'items'=>array( //optional
        array( //item1
            'name'=>'prod1',
            'description'=>'prod1 description',//optional
            'count'=>'2',//optional
            'price'=>'10.24',//optional
        ),
        array( //item2 //optional
            'name'=>'prod2',
            'description'=>'prod2 description',//optional
            'count'=>'5',//optional
            'price'=>'0.56',//optional
        ),
        // ... more items
    ),

    'shipping'=>array( //optional
        'country'=>'USA', //optional
        'state'=>'NY', //optional
        'city'=>'New York', //optional
        'zip'=>'10001', //optional
        'street'=>'450 W. 33 Street', //optional
        'phone'=>'34634562354', //optional
    ),

    'recurring'=>array(
        'period'=>'30',
        'price'=>'912.69',//optional
        //'begin'=>'14.10.2012',//optional
        'count'=>'10',//optional
    ),
);

//create API object
$cardpayApi = new CardpayAPI($config, $order);

//html form template
$template = <<<EOT
<form method="post" action="%cardpay_url%">
    <input type="hidden" name="orderXML" value="%cardpay_orderxml%">
    <input type="hidden" name="sha512" value="%cardpay_sha512%">
    <input type="submit" value="PAY BY CARDPAY">
</form>
EOT;

//echo html form
echo $cardpayApi->paymentForm($template);
