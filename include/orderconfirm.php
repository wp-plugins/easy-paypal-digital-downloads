<?php

require_once ("paypalfunctions.php");

$PaymentOption = "PayPal";
if ( $PaymentOption == "PayPal" ) {

$tok = sanitize_text_field($_REQUEST['token']);

$res = wpepdd_GetExpressCheckoutDetails ( $tok );
$finalPaymentAmount =  $res["PAYMENTREQUEST_0_AMT"];
$url =  $res["PAYMENTREQUEST_0_CUSTOM"];
$token 				=  sanitize_text_field($_REQUEST['token']);
$payerID 			=  sanitize_text_field($_REQUEST['PayerID']);
$paymentType 		= 'Sale';
$currencyCodeType 	= $res['CURRENCYCODE'];
$items = array();
$i = 0;

while (isset($res["L_PAYMENTREQUEST_0_NAME$i"])) {
$items[] = array('name' => $res["L_PAYMENTREQUEST_0_NAME$i"], 'amt' => $res["L_PAYMENTREQUEST_0_AMT$i"], 'qty' => $res["L_PAYMENTREQUEST_0_QTY$i"]);
$i++;
}

$resArray = wpepdd_ConfirmPayment ( $token, $paymentType, $currencyCodeType, $payerID, $finalPaymentAmount, $items, $url );
$ack = strtoupper($resArray["ACK"]);

if ( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" ) {

$transactionId		= $resArray["PAYMENTINFO_0_TRANSACTIONID"];
$transactionType 	= $resArray["PAYMENTINFO_0_TRANSACTIONTYPE"];
$paymentType		= $resArray["PAYMENTINFO_0_PAYMENTTYPE"];
$orderTime 			= $resArray["PAYMENTINFO_0_ORDERTIME"];
$amt				= $resArray["PAYMENTINFO_0_AMT"];
$currencyCode		= $resArray["PAYMENTINFO_0_CURRENCYCODE"];
$feeAmt				= $resArray["PAYMENTINFO_0_FEEAMT"];
$taxAmt				= $resArray["PAYMENTINFO_0_TAXAMT"];
$paymentStatus = $resArray["PAYMENTINFO_0_PAYMENTSTATUS"];
$pendingReason = $resArray["PAYMENTINFO_0_PENDINGREASON"];
$reasonCode	= $resArray["PAYMENTINFO_0_REASONCODE"];

// successful payment



?>
<html>


<script>


<?php
if ($value['notices'] == "1") {
?>
alert("Payment successful");
<?php
}
?>

window.onload = function(){
if(window.opener){
window.close();
} else {
if(top.dg.isOpen() == true){
top.dg.closeFlow();
return true;
}
}
top.window.opener.location = "<?php echo $url; ?>";
};
</script>
</html>
<?php
} else {
$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
echo "DoExpressCheckoutDetails API call failed. ";
echo "Detailed Error Message: " . $ErrorLongMsg;
echo "Short Error Message: " . $ErrorShortMsg;
echo "Error Code: " . $ErrorCode;
echo "Error Severity Code: " . $ErrorSeverityCode;
?>
<html>
<script>
alert("Payment failed");
window.onload = function(){
if(window.opener){
window.close();
}
else{
if(top.dg.isOpen() == true){
top.dg.closeFlow();
return true;
}
}                              
};
</script>
</html>
<?php
}
}
?>