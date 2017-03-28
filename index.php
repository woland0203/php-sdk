<?php
require_once __DIR__ . '/lib/bootstrap.php';

switch ($_GET["flow"]) {
    case "payment-page-mode":
        require_once __DIR__ . '/sample/paymentPageMode.php';
        exit;
    case "gateway-mode":
        require_once __DIR__ . '/sample/gatewayMode.php';
        exit;
    case "change-order-status-capture":
        require_once __DIR__ . '/sample/changeOrderStatusCapture.php';
        exit;
    case "change-order-status-void":
        require_once __DIR__ . '/sample/changeOrderStatusVoid.php';
        exit;
    case "change-order-status-refund":
        require_once __DIR__ . '/sample/changeOrderStatusRefund.php';
        exit;
    case "payments-report":
        require_once __DIR__ . '/sample/paymentsReport.php';
        exit;
    case "refunds-report":
        require_once __DIR__ . '/sample/refundsReport.php';
        exit;
    case "payment-report":
        require_once __DIR__ . '/sample/paymentReport.php';
        exit;
    case "refund-report":
        require_once __DIR__ . '/sample/refundReport.php';
        exit;
}

?>

<a href="/index.php?flow=payment-page-mode">Payment page mode</a>
<br>
<a href="/index.php?flow=gateway-mode">Gateway mode</a>
<br>
<a href="/index.php?flow=change-order-status-capture">Change Order Status to Capture</a>
<br>
<a href="/index.php?flow=change-order-status-void">Change Order Status to Void</a>
<br>
<a href="/index.php?flow=change-order-status-refund">Change Order Status to Refund</a>
<br>
<a href="/index.php?flow=payments-report">List Payments Report</a>
<br>
<a href="/index.php?flow=refunds-report">List Refunds Report</a>
<br>
<a href="/index.php?flow=payment-report">One Payment Report</a>
<br>
<a href="/index.php?flow=refund-report">One Refund Report</a>