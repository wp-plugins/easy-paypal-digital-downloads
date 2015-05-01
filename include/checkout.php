<?php

require_once ("paypalfunctions.php");
require_once ("encrypt_url.php");

$PaymentOption = "PayPal";

global $paymentAmount;
global $itemName;
global $currencyCodeType;
global $paymentType;

if ( $PaymentOption == "PayPal") {

$paymentAmount = sanitize_text_field($_POST['amount']);
$itemName = sanitize_text_field($_POST['name']);
$currencyCodeType = sanitize_text_field($_POST['currency']);
$paymentType = "Sale";


$returnURL = get_admin_url() . "admin-post.php?action=submit-form-wpepdd-return";
$cancelURL = get_admin_url() . "admin-post.php?action=submit-form-wpepdd-cancel";



$items = array();
$items[] = array('name' => $itemName, 'amt' => $paymentAmount, 'qty' => 1);
$url = sanitize_text_field($_POST['url']);
$language = sanitize_text_field($_POST['language']);


if (!isset($url)) {
echo "No URL has been specified for this button. Please include the URL attributed in the buttons shortcode.";
exit;
}



$code = new Encryption;
$url = $code->wpepdd_decode($url,$hash);




$resArray = wpepdd_SetExpressCheckoutDG( $paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items, $language, $url );


if (isset($resArray["ACK"])) { $ack = $resArray["ACK"]; } else { $ack = ""; }
$ack = strtoupper($ack);

if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
$token = urldecode($resArray["TOKEN"]);
wpepdd_RedirectToPayPalDG( $token );

} else {

if (isset($resArray["L_ERRORCODE0"])) { $acka = $resArray["L_ERRORCODE0"]; } else { $acka = ""; }
if (isset($resArray["L_SHORTMESSAGE0"])) { $ackb = $resArray["L_SHORTMESSAGE0"]; } else { $ackb = ""; }
if (isset($resArray["L_LONGMESSAGE0"])) { $ackc = $resArray["L_LONGMESSAGE0"]; } else { $ackc = ""; }
if (isset($resArray["L_SEVERITYCODE0"])) { $ackd = $resArray["L_SEVERITYCODE0"]; } else { $ackd = ""; }


$ErrorCode = urldecode($acka);
$ErrorShortMsg = urldecode($ackb);
$ErrorLongMsg = urldecode($ackc);
$ErrorSeverityCode = urldecode($ackd);

echo "<br />Error - Double check your Business API settings. <br /><br />SetExpressCheckout API call failed.<br /><br />";
echo "Detailed Error Message: " . $ErrorLongMsg ."<br /><br />";
echo "Short Error Message: " . $ErrorShortMsg ."<br /><br />";
echo "Error Code: " . $ErrorCode ."<br /><br />";
echo "Error Severity Code: " . $ErrorSeverityCode ."<br /><br />";
}
}

?>