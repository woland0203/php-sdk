<?php
/**
 * Sample 3ds return page
 */

require_once 'Cardpay/CardpayAPI.php';
$config = require 'Cardpay/config.php';

if (!empty($_POST['MD']) && !empty($_POST['PaRes'])) {

    //prepare params
    $params = array(
        'MD' => $_POST['MD'],
        'PaRes' => $_POST['PaRes'],
    );

    //create API object
    $cardpayApi = new CardpayAPI($config, $params);
    //call Cardpay and get payment result
    $result = $cardpayApi->final3ds($params);
    //echo result
    echo htmlspecialchars($result);

} else {

    //bad request
    echo 'Error 400: Bad request';

}
