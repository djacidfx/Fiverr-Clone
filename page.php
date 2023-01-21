<?php
include('include/autoloader.php');
$slug = make_safe($_GET['slug']);
$page = $general->page($slug);
if ($page == 0) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
foreach ($page AS $key => $value) {
    $smarty->assign('page_' . $key, $value);
}

$smarty->assign('seo_title',htmlspecialchars_decode($page['title'],ENT_QUOTES).' | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_description',normalize_input($page['seo_description']));
$smarty->assign('seo_keywords',normalize_input($page['seo_keywords']));
$smarty->display('page.html');
?>