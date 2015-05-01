<?php

/*
Plugin Name: Easy PayPal Digital Downloads
Plugin URI: https://wpplugin.org/easy-paypal-digital-downloads/
Description: A simple and easy way to sell digital goods on your WordPress website.
Tags: digital downloads, digital goods, goods, digital download, digital good, easy paypal, shopping cart, buy now, store, shop, downloads, PayPal payment, PayPal, button, payment, online payments, Stripe, Super Stripe, Stripe checkout, pay now, paypal plugin for wordpress, button, paypal button, payment form, pay online, paypal buy now, ecommerce, paypal plugin, shortcode, online, payments, payments for wordpress, paypal for wordpress, paypal donation, paypal transfer, payment processing, paypal widget, wp paypal, purchase button, money, invoice, invoicing, payment collect, secure, process credit cards, paypal integration, gateway, stripe, authorize, shopping cart, cart, shopping, shop
Author: Scott Paterson
Author URI: https://wpplugin.org
License: GPL2
Version: 1.2
*/

/*  Copyright 2014-2015 Scott Paterson

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/





global $pagenow, $typenow;


// add media button for editor page
if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) && $typenow != 'download' ) {

add_action('media_buttons', 'wpepdd_add_my_media_button', 20);
function wpepdd_add_my_media_button() {
    echo '<a href="#TB_inline?width=600&height=400&inlineId=wpepdd_popup_container" title="Easy PayPal Digital Downloads" id="insert-my-media" class="button thickbox">PayPal Digital Downloads</a>';
}

add_action( 'admin_footer',  'wpepdd_add_inline_popup_content' );
function wpepdd_add_inline_popup_content() {
?>



<script type="text/javascript">
function wpepdd_InsertShortcode(){

var wpepdd_scnamea = document.getElementById("wpepdd_scnamea").value;
var wpepdd_scpricea = document.getElementById("wpepdd_scpricea").value;
var wpepdd_imagea = document.getElementById("wpepdd_imagea").value;
var wpepdd_alignmentca = document.getElementById("wpepdd_alignmenta");
var wpepdd_alignmentba = wpepdd_alignmentca.options[wpepdd_alignmentca.selectedIndex].value;

if(!wpepdd_scnamea.match(/\S/)) { alert("Item Name is required."); return false; }
if(!wpepdd_scpricea.match(/\S/)) { alert("Item Price is required."); return false; }
if(!wpepdd_imagea.match(/\S/)) { alert("Item Link is required."); return false; }
if(wpepdd_alignmentba == "none") { var wpepdd_alignmenta = ""; } else { var wpepdd_alignmenta = ' align="' + wpepdd_alignmentba + '"'; };

document.getElementById("wpepdd_scnamea").value = "";
document.getElementById("wpepdd_scpricea").value = "";
document.getElementById("wpepdd_imagea").value = "";
wpepdd_alignmentca.selectedIndex = null;

window.send_to_editor('[wpepdd name="' + wpepdd_scnamea + '" price="' + wpepdd_scpricea + '"' + ' url="' + wpepdd_imagea + '"' + wpepdd_alignmenta + ']');
}
</script>






<div id="wpepdd_popup_container" style="display:none;">

<h2>Insert a Buy Now Button</h2>

<table><tr><td>

Item Name: </td><td><input size="40" type="text" name="wpepdd_scnamea" id="wpepdd_scnamea" value=""> (Required)</td><td></td></tr><tr><td>
Link to Item: </td><td><input size="40" type="text" name="wpepdd_imagea" id="wpepdd_imagea" value=""> (Required)</td><td></td></tr><tr><td>
Item Price: </td><td><input size="10" type="text" name="wpepdd_scpricea" id="wpepdd_scpricea" value=""> (Required - Format: 4.99)</td><td></td></tr><tr><td>
Alignment: </td><td><select name="wpepdd_alignmenta" id="wpepdd_alignmenta"><option value="none"></option><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select> 
(Optional)</td><td></td></tr><tr><td>

</td></tr><tr><td>

<br />
</td></tr><tr><td colspan="3">

<input type="button" id="wpepdd-insert" class="button-primary" onclick="wpepdd_InsertShortcode();" value="Insert">

<br /><br />

Link to Item: You can enter a URL using the format - http://example.com/path. 

<br /><br />
This URL can be for a website, file, image; anything that you want to redirect your customer to after they have successfully purchased your item.

<br /><br />
You can obtain links to files on your WordPress site by going to Media -> <a target="_blank" href="upload.php">Library</a> -> Pick an Attchment -> URL.

</td></tr></table>


</div>
<?php
}
}











// variables
// plugin_prefix 	  = wpepdd
// shortcode 		  = wpepdd
// plugin_name 		  = WPeasypaypaldigitaldownload
// plugin_page 		  = easy-paypal-digital-download
// menu_page 		  = Digital Downloads
// WPPlugin url path  = easy-paypal-digital-download
// WordPress url path = easy-paypal-digital-download


// plugin functions

register_activation_hook( __FILE__, "wpepdd_activate" );
register_deactivation_hook( __FILE__, "wpepdd_deactivate" );
register_uninstall_hook( __FILE__, "wpepdd_uninstall" );

function wpepdd_activate() {

// generate unique hash for passing URL rendered in shortcode

$uniquehash = md5(rand());

// initial settings
$wpepdd_settingsoptions = array(
'currency'    => '25',
'language'    => '3',
'mode'    => '2',
'size'    => '2',
'opens'    => '2',
'notices'    => '1',
'hash'		=> $uniquehash,
'api_username'		=> '',
'api_password'		=> '',
'api_signature'		=> '',
'sandbox_api_username'		=> '',
'sandbox_api_password'		=> '',
'sandbox_api_signature'		=> ''
);

add_option("wpepdd_settingsoptions", $wpepdd_settingsoptions);

}


function wpepdd_deactivate() {
delete_option("wpepdd_my_plugin_notice_shown");
}


function wpepdd_uninstall() {
}






// display activation notice

add_action('admin_notices', 'wpepdd_my_plugin_admin_notices');

function wpepdd_my_plugin_admin_notices() {
if (!get_option('wpepdd_my_plugin_notice_shown')) {
echo "<div class='updated'><p><a href='admin.php?page=easy-paypal-digital-download'>Click here to view the plugin settings</a>.</p></div>";
update_option("wpepdd_my_plugin_notice_shown", "true");
}
}






// settings page menu link
add_action( "admin_menu", "wpepdd_plugin_menu" );

function wpepdd_plugin_menu() {
add_options_page( "Easy Digital Downloads", "Digital Downloads", "manage_options", "easy-paypal-digital-download", "wpepdd_plugin_options" );
}
add_filter('plugin_action_links', 'wpepdd_myplugin_plugin_action_links', 10, 2);

function wpepdd_myplugin_plugin_action_links($links, $file) {
static $this_plugin;
if (!$this_plugin) {
$this_plugin = plugin_basename(__FILE__);
}
if ($file == $this_plugin) {
$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-paypal-digital-download">Settings</a>';
array_unshift($links, $settings_link);
}
return $links;
}

function wpepdd_plugin_settings_link($links)
{
unset($links['edit']);

$forum_link   = '<a target="_blank" href="https://wordpress.org/support/plugin/easy-paypal-digital-downloads/">' . __('Support', 'PTP_LOC') . '</a>';
$premium_link = '<a target="_blank" href="https://wpplugin.org/easy-paypal-digital-downloads/">' . __('Purchase Premium', 'PTP_LOC') . '</a>';
array_push($links, $forum_link);
array_push($links, $premium_link);
return $links; 
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wpepdd_plugin_settings_link' );



function wpepdd_plugin_options() {
if ( !current_user_can( "manage_options" ) )  {
wp_die( __( "You do not have sufficient permissions to access this page." ) );
}




// settings page




echo "<table width='100%'><tr><td width='70%'><br />";
echo "<label style='color: #000;font-size:18pt;'><center>Easy PayPal Digital Download Settings</center></label>";
echo "<form method='post' action='".$_SERVER["REQUEST_URI"]."'>";


// save and update options
if (isset($_POST['update'])) {

$options['hash'] = sanitize_text_field($_POST['hash']);
$options['currency'] = sanitize_text_field($_POST['currency']);
$options['language'] = sanitize_text_field($_POST['language']);
$options['api_username'] = sanitize_text_field($_POST['api_username']);
$options['api_password'] = sanitize_text_field($_POST['api_password']);
$options['api_signature'] = sanitize_text_field($_POST['api_signature']);
$options['sandbox_api_username'] = sanitize_text_field($_POST['sandbox_api_username']);
$options['sandbox_api_password'] = sanitize_text_field($_POST['sandbox_api_password']);
$options['sandbox_api_signature'] = sanitize_text_field($_POST['sandbox_api_signature']);
$options['mode'] = sanitize_text_field($_POST['mode']);
$options['size'] = sanitize_text_field($_POST['size']);
$options['notices'] = sanitize_text_field($_POST['notices']);

update_option("wpepdd_settingsoptions", $options);

echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";

}


// get options
$options = get_option('wpepdd_settingsoptions');
foreach ($options as $k => $v ) { $value[$k] = $v; }


echo "</td><td></td></tr><tr><td>";





// form
echo "<br />";
?>

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Usage
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

In a page or post editor you will see a new button called "PayPal Digital Downloads" located right above the text area beside the Add Media button. By using this you can automatically 
create shortcodes which will display as Buy Now buttons on your site.

<br /><br />
<b>Note: </b> There is no limit to the amount of times you can place buttons in a post or a page.


<br /><br />
</div><br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Language & Currency
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<b>Language:</b>
<select name="language">
<option <?php if ($value['language'] == "1") { echo "SELECTED"; } ?> value="1">Danish</option>
<option <?php if ($value['language'] == "2") { echo "SELECTED"; } ?> value="2">Dutch</option>
<option <?php if ($value['language'] == "3") { echo "SELECTED"; } ?> value="3">English</option>
<option <?php if ($value['language'] == "4") { echo "SELECTED"; } ?> value="4">French</option>
<option <?php if ($value['language'] == "5") { echo "SELECTED"; } ?> value="5">German</option>
<option <?php if ($value['language'] == "6") { echo "SELECTED"; } ?> value="6">Hebrew</option>
<option <?php if ($value['language'] == "7") { echo "SELECTED"; } ?> value="7">Italian</option>
<option <?php if ($value['language'] == "8") { echo "SELECTED"; } ?> value="8">Japanese</option>
<option <?php if ($value['language'] == "9") { echo "SELECTED"; } ?> value="9">Norwgian</option>
<option <?php if ($value['language'] == "10") { echo "SELECTED"; } ?> value="10">Polish</option>
<option <?php if ($value['language'] == "11") { echo "SELECTED"; } ?> value="11">Portuguese</option>
<option <?php if ($value['language'] == "12") { echo "SELECTED"; } ?> value="12">Russian</option>
<option <?php if ($value['language'] == "13") { echo "SELECTED"; } ?> value="13">Spanish</option>
<option <?php if ($value['language'] == "14") { echo "SELECTED"; } ?> value="14">Swedish</option>
<option <?php if ($value['language'] == "15") { echo "SELECTED"; } ?> value="15">Simplified Chinese -China only</option>
<option <?php if ($value['language'] == "16") { echo "SELECTED"; } ?> value="16">Traditional Chinese - Hong Kong only</option>
<option <?php if ($value['language'] == "17") { echo "SELECTED"; } ?> value="17">Traditional Chinese - Taiwan only</option>
<option <?php if ($value['language'] == "18") { echo "SELECTED"; } ?> value="18">Turkish</option>
<option <?php if ($value['language'] == "19") { echo "SELECTED"; } ?> value="19">Thai</option>
</select>

PayPal currently supports 18 languages.
<br /><br />

<b>Currency:</b> 
<select name="currency">
<option <?php if ($value['currency'] == "1") { echo "SELECTED"; } ?> value="1">Australian Dollar - AUD</option>
<option <?php if ($value['currency'] == "2") { echo "SELECTED"; } ?> value="2">Brazilian Real - BRL</option> 
<option <?php if ($value['currency'] == "3") { echo "SELECTED"; } ?> value="3">Canadian Dollar - CAD</option>
<option <?php if ($value['currency'] == "4") { echo "SELECTED"; } ?> value="4">Czech Koruna - CZK</option>
<option <?php if ($value['currency'] == "5") { echo "SELECTED"; } ?> value="5">Danish Krone - DKK</option>
<option <?php if ($value['currency'] == "6") { echo "SELECTED"; } ?> value="6">Euro - EUR</option>
<option <?php if ($value['currency'] == "7") { echo "SELECTED"; } ?> value="7">Hong Kong Dollar - HKD</option> 	 
<option <?php if ($value['currency'] == "8") { echo "SELECTED"; } ?> value="8">Hungarian Forint - HUF</option>
<option <?php if ($value['currency'] == "9") { echo "SELECTED"; } ?> value="9">Israeli New Sheqel - ILS</option>
<option <?php if ($value['currency'] == "10") { echo "SELECTED"; } ?> value="10">Japanese Yen - JPY</option>
<option <?php if ($value['currency'] == "11") { echo "SELECTED"; } ?> value="11">Malaysian Ringgit - MYR</option>
<option <?php if ($value['currency'] == "12") { echo "SELECTED"; } ?> value="12">Mexican Peso - MXN</option>
<option <?php if ($value['currency'] == "13") { echo "SELECTED"; } ?> value="13">Norwegian Krone - NOK</option>
<option <?php if ($value['currency'] == "14") { echo "SELECTED"; } ?> value="14">New Zealand Dollar - NZD</option>
<option <?php if ($value['currency'] == "15") { echo "SELECTED"; } ?> value="15">Philippine Peso - PHP</option>
<option <?php if ($value['currency'] == "16") { echo "SELECTED"; } ?> value="16">Polish Zloty - PLN</option>
<option <?php if ($value['currency'] == "17") { echo "SELECTED"; } ?> value="17">Pound Sterling - GBP</option>
<option <?php if ($value['currency'] == "18") { echo "SELECTED"; } ?> value="18">Russian Ruble - RUB</option>
<option <?php if ($value['currency'] == "19") { echo "SELECTED"; } ?> value="19">Singapore Dollar - SGD</option>
<option <?php if ($value['currency'] == "20") { echo "SELECTED"; } ?> value="20">Swedish Krona - SEK</option>
<option <?php if ($value['currency'] == "21") { echo "SELECTED"; } ?> value="21">Swiss Franc - CHF</option>
<option <?php if ($value['currency'] == "22") { echo "SELECTED"; } ?> value="22">Taiwan New Dollar - TWD</option>
<option <?php if ($value['currency'] == "23") { echo "SELECTED"; } ?> value="23">Thai Baht - THB</option>
<option <?php if ($value['currency'] == "24") { echo "SELECTED"; } ?> value="24">Turkish Lira - TRY</option>
<option <?php if ($value['currency'] == "25") { echo "SELECTED"; } ?> value="25">U.S. Dollar - USD</option>
</select>
PayPal currently supports 25 currencies.
<br /><br /></div>

<?php


?>
<br /><br /><div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; PayPal Account </div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

You need to have a PayPal account as well as PayPal Digital Good enabled on your account to use this plugin. 
You can sign up for a PayPal account <a target="_blank" href="https://www.paypal.com"> here</a>. After you have a PayPal account you can enable PayPal for Digital Goods <a target="_blank" href="https://www.paypal.com/webapps/mpp/digital-goods">
here</a>.

<br /><br />

Once you have everything set up, you can obtain your API credientials by following <a target="_blank" href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#creating-classic-api-credentials"> these steps</a>.

<br /><br />

<?php

echo "<table><tr><td>";

echo "<b>Production Settings</b></td></tr><tr><td>";
echo "<b>API Username: </b></td><td><input type='text' name='api_username' value='".$value['api_username']."'> Required </td></tr><tr><td>";
echo "<b>API Password: </b></td><td><input type='text' name='api_password' value='".$value['api_password']."'> Required </td></tr><tr><td>";
echo "<b>API Signature: </b></td><td><input type='text' name='api_signature' value='".$value['api_signature']."'> Required </td></tr><tr><td>";

echo "<br /></td></tr><tr><td colspan='3'>";

?>

You can setup a PayPal Sandbox account for testing if you would like, but it's completely optional. The benefit of it is you can make sure your site is working correctly before going live (production). The PayPal Sandbox works just like the 
real PayPal except for using fake accounts and money. You can make an account by first making a PayPal developers account <a target="_blank" href="https://developer.paypal.com">here</a>.

<br /><br />

Once you have a Developers account, make a business and personal Sandbox account <a target="_blank" href="https://developer.paypal.com/webapps/developer/applications/accounts">here</a>. You can obtain your Sandbox
API credentials in your Sandbox business account details page. Then you can use the Sandbox personal account username and password you created to act as the customer and purchase your items.

<br /><br />

Note: You can double check to make sure you are in Sandbox mode by looking at the PayPal URL address in the popup window after clicking the Buy Now button - it will start with sandbox.paypal.com.

<br /><br />

<?php

echo "<b>Sandbox Settings</b></td></tr><tr><td>";
echo "<b>API Username: </b></td><td><input type='text' name='sandbox_api_username' value='".$value['sandbox_api_username']."'> Optional</td></tr><tr><td>";
echo "<b>API Password: </b></td><td><input type='text' name='sandbox_api_password' value='".$value['sandbox_api_password']."'> Optional</td></tr><tr><td>";
echo "<b>API Signature: </b></td><td><input type='text' name='sandbox_api_signature' value='".$value['sandbox_api_signature']."'> Optional</td></tr><tr><td>";

echo "<br /></td></tr><tr><td>";

echo "<b>Sandbox Mode:</b></td><td>";
echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "1") { echo "checked='checked'"; } echo " type='radio' name='mode' value='1'>On (Sandbox mode)";
echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "2") { echo "checked='checked'"; } echo " type='radio' name='mode' value='2'>Off (Production mode)";

echo "</td></tr></table>";

echo "<br /><br /></div>";



?>

<br /><br />
<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Other Settings
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<?php
echo "<table><tr><td valign='top'>";




echo "<b>Button Size and type:</b></td><td valign='top' style='text-align: center;'>";
echo "<input "; if ($value['size'] == "1") { echo "checked='checked'"; } echo " type='radio' name='size' value='1'>Small <br /><img src='https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif'></td><td valign='top' style='text-align: center;'>";
echo "<input "; if ($value['size'] == "2") { echo "checked='checked'"; } echo " type='radio' name='size' value='2'>Big <br /><img src='https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif'></td><td valign='top' style='text-align: center;'>";
echo "<input "; if ($value['size'] == "3") { echo "checked='checked'"; } echo " type='radio' name='size' value='3'>Big with credit cards <br /><img src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif'></td><td valign='top' style='text-align: center;'>";
echo "<input "; if ($value['size'] == "5") { echo "checked='checked'"; } echo " type='radio' name='size' value='5'>Gold (English Only) <br /><img src='https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png'></td><td valign='top' style='text-align: center;'>";



echo "</td></tr><tr><td><b>Display Notices:</b></td>";
echo "<td><input "; if ($value['notices'] == "1") { echo "checked='checked'"; } echo " type='radio' name='notices' value='1'>Yes</td>";
echo "<td><input "; if ($value['notices'] == "2") { echo "checked='checked'"; } echo " type='radio' name='notices' value='2'>No</td></tr><tr><td>";
echo "<td colspan='4'>Should notices like 'Payment Successful' or 'Payment Cancelled' be displayed to the user.";


echo "</table><br />";


echo "<input type='hidden' name='hash' value='"; echo $value['hash']; echo "'>";

?>
<br /><br /></div>

<input type='hidden' name='update'><br />
<input type='submit' name='btn2' class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;' value='Save Settings'>





<br /><br /><br />


WPPlugin is an offical PayPal Partner. Various trademarks held by their respective owners.


</form>














</td><td width='5%'>
</td><td width='24%' valign='top'>

<br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Professional Version
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;">


<center><label style="font-size:14pt;">With the Pro version you'll <br /> be able to: </label></center>
 
<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Charge Tax <br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Charge Shipping & Handling<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Custom Button Image<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Priority Support<br />

<br />
<center><a target='_blank' href="https://wpplugin.org/easy-paypal-digital-downloads/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Learn More</a></center>
<br />
</div>

<br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Quick Links
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;"><br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wordpress.org/support/plugin/easy-paypal-digital-downloads/">Support Forum</a> <br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/easy-paypal-digital-downloads-support/">FAQ</a> <br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/easy-paypal-digital-downloads/">Digital Downloads Pro</a> <br /><br />

</div>

<br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Like this Plugin?
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;"><br />

<center><a target='_blank' href="https://wordpress.org/plugins/easy-paypal-digital-downloads/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Leave a Review</a></center>
<br />
<center><a target='_blank' href="https://wpplugin.org/donate/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Donate</a></center>
<br />

</div>



</td><td width='1%'>

</td></tr></table>


<?php

// end settings page and required permissions
}







// add to cart shortcode

add_shortcode('wpepdd', 'wpepdd_options');


function wpepdd_options($atts) {

// get shortcode user fields
$atts = shortcode_atts(array('name' => 'Example Name','price' => '0.00','item_id' => '','size' => '','align' => '','url' => ''), $atts);

// get settings page values
$options = get_option('wpepdd_settingsoptions');
foreach ($options as $k => $v ) { $value[$k] = $v; }

// currency
if ($value['currency'] == "1") { $currency = "AUD"; }
if ($value['currency'] == "2") { $currency = "BRL"; }
if ($value['currency'] == "3") { $currency = "CAD"; }
if ($value['currency'] == "4") { $currency = "CZK"; }
if ($value['currency'] == "5") { $currency = "DKK"; }
if ($value['currency'] == "6") { $currency = "EUR"; }
if ($value['currency'] == "7") { $currency = "HKD"; }
if ($value['currency'] == "8") { $currency = "HUF"; }
if ($value['currency'] == "9") { $currency = "ILS"; }
if ($value['currency'] == "10") { $currency = "JPY"; }
if ($value['currency'] == "11") { $currency = "MYR"; }
if ($value['currency'] == "12") { $currency = "MXN"; }
if ($value['currency'] == "13") { $currency = "NOK"; }
if ($value['currency'] == "14") { $currency = "NZD"; }
if ($value['currency'] == "15") { $currency = "PHP"; }
if ($value['currency'] == "16") { $currency = "PLN"; }
if ($value['currency'] == "17") { $currency = "GBP"; }
if ($value['currency'] == "18") { $currency = "RUB"; }
if ($value['currency'] == "19") { $currency = "SGD"; }
if ($value['currency'] == "20") { $currency = "SEK"; }
if ($value['currency'] == "21") { $currency = "CHF"; }
if ($value['currency'] == "22") { $currency = "TWD"; }
if ($value['currency'] == "23") { $currency = "THB"; }
if ($value['currency'] == "24") { $currency = "TRY"; }
if ($value['currency'] == "25") { $currency = "USD"; }

// language
if ($value['language'] == "1") {
$language = "DK";
$image = "https://www.paypalobjects.com/da_DK/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/da_DK/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/da_DK/DK/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Danish

if ($value['language'] == "2") {
$language = "NL";
$image = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Dutch

if ($value['language'] == "3") {
$language = "US";
$image = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //English

if ($value['language'] == "4") {
$language = "FR";
$image = "https://www.paypalobjects.com/fr_CA/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/fr_CA/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/fr_CA/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //French

if ($value['language'] == "5") {
$language = "DE";
$image = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //German

if ($value['language'] == "6") {
$language = "IL";
$image = "https://www.paypalobjects.com/he_IL/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/he_IL/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/he_IL/IL/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Hebrew

if ($value['language'] == "7") {
$language = "IT";
$image = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Italian

if ($value['language'] == "8") {
$language = "JP";
$image = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Japanese

if ($value['language'] == "9") {
$language = "NO";
$image = "https://www.paypalobjects.com/no_NO/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/no_NO/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/no_NO/NO/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Norwgian

if ($value['language'] == "10") {
$language = "PL";
$image = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Polish

if ($value['language'] == "11") {
$language = "BR";
$image = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Portuguese

if ($value['language'] == "12") {
$language = "RU";
$image = "https://www.paypalobjects.com/ru_RU/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/ru_RU/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/ru_RU/RU/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Russian

if ($value['language'] == "13") {
$language = "ES";
$image = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Spanish

if ($value['language'] == "14") {
$language = "SE";
$image = "https://www.paypalobjects.com/sv_SE/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/sv_SE/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/sv_SE/SE/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Swedish

if ($value['language'] == "15") {
$language = "CN";
$image = "https://www.paypalobjects.com/zh_XC/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/zh_XC/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/zh_XC/C2/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Simplified Chinese - China

if ($value['language'] == "16") {
$language = "HK";
$image = "https://www.paypalobjects.com/zh_HK/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/zh_HK/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/zh_HK/HK/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Traditional Chinese - Hong Kong

if ($value['language'] == "17") {
$language = "TW";
$image = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Traditional Chinese - Taiwan

if ($value['language'] == "18") {
$language = "TR";
$image = "https://www.paypalobjects.com/tr_TR/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/tr_TR/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/tr_TR/TR/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Turkish

if ($value['language'] == "19") {
$language = "TH";
$image = "https://www.paypalobjects.com/th_TH/i/btn/btn_buynow_SM.gif";
$imageb = "https://www.paypalobjects.com/th_TH/i/btn/btn_buynow_LG.gif";
$imagecc = "https://www.paypalobjects.com/th_TH/TH/i/btn/btn_buynowCC_LG.gif";
$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
} //Thai

if (!empty($atts['size'])) {
if ($atts['size'] == "1") { $img = $image; }
if ($atts['size'] == "2") { $img = $imageb; }
if ($atts['size'] == "3") { $img = $imagecc; }
if ($atts['size'] == "5") { $img = $imagenew; }
} else {
if ($value['size'] == "1") { $img = $image; }
if ($value['size'] == "2") { $img = $imageb; }
if ($value['size'] == "3") { $img = $imagecc; }
if ($value['size'] == "4") { $img = $value['upload_image']; }
if ($value['size'] == "5") { $img = $imagenew; }
}

// note action
if ($value['notices'] == "1") { $note = "0"; }
if ($value['notices'] == "2") { $note = "1"; }

// alignment
$alignment = "";
if ($atts['align'] == "left") { $alignment = "style='float: left;'"; }
if ($atts['align'] == "right") { $alignment = "style='float: right;'"; }
if ($atts['align'] == "center") { $alignment = "style='margin-left: auto;margin-right: auto;width:170px'"; }




include_once ("include/encrypt_url.php");


$hash = $value['hash'];


$code = new Encryption($hash);
$encoded_url = $code->wpepdd_encode($atts['url'],$hash);


$output = "";
$output .= "<div $alignment>";
$output .= "<form action='" . get_admin_url() . "admin-post.php' METHOD='POST'>";
$output .= "<input type='hidden' name='action' value='submit-form-wpepdd' />";
$output .= "<input style='border:none;' type='image' name='paypal_submit' id='paypal_submit' src='$img' border='0' align='top' alt='Pay with PayPal'/>";
$output .= "<input type='hidden' name='url' value='$encoded_url'>";
$output .= "<input type='hidden' name='name' value='". $atts['name'] ."'>";
$output .= "<input type='hidden' name='amount' value='". $atts['price'] ."'>";
$output .= "<input type='hidden' name='currency' value='". $currency ."'>";
$output .= "<input type='hidden' name='language' value='". $language ."'>";
$output .= "</form></div>";

return $output;

}


// paypal footer script
function wpepdd_add_this_script_footer(){ ?>

<script src='https://www.paypalobjects.com/js/external/dg.js' type='text/javascript'></script>
<script>
var dg = new PAYPAL.apps.DGFlow(
{
trigger: 'paypal_submit',
expType: 'instant'
});
</script>

<?php

}

add_action('wp_footer', 'wpepdd_add_this_script_footer');








// paypal post
add_action('admin_post_submit-form-wpepdd', 'wpepdd_handle_form_action');
add_action('admin_post_nopriv_submit-form-wpepdd', 'wpepdd_handle_form_action');

function wpepdd_handle_form_action() {
include_once ('include/checkout.php');
}

// paypal response
add_action('admin_post_submit-form-wpepdd-return', 'wpepdd_handle_form_action_return');
add_action('admin_post_nopriv_submit-form-wpepdd-return', 'wpepdd_handle_form_action_return');

function wpepdd_handle_form_action_return() {
include_once ('include/orderconfirm.php');
}

// paypal cancel
add_action('admin_post_submit-form-wpepdd-cancel', 'wpepdd_handle_form_action_cancel');
add_action('admin_post_nopriv_submit-form-wpepdd-cancel', 'wpepdd_handle_form_action_cancel');

function wpepdd_handle_form_action_cancel() {
include_once ('include/cancel.php');
}




?>