<?php

$options = get_option('wpepdd_settingsoptions');
foreach ($options as $k => $v ) { $value[$k] = $v; }

$hash = $value['hash'];

global $USE_PROXY;
global $version;
global $API_UserName;
global $API_Password;
global $API_Signature;
global $sBNCode;

global $API_Endpoint;
global $PAYPAL_URL;
global $PAYPAL_DG_URL;

$API_UserName = "";
$API_Password = "";
$API_Signature = "";

if ($value['mode'] == "1") {
$SandboxFlag = true;
$API_UserName = $value['sandbox_api_username'];
$API_Password = $value['sandbox_api_password'];
$API_Signature = $value['sandbox_api_signature'];
} else {
$SandboxFlag = false;
$API_UserName = $value['api_username'];
$API_Password = $value['api_password'];
$API_Signature = $value['api_signature'];
}



$sBNCode = "WPPlugin_SP";

if ($SandboxFlag == true) 
{
$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
$PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
$PAYPAL_DG_URL = "https://www.sandbox.paypal.com/incontext?token=";
} else {
$API_Endpoint = "https://api-3t.paypal.com/nvp";
$PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
$PAYPAL_DG_URL = "https://www.paypal.com/incontext?token=";
}

$USE_PROXY = false;
$version = "84";










function wpepdd_SetExpressCheckoutDG( $paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items, $language, $url) {
$nvpstr = "&PAYMENTREQUEST_0_AMT=". $paymentAmount;
$nvpstr .= "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
$nvpstr .= "&RETURNURL=" . $returnURL;
$nvpstr .= "&CANCELURL=" . $cancelURL;
$nvpstr .= "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
$nvpstr .= "&REQCONFIRMSHIPPING=0";
$nvpstr .= "&NOSHIPPING=1";
$nvpstr .= "&PAYMENTREQUEST_0_CUSTOM=" . $url;
$nvpstr .= "&LOCALECODE=" . $language;


foreach ($items as $index => $item) {

$nvpstr .= "&L_PAYMENTREQUEST_0_NAME" . $index . "=" . urlencode($item["name"]);
$nvpstr .= "&L_PAYMENTREQUEST_0_AMT" . $index . "=" . urlencode($item["amt"]);
$nvpstr .= "&L_PAYMENTREQUEST_0_QTY" . $index . "=" . urlencode($item["qty"]);
$nvpstr .= "&L_PAYMENTREQUEST_0_ITEMCATEGORY" . $index . "=Digital";
}

$resArray = wpepdd_hash_call("SetExpressCheckout", $nvpstr);


if (isset($resArray["ACK"])) { $ack = $resArray["ACK"]; } else { $ack = ""; }


$ack = strtoupper($ack);
if($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING")
{
$token = urldecode($resArray["TOKEN"]);
$_SESSION['TOKEN'] = $token;
}

return $resArray;
}













function wpepdd_GetExpressCheckoutDetails( $token ) {
$nvpstr="&TOKEN=" . $token;
$resArray=wpepdd_hash_call("GetExpressCheckoutDetails",$nvpstr);
$ack = strtoupper($resArray["ACK"]);
if($ack == "SUCCESS" || $ack=="SUCCESSWITHWARNING")
{	
return $resArray;
} 
else return false;

}












function wpepdd_ConfirmPayment( $token, $paymentType, $currencyCodeType, $payerID, $FinalPaymentAmt, $items ) {
$token 				= urlencode($token);
$paymentType 		= urlencode($paymentType);
$currencyCodeType 	= urlencode($currencyCodeType);
$payerID 			= urlencode($payerID);
$serverName 		= urlencode($_SERVER['SERVER_NAME']);

$nvpstr  = '&TOKEN=' . $token . '&PAYERID=' . $payerID . '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymentType . '&PAYMENTREQUEST_0_AMT=' . $FinalPaymentAmt;
$nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . $currencyCodeType . '&IPADDRESS=' . $serverName; 

foreach($items as $index => $item) {

$nvpstr .= "&L_PAYMENTREQUEST_0_NAME" . $index . "=" . urlencode($item["name"]);
$nvpstr .= "&L_PAYMENTREQUEST_0_AMT" . $index . "=" . urlencode($item["amt"]);
$nvpstr .= "&L_PAYMENTREQUEST_0_QTY" . $index . "=" . urlencode($item["qty"]);
$nvpstr .= "&L_PAYMENTREQUEST_0_ITEMCATEGORY" . $index . "=Digital";
}

$resArray=wpepdd_hash_call("DoExpressCheckoutPayment",$nvpstr);
$ack = strtoupper($resArray["ACK"]);

return $resArray;
}












function wpepdd_hash_call($methodName,$nvpStr) {
global $API_Endpoint, $version, $API_UserName, $API_Password, $API_Signature;
global $gv_ApiErrorURL;
global $sBNCode;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
curl_setopt($ch, CURLOPT_VERBOSE, 1);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1);


$nvpreq="METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($version) . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpStr . "&BUTTONSOURCE=" . urlencode($sBNCode);

curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

$response = curl_exec($ch);

$nvpResArray=wpepdd_deformatNVP($response);
$nvpReqArray=wpepdd_deformatNVP($nvpreq);
$_SESSION['nvpReqArray']=$nvpReqArray;

if (curl_errno($ch)) 
{
$_SESSION['curl_error_no']=curl_errno($ch) ;
$_SESSION['curl_error_msg']=curl_error($ch);
} 
else 
{
curl_close($ch);
}

return $nvpResArray;
}















function wpepdd_RedirectToPayPal ( $token ) {
global $PAYPAL_URL;
$payPalURL = $PAYPAL_URL . $token;
header("Location: ".$payPalURL);
exit;
}








function wpepdd_RedirectToPayPalDG ( $token ) {
global $PAYPAL_DG_URL;

$payPalURL = $PAYPAL_DG_URL . $token;
header("Location: ".$payPalURL);
exit;
}








function wpepdd_deformatNVP($nvpstr) {
$intial=0;
$nvpArray = array();

while(strlen($nvpstr))
{

$keypos= strpos($nvpstr,'=');

$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

$keyval=substr($nvpstr,$intial,$keypos);
$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);

$nvpArray[urldecode($keyval)] =urldecode( $valval);
$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
}
return $nvpArray;
}







?>