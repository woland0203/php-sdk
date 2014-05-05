I. How to use CardpayAPI class:
(see examples in cardpay_standard.php, cardpay_recurring.php, cardpay_redirect.php and cardpay_gateway.php files)

1. Set the Cardpay merchant's account settings in Cardpay/config.php file.

2. Add the following code to your php file:

require_once 'Cardpay/CardpayAPI.php';
$config = require 'Cardpay/config.php';

3. Prepare $order variable - the array with the order parameters.

4. Add the following code to your php file:

//create API object
$cardpayApi = new CardpayAPI($config, $order);


5. For redirect to payment page add the following code to your php file:
------------------------------------------------------------------------
//redirect to payment page
$cardpayApi->redirectToPaymentPage();

OR

5. For gateway payment add the following code to your php file:
---------------------------------------------------------------
//call payment url and get payment result
$result = $cardpayApi->getewayPayment();

OR

5. For HTML form generation:
----------------------------
- Create $template variable - the HTML form template.
- Add the following code to your php file:

//echo html form
echo $cardpayApi->paymentForm($template);


Additional API functions that are available:

//get order XML
$orderXML = $cardpayApi->getOrderXML();

//get order XML encoded
$orderXMLencoded = $cardpayApi->getOrderXMLencoded($orderXML);

//get SHA512
$sha512 = $cardpayApi->getSHA512($orderXML);

------------------------------------------------------------------------
------------------------------------------------------------------------

II. 3ds transactions workflow.
(see examples in cardpay_3ds.php and cardpay_3ds_return.php files)

On start page (cardpay_3ds.php):
--------------------------------

1. Set the Cardpay merchant's account settings in Cardpay/config.php file.

2. Add the following code to your php file:

require_once 'Cardpay/CardpayAPI.php';
$config = require 'Cardpay/config.php';

3. Prepare $order variable - the array with the order parameters.

4. Add the following code to your php file:

//create API object
$cardpayApi = new CardpayAPI($config, $order);

//call payment url and get payment result
$result = $cardpayApi->getewayPayment();

//get result as an array
$result_array = CardpayAPI::getXMLAsArray($result);

5. Check result of initial call.

//check if transaction should be processed as 3ds or not
if (!empty($result_array['redirect']))

If transaction should be processed in 3ds mode:
------------------------------------------
6. Generate HTML form:
- Create $template variable - the HTML form template.
- Add the following code to your php file:

//echo html form
echo $cardpayApi->get3dsForm($result_array,$template);

On return page (cardpay_3ds_return.php):
----------------------------------------

1. Set the Cardpay merchant's account settings in Cardpay/config.php file.

2. Add the following code to your php file:

require_once 'Cardpay/CardpayAPI.php';
$config = require 'Cardpay/config.php';

3. Collect posted variables to $params array.

//prepare params
$params = array(
    'MD' => $_POST['MD'],
    'PaRes' => $_POST['PaRes'],
);

4. Add the following code to your php file:

//create API object
$cardpayApi = new CardpayAPI($config, $params);

//call Cardpay and get payment result
$result = $cardpayApi->final3ds($params);

------------------------------------------------------------------------
------------------------------------------------------------------------

III. How to use CardpayReport class:
(see examples in cardpay_report.php file)

1. Set the Cardpay merchant's account settings in Cardpay/config.php file.

2. Add the following code to your php file:

require_once 'Cardpay/CardpayReport.php';
$config = require 'Cardpay/config.php';

3. For last 10 transactions add the following code to your php file:
--------------------------------------------------------------------
//get last transactions
$result = $cardpayReport->getReport();

OR

3. For custom report pass config parameters to getReport():
-----------------------------------------------------------
//get transactions for period range
$result = $cardpayReport->getReport(
    array(
        'date_begin'=>'2010-01-01 00:02',//optional
        'date_end'=>'2011-01-01 03:04',//optional
        'currency'=>'',//optional
        'order_number'=>'',//optional
    )
);