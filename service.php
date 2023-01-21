<?php
include('include/autoloader.php');
$slug = make_safe($_GET['slug']);
$service = $general->service($slug);
if ($service == 0) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
$smarty->assign('is_service',1);
foreach ($service AS $key => $value) {
    $smarty->assign('service_' . $key, $value);
}
if (isset($theme_options['theme_related_services_number'])) {$related_number = $theme_options['theme_related_services_number'];} else {$related_number = 3;}
$related = $general->related($service['id'],$service['title'],$related_number);
$smarty->assign('services',$related);

$smarty->assign('seo_title',normalize_input(htmlspecialchars_decode($service['title'], ENT_QUOTES)).' | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_description',normalize_input($service['seo_description']));
$smarty->assign('seo_keywords',normalize_input($service['seo_keywords']));
$smarty->display('service.html');
?>