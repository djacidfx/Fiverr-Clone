<?php
include('include/autoloader.php');
if (!isset($_SESSION['ss_solo_user'])) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
$id = make_safe($_GET['id']);
if (empty($id)) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
$_SESSION['user_session_id'] = session_id();
$order = $general->user_order($id,$_SESSION['ss_solo_user']);
if ($order == 0) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
foreach ($order AS $key=>$value) {
    $smarty->assign('order_'.$key,$value);
}
$service = $general->service_by_id($order['service_id']);
foreach ($service AS $key=>$value) {
    $smarty->assign('service_'.$key,$value);
}
$messages = $general->order_messages($order['id'],$_SESSION['ss_solo_user']);
$smarty->assign('messages',$messages);
$smarty->assign('seo_title','Order: '.$id.' | '.$options['general_seo_title']);
$smarty->assign('seo_description',$options['general_seo_description']);
$smarty->assign('seo_keywords',$options['general_seo_keywords']);
$smarty->display('order.html');
?>