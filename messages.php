<?php
include('include/autoloader.php');
if (!isset($_SESSION['ss_solo_user'])) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
$smarty->assign('is_dashboard',1);


$page = 1;
$size = 20;
if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
$sqls = "SELECT ss_sales.id AS sid,ss_sales.order_id,ss_sales.service_id,ss_sales.customer_id AS sale_customer_id,ss_sales.order_datetime,ss_sales.completed,ss_sales.deleted,ss_messages.id,ss_messages.sender_id,ss_messages.customer_id,ss_messages.readed,ss_messages.deleted,ss_messages.sale_id FROM ss_messages JOIN ss_sales ON ss_messages.sale_id=ss_sales.id WHERE ss_messages.customer_id='$_SESSION[ss_solo_user]' AND ss_messages.sender_id='0' AND ss_messages.deleted='0' GROUP BY ss_messages.sale_id ORDER BY ss_messages.readed";
$query = $mysqli->query($sqls);
$total_records = $query->num_rows;
$smarty->assign('total_records',$total_records);
if ($total_records > 0) {
    $pagination = new Pagination();
    $pagination->setLink("./dashboard/messages?page=%s");
    $pagination->setPage($page);
    $pagination->setSize($size);
    $pagination->setTotalRecords($total_records);
    $get = "SELECT ss_sales.id AS sid,ss_sales.order_id,ss_sales.service_id,ss_sales.customer_id AS sale_customer_id,ss_sales.order_datetime,ss_sales.completed,ss_sales.deleted,ss_messages.id,ss_messages.sender_id,ss_messages.customer_id,ss_messages.readed,ss_messages.datetime,ss_messages.deleted,ss_messages.sale_id  FROM ss_messages JOIN ss_sales ON ss_messages.sale_id=ss_sales.id WHERE ss_messages.customer_id='$_SESSION[ss_solo_user]' AND ss_messages.sender_id='0' AND ss_messages.deleted='0' GROUP BY ss_messages.sale_id ORDER BY ss_messages.readed ASC, ss_messages.id DESC " . $pagination->getLimitSql();
    $q = $mysqli->query($get);
    while ($row = $q->fetch_assoc()) {
        $messages[] = $row;
    }
    $smarty->assign('messages',$messages);
    $smarty->assign('paginations',$pagination->create_links());
}




$smarty->assign('seo_title','Dashboard | '.$options['general_seo_title']);
$smarty->assign('seo_description',$options['general_seo_description']);
$smarty->assign('seo_keywords',$options['general_seo_keywords']);
$smarty->display('dashboard.html');
?>