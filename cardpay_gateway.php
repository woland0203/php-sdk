<?php
/**
 * Sample Cardpay API call using gateway payment settings
 */

require_once 'Cardpay/CardpayAPI.php';
$config = require 'Cardpay/config.php';

//order settings
$order = array(
    'wallet_id'=>$config['wallet_id'],
    'number'=>'1234',
    'amount'=>'1523.45',
    'email'=>'admin@myshop.com',
    'is_gateway'=>'1',
    'ip'=>'10.20.30.40',
    'description'=>'sample description',//optional
    'is_two_phase'=>'0',//optional
    //'currency'=>'USD',//optional
    'locale'=>'en',//optional
    //'is_deposit'=>'0',//optional

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

    'card'=>array(
        'num'=>'4000000000000000',
        'holder'=>'John Silver',
        'cvv'=>'785',//optional if is_deposit = 1
        'expires'=>'09.2016',
    ),

    'billing'=>array(
        'country'=>'USA',
        'state'=>'NY',
        'city'=>'New York',
        'zip'=>'10001',
        'street'=>'450 W. 33 Street',
        'phone'=>'34634562354',
    ),
);

//create API object
$cardpayApi = new CardpayAPI($config, $order);
//call payment url and get payment result
$result = $cardpayApi->getewayPayment();
//echo result
echo htmlspecialchars($result);
