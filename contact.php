<?php
include('include/autoloader.php');
$smarty->assign('is_contact',1);

$smarty->assign('seo_title','Contact Us | '.$options['general_seo_title']);
$smarty->assign('seo_description',$options['general_seo_description']);
$smarty->assign('seo_keywords',$options['general_seo_keywords']);
$smarty->display('contact.html');
?>