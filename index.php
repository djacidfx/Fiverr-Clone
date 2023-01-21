<?php
include('include/autoloader.php');
$smarty->assign('is_home',1);
$slider = $general->slider();
if ($slider != 0) {
    $smarty->assign('slider',$slider);
}
$services_number = $general->services_number();
$smarty->assign('services_number',$services_number);
if ($services_number > 0) {
    $latest = $general->latest_services($theme_options['theme_home_services_number']);
    $smarty->assign('services',$latest);
}
$smarty->assign('seo_title',$options['general_seo_title']);
$smarty->assign('seo_description',$options['general_seo_description']);
$smarty->assign('seo_keywords',$options['general_seo_keywords']);
$smarty->display('index.html');
?>