<?php
//Cardpay merchant's account settings
return array(
    'wallet_id' => '1111',//required
    'secret_word' => 'secret',//required for CardpayAPI
    'url' => 'https://cardpay.com/MI/cardpayment.html',//required for CardpayAPI
    'return_url' => 'http://mysite.com/cardpay_3ds_return.php',//required for some 3ds transactions
    //transaction report settings
    'client_login' => 'username',//required for CardpayReport
    'client_password' => 'password',//required for CardpayReport
    'report_url' => 'https://cardpay.com/MI/service/order-report',//required for CardpayReport
);
