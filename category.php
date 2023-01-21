<?php
include('include/autoloader.php');
$smarty->assign('is_category',1);
$page = 1;
$category_id = make_safe($_GET['id']);
$category = $general->category($category_id);
foreach($category AS $key=>$value) {
    $smarty->assign('category_'.$key,$value);
}
if (isset($theme_options['theme_all_services_number'])) {
    $size = normalize_input($theme_options['theme_all_services_number']);
} else {
    $size = 12;
}
if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
$g = $mysqli->query("SELECT * FROM ss_services WHERE active='1' AND deleted='0' AND category_id='$category_id' ORDER BY id DESC");
$services_number = $g->num_rows;
$smarty->assign('services_number',$services_number);
if ($services_number > 0) {
    $pagination = new Pagination();
    $pagination->setLink("./services?page=%s");
    $pagination->setPage($page);
    $pagination->setSize($size);
    $pagination->setTotalRecords($services_number);
    $get = "SELECT * FROM ss_services WHERE active='1' AND deleted='0' AND category_id='$category_id' ORDER BY id DESC ".$pagination->getLimitSql();
    $q = $mysqli->query($get);
    while ($row = $q->fetch_assoc()) {
        $services[] = $row;
    }
    $smarty->assign('services',$services);
    $smarty->assign('paginations',$pagination->create_links());
}


$smarty->assign('seo_title',normalize_input($category['category']));
$smarty->assign('seo_keywords',normalize_input($category['seo_keywords']));
$smarty->assign('seo_description',normalize_input($category['seo_description']));
$smarty->display('category.html');
?>