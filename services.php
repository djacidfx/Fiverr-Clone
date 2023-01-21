<?php
include('include/autoloader.php');
$smarty->assign('is_services',1);
$page = 1;
if (isset($theme_options['theme_all_services_number'])) {
    $size = normalize_input($theme_options['theme_all_services_number']);
} else {
    $size = 12;
}
if (isset($_GET['page'])){ $page = (int) $_GET['page']; }

$services_number = $general->services_number();
$smarty->assign('services_number',$services_number);
if ($services_number > 0) {
    $pagination = new Pagination();
    $pagination->setLink("./services?page=%s");
    $pagination->setPage($page);
    $pagination->setSize($size);
    $pagination->setTotalRecords($services_number);
    $get = "SELECT * FROM ss_services WHERE active='1' AND deleted='0' ORDER BY id DESC ".$pagination->getLimitSql();
    $q = $mysqli->query($get);
    while ($row = $q->fetch_assoc()) {
        $services[] = $row;
    }
    $smarty->assign('services',$services);
    $smarty->assign('paginations',$pagination->create_links());
}


$smarty->assign('seo_title','All Services | '.normalize_input($options['general_seo_title']));
$smarty->assign('seo_keywords',normalize_input($options['general_seo_keywords']));
$smarty->assign('seo_description',normalize_input($options['general_seo_description']));
$smarty->display('services.html');
?>