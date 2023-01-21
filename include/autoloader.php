<?php
session_start();
error_reporting(E_ERROR);
include('config.php');
include('connect.php');
$cc = $mysqli->query("SHOW TABLES LIKE 'ss_admin'");
if($cc->num_rows == 0) {
	die('The Script Isn\'t Installed Successfully. <a href="install/install.php">Go to Installation.</a>');
}
if ($cc->num_rows > 0 AND is_dir('install/')) {
	die('The <b>Install</b> folder is exists. Please delete the <b>Install</b> folder or rename it.');
}
include('pagination.php');
include('nocsrf.php');
include('general.class.php');
require 'paypal-sdk/autoload.php';
require 'stripe-sdk/Stripe.php';
require('razorpay-php/Razorpay.php');
include('functions.php');
$general = new General;
$general->set_connection($mysqli);
$general_options = $general->get_options('General');
if (!empty($_SERVER['PATH_INFO'])) {$pathinfo = $_SERVER['PATH_INFO'];} else {$pathinfo = '';}
if (!empty($_SERVER['QUERY_STRING'])) {$querystring = $_SERVER['QUERY_STRING'];} else {$querystring = '';}
$all = parse_url(curPageURL());
$paths = explode('/',$all['path']);
if (!empty($paths[4])) {$id = $paths[4];} else {$id = false;}
if (!empty($paths[5])) {$slug = $paths[5];} else {$slug = false;}
if (isset($all['query'])) {
$queries = explode('&',$all['query']);
parse_str($all['query'],$queries);
}
require_once('smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->compile_dir = 'cache/';
$smarty->template_dir = 'themes/'.$general_options['site_theme'].'/';
$smarty->plugins_dir = array(
                       './include/smarty/plugins',
                       './plugins/'
                       );
$smarty->force_compile = true;
$options = $general->get_all_options();
foreach ($options AS $key=>$value) {
$smarty->assign($key,$value);
}
$theme_options = $general->get_theme_options($options['general_site_theme']);
foreach ($theme_options AS $key=>$value) {
	$smarty->assign($key,$value);
}
$smarty->assign('mysqli',$mysqli);
include('languages/'.$options['general_site_language'].'/site.php');
foreach ($language AS $key=>$value) {
	$smarty->assign('lang_'.$key,$value);
}
$pubkey = $options['api_recaptcha_public'];
$smarty->assign('pubkey',$pubkey);
$smarty->assign('currentpage',curPageURL());

include('twitter_login.php');
include('envato_login.php');
include('autologin.php');
if (isset($_SESSION['ss_solo_user'])) {
	$smarty->assign('islogged',$_SESSION['ss_solo_user']);
	$user = $general->customer($_SESSION['ss_solo_user']);
	foreach ($user AS $key=>$value) {
		$smarty->assign('customer_'.$key,$value);
	}
	$messages_count = $general->unread_messages_count($_SESSION['ss_solo_user']);
	$smarty->assign('messages_count',$messages_count);
    $mysqli->query("UPDATE ss_customers SET last_active='".time()."' WHERE id='$row[id]'");
} else {
	$smarty->assign('islogged',0);
}
$fonts = get_google_fonts();
$smarty->assign('fonts',$fonts);
$pages = $general->pages();
$smarty->assign('pages',$pages);
$categories = $general->main_categories('category_order ASC');
$smarty->assign('categories',$categories);

?>
