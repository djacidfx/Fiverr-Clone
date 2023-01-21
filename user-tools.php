<?php
include('include/autoloader.php');
if (isset($_SESSION['ss_solo_user'])) {
header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');	
exit;
}	
if (isset($_GET['case'])) {
$case = make_safe(xss_clean($_GET['case']));	
} else {
header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');	
exit;
}
switch ($case) {
case 'confirm_user_email';
$email = make_safe(xss_clean($_GET['email']));
$activation_code = make_safe(xss_clean($_GET['activation_code']));
if (!empty($email) OR !empty($activation_code)) {
$check_query = $mysqli->query("SELECT * FROM ss_customers WHERE activation_code='$activation_code' LIMIT 1");
$check = $check_query->num_rows;
if ($check == 0) {
$message = notification('danger','You have followed an invalid link');
} else {
$row = $check_query->fetch_assoc();
if ($email == md5($row['email'])) {
$new_activation_code = genRandomString(24);
$update = $mysqli->query("UPDATE ss_customers SET active='1',activation_code='$new_activation_code' WHERE id='$row[id]'");	
if ($update) {
$message = notification('success','You Have Successfully Activated your Membership, <a href="javascript:void();" data-toggle="modal" data-target="#login-modal" class="alert-link">Login to Your Account</a>');	
} else {
$message = notification('danger','Error Happened');	
}
} else {
$message = notification('danger','You have followed an invalid link');
}
} 
} else {
$message = notification('danger','You have followed an invalid link');
}
$smarty->assign('message',normalize_input($message));
$smarty->assign('seo_title','membership Confirmation | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_keywords',normalize_input($options['general_seo_keywords']));
$smarty->assign('seo_description',normalize_input($options['general_seo_description']));
$smarty->display('user-activation.html');
break;	
case 'forget_password';
$smarty->assign('seo_title','Forget Password | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_keywords',normalize_input($options['general_seo_keywords']));
$smarty->assign('seo_description',normalize_input($options['general_seo_description']));
$smarty->display('forget-password.html');
break;
case 'reset_password';
$email = make_safe(xss_clean($_GET['email']));
$activation_code = make_safe(xss_clean($_GET['activation_code']));
if (!empty($email) OR !empty($activation_code)) {
$check_query = $mysqli->query("SELECT * FROM ss_customers WHERE activation_code='$activation_code' LIMIT 1");
$check = $check_query->num_rows;
if ($check == 0) {
$message = notification('danger','You have followed an invalid link');
} else {
$row = $check_query->fetch_assoc();
if ($email == md5($row['email'])) {
$smarty->assign('true_link',1);
$smarty->assign('user_id',$row['id']);
} else {
$message = notification('danger','You have followed an invalid link');
}
} 
} else {
$message = notification('danger','You have followed an invalid link');
}
if (isset($message)) {
	$smarty->assign('message',$message);
}
$smarty->assign('seo_title','Reset Password - '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_keywords',normalize_input($options['general_seo_keywords']));
$smarty->assign('seo_description',normalize_input($options['general_seo_description']));
$smarty->display('reset-password.html');
break;
default;
die('You Can\'t Access This Page.');	
}
?>