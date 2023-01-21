<?php
include('include/autoloader.php');
if (!isset($_SESSION['ss_solo_user'])) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}

$smarty->assign('seo_title','Account | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_keywords',normalize_input($options['general_seo_keywords']));
$smarty->assign('seo_description',normalize_input($options['general_seo_description']));
$smarty->display('account.html');
?>