<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Customers</h1>
        <div class="actions">
            <a href="<?php echo help_links('customers'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
        </div>
    </div>
<?php
if (!empty($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
switch ($case) {
case 'details';
    $id = make_safe($_GET['id']);
    $customer = $general->single_customer($id);
    $orders = $general->customer_orders($id);
    ?>
<div class="page-container">
    <div class="page-header">
        <h4>Orders By <?php echo make_safe($customer['username']); ?></h4>
        <div class="actions">
            <a href="customers.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
    <div class="form">
<?php if ($orders == 0): ?>
    <div class="alert alert-warning">This Customer hasn't ordered yet.</div>
<?php else: ?>
    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th width="35"></th>
            <th>Service</th>
            <th>Customer</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($orders AS $order) {
            ?>
            <tr>
                <td>
                    <?php if ($order['deleted'] == 1) { ?>
                        <span class="badge badge-danger">Deleted</span>
                    <?php } else { ?>
                        <?php if ($order['completed'] == 0) { ?>
                            <?php if ($order['start_datetime'] == 0) : ?>
                                <span class="badge badge-info">New</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Ongoing</span>
                            <?php endif; ?>
                        <?php } else { ?>
                            <span class="badge badge-success">Completed</span>
                        <?php } ?>
                    <?php } ?>
                </td>
                <td>
                    <div class="customer-details">
                        <h3><a href="orders.php?case=details&id=<?php echo make_safe($order['id']); ?>">#<?php echo make_safe($order['order_id']); ?></a></h3>
                        <?php echo make_safe(get_service_title($order['service_id'])); ?>
                    </div>
                </td>
                <td>
                    <div class="customer-details"><a href="customers.php?case=details&id=<?php echo make_safe($order['customer_id']); ?>"><?php echo make_safe(get_customer($order['customer_id'])); ?></a></div>
                    <div><?php echo make_safe(date('d F, Y',$order['order_datetime'])); ?></div>
                </td>
                <td width="120"><?php echo make_safe($order['amount']).' '.make_safe($order['currency']); ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    </div>
    </div>
    <?php
endif;
break;
default;
?>
<div class="page-container">
    <div class="page-header">
        <h4>All Customers</h4>
    </div>
    <div class="form">
    <?php
    if (isset($message)) {echo normalize_input($message);}
    $page = 1;
    $size = 20;
    if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
    $sqls = "SELECT * FROM ss_customers WHERE active='1' AND deleted='0' ORDER BY id DESC";
    $query = $mysqli->query($sqls);
    $total_records = $query->num_rows;
    if ($total_records == 0) {
        echo notification('warning','There Are No Active Customers.');
    } else {
        $pagination = new Pagination();
        $pagination->setLink("?page=%s");
        $pagination->setPage($page);
        $pagination->setSize($size);
        $pagination->setAlign('justify-content-end');
        $pagination->setTotalRecords($total_records);
        $get = "SELECT * FROM ss_customers WHERE active='1' AND deleted='0' ORDER BY id DESC ".$pagination->getLimitSql();
        $q = $mysqli->query($get);
        ?>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>Username</th>
                    <th class="hidden-xs">Orders</th>
                    <th class="hidden-xs">Provider</th>
                    <th class="hidden-xs">Register Date</th>
                    <th class="hidden-xs">Last Activity</th>
                    <th width="200"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = $q->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <div class="customer-details">
                                <a class="d-block" href="customers.php?case=details&id=<?php echo make_safe($row['id']); ?>"><?php echo make_safe($row['username']); ?></a>
                                <span><?php echo $row['email']; ?></span>

                            </div>
                        </td>
                        <td class="hidden-xs" width="120"><?php echo make_safe(customer_orders($row['id'])); ?></td>
                        <td class="hidden-xs" width="120"><?php if (!empty($row['provider'])) {echo $row['provider'];} else {echo 'Site';} ?></td>
                        <td class="hidden-xs">
                            <span><?php echo make_safe(date('d F, Y',$row['datetime'])); ?></span>
                        </td>
                        <td class="hidden-xs">
                            <span><?php echo make_safe(date('d F, Y',$row['last_active'])); ?></span>
                        </td>
                        <td align="right">
                            <a class="action-remove" href="javascript:deleteCustomer(<?php echo make_safe($row['id']); ?>);" data-toggle="tooltip" data-placement="top" title="Delete">Delete</a>
                        </td>
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