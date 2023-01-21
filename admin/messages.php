<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Messages</h1>
        <div class="actions">
            <a href="<?php echo help_links('messages'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
        </div>
    </div>
<?php
$_SESSION['user_session_id'] = session_id();
if (!empty($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
switch ($case) {

default;
?>
<div class="page-container">
    <div class="page-header">
        <h4>All Messages</h4>
    </div>
    <div class="form">
    <?php
    if (isset($message)) {echo normalize_input($message);}
    $page = 1;
    $size = 20;
    if (isset($_GET['page'])){ $page = (int) $_GET['page']; }

    $sqls = "SELECT ss_sales.id AS sid,ss_sales.order_id,ss_sales.service_id,ss_sales.customer_id AS sale_customer_id,ss_sales.order_datetime,ss_sales.completed,ss_messages.id,ss_messages.sender_id,ss_messages.customer_id,ss_messages.readed,ss_messages.deleted FROM ss_messages JOIN ss_sales ON ss_messages.sale_id=ss_sales.id WHERE ss_messages.deleted='0' GROUP BY ss_messages.sale_id ORDER BY ss_messages.readed ASC";
    $query = $mysqli->query($sqls);
    $total_records = $query->num_rows;
    if ($total_records == 0) {
        echo notification('warning','There Are No Messages right now.');
    } else {
        $pagination = new Pagination();
        $pagination->setLink("messages.php?page=%s");
        $pagination->setPage($page);
        $pagination->setSize($size);
        $pagination->setTotalRecords($total_records);
        $get = "SELECT ss_sales.id AS sid,ss_sales.order_id,ss_sales.service_id,ss_sales.customer_id AS sale_customer_id,ss_sales.order_datetime,ss_sales.completed,ss_messages.id,ss_messages.sender_id,ss_messages.customer_id,ss_messages.readed,ss_messages.datetime,ss_messages.deleted FROM ss_messages JOIN ss_sales ON ss_messages.sale_id=ss_sales.id WHERE ss_messages.deleted='0' GROUP BY ss_messages.sale_id ORDER BY ss_messages.readed ASC, ss_messages.id DESC " . $pagination->getLimitSql();
        $q = $mysqli->query($get);
        ?>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th width="30"></th>
                <th>Order</th>
                <th class="hidden-xs">Customer</th>
                <th class="hidden-xs">Date</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($row = $q->fetch_assoc()) {
                ?>
                <tr <?php if(get_unreaded_messages_number($row['sid']) > 0): ?>class="unreaded"<?php endif; ?>>
                    <td class="message-statue"><i class="icon <?php if(get_unreaded_messages_number($row['sid']) > 0): ?>icon-email<?php else: ?>icon-newsletter<?php endif; ?>"></i></td>
                    <td>
                        <div class="customer-details">
                            <h3><a href="orders.php?case=details&id=<?php echo make_safe($row['sid']); ?>#message-<?php echo make_safe($row['id']); ?>">#<?php echo make_safe($row['order_id']); ?></a></h3>
                            <a href="orders.php?case=details&id=<?php echo make_safe($row['sid']); ?>#message-<?php echo make_safe($row['id']); ?>"><?php echo make_safe(get_service_title($row['service_id'])); ?></a>
                        </div>
                    </td>
                    <td class="hidden-xs" width="120"><a href="customers.php?case=details&id=<?php echo make_safe($row['customer_id']); ?>"><?php echo make_safe(get_customer($row['customer_id'])); ?></a></td>
                    <td class="hidden-xs"><?php echo make_safe(date('d F, Y h:i a',$row['datetime'])); ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <div class="news-actions">
           <?php echo normalize_input($pagination->create_links()); ?>
        </div>
        <?php } ?>
    </div>
</div>
<?php }
include('footer.php');
?>