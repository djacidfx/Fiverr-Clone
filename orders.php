<?php
include('include/autoloader.php');
if (!isset($_SESSION['ss_solo_user'])) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}

$page = 1;
$size = 20;
if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
$sqls = "SELECT ss_sales.order_id,ss_sales.service_id,ss_sales.customer_id,ss_sales.amount,ss_sales.currency,ss_sales.order_datetime,ss_sales.completed,ss_sales.deleted,ss_services.id,ss_services.title,ss_services.slug FROM ss_sales JOIN ss_services ON ss_sales.service_id=ss_services.id WHERE ss_sales.customer_id='$_SESSION[ss_solo_user]' ORDER BY ss_sales.id DESC";
$query = $mysqli->query($sqls);
$total_records = $query->num_rows;
if ($total_records > 0) {
    $pagination = new Pagination();
    $pagination->setLink("./dashboard/orders?page=%s");
    $pagination->setPage($page);
    $pagination->setSize($size);
    $pagination->setTotalRecords($total_records);
    $get = "SELECT ss_sales.order_id,ss_sales.service_id,ss_sales.customer_id,ss_sales.amount,ss_sales.currency,ss_sales.order_datetime,ss_sales.completed,ss_sales.deleted,ss_services.id,ss_services.title,ss_services.slug FROM ss_sales JOIN ss_services ON ss_sales.service_id=ss_services.id WHERE ss_sales.customer_id='$_SESSION[ss_solo_user]' ORDER BY ss_sales.id DESC " . $pagination->getLimitSql();
    $q = $mysqli->query($get);
    while ($row = $q->fetch_assoc()) {
        $orders[] = $row;
    }
    $smarty->assign('orders',$orders);
    $smarty->assign('paginations',$pagination->create_links());
}

$smarty->assign('seo_title','Orders | '.$options['general_seo_title']);
$smarty->assign('seo_description',$options['general_seo_description']);
$smarty->assign('seo_keywords',$options['general_seo_keywords']);
$smarty->display('orders.html');
?>