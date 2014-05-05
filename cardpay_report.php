<?php
/**
 * Sample Cardpay Report call
 */

require_once 'Cardpay/CardpayReport.php';
$config = require 'Cardpay/config.php';

//create Report object
$cardpayReport = new CardpayReport($config);

//get last transactions
$result = $cardpayReport->getReport();
//echo result
echo htmlspecialchars($result).'<br><br>';

//get transactions for period range
$result = $cardpayReport->getReport(
    array(
        'date_begin'=>'2010-01-01 00:02',//optional
        'date_end'=>'2011-01-01 03:04',//optional
        'currency'=>'',//optional
        'order_number'=>'',//optional
    )
);
//echo result
echo htmlspecialchars($result).'<br><br>';
