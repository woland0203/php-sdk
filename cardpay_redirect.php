<?php
/**
 * Sample Cardpay API call using standard payment settings and redirect to payment page
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
);

//create API object
$cardpayApi = new CardpayAPI($config, $order);
//redirect to payment page
$cardpayApi->redirectToPaymentPage();
