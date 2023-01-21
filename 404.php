<?php
include('include/autoloader.php');
$smarty->assign('is_notfound',1);

$smarty->assign('seo_title','Page not found | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_keywords',normalize_input($options['general_seo_keywords']));
$smarty->assign('seo_description',normalize_input($options['general_seo_description']));
$smarty->display('404.html');
?>