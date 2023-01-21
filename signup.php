<?php
include('include/autoloader.php');
$smarty->assign('is_signup',1);

$smarty->assign('seo_title','Signup | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_keywords',normalize_input($options['general_seo_keywords']));
$smarty->assign('seo_description',normalize_input($options['general_seo_description']));
$smarty->display('signup.html');
?>