<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Orders</h1>
        <div class="actions">
            <a href="<?php echo help_links('orders'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
        </div>
    </div>
<?php
if (!empty($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
switch ($case) {
case 'refund_sale';
    $id = make_safe(intval($_GET['id']));
    $order = $general->single_order($id);
    $service = $services->service($order['service_id']);
    $customer = $general->single_customer($order['customer_id']);
    if (isset($_POST['refund'])) {
        if ($order['payment_method'] == 'paypal') {
            if ($options['payment_paypal_sandbox'] == 'sandbox') {
                $apiContext = new \PayPal\Rest\ApiContext(
                    new \PayPal\Auth\OAuthTokenCredential(
                        $options['payment_paypal_test_client_id'],     // ClientID
                        $options['payment_paypal_test_client_secret']      // ClientSecret
                    )
                );
            } else {
                $apiContext = new \PayPal\Rest\ApiContext(
                    new \PayPal\Auth\OAuthTokenCredential(
                        $options['payment_paypal_client_id'],     // ClientID
                        $options['payment_paypal_client_secret']      // ClientSecret
                    )
                );
                $apiContext->setConfig(
                    array(
                        'log.LogEnabled' => false,
                        'log.FileName' => 'PayPal.log',
                        'log.LogLevel' => 'DEBUG',
                        'mode' => 'live'
                    )
                );
            }


            $amt = new PayPal\Api\Amount();
            $amt->setTotal($order['amount'])
                ->setCurrency($order['currency']);

            $refund = new PayPal\Api\Refund();
            $refund->setAmount($amt);
            $sale = new PayPal\Api\Sale();
            $sale->setId($order['transaction_id']);

            try {
                $refundedSale = $sale->refund($refund, $apiContext);
                if ($refundedSale->state == 'completed') {
                    $refund_result = 1;
                } else {
                    $refund_result = 0;
                }
            } catch (PayPal\Exception\PayPalConnectionException $ex) {
                die($ex);
            } catch (Exception $ex) {
                die($ex);
            }
        }

        if ($order['payment_method'] == 'stripe') {
            $params = array(
                "testmode"   => $options['payment_stripe_test_mode'],
                "private_live_key" => $options['payment_stripe_private_live_key'],
                "public_live_key"  => $options['payment_stripe_public_live_key'],
                "private_test_key" => $options['payment_stripe_private_test_key'],
                "public_test_key"  => $options['payment_stripe_public_test_key']
            );

            if ($params['testmode'] == "on") {
                Stripe::setApiKey($params['private_test_key']);
                $pubkey = $params['public_test_key'];
            } else {
                Stripe::setApiKey($params['private_live_key']);
                $pubkey = $params['public_live_key'];
            }

            $ch = Stripe_Charge::retrieve($order['transaction_id']);
            $refund = $ch->refunds->create(array('amount' => ($order['amount']*100)));
            if ($refund->status == 'succeeded') {
                $refund_result = 1;
            } else {
                $refund_result = 0;
            }

        }
    }

    ?>
<div class="page-container">
    <div class="page-header">
        <h4>Refund Order : #<?php echo make_safe($order['order_id']); ?><?php echo make_safe($service['title']); ?></h4>
    </div>
    <div class="form">
<div class="order-details">
            <?php if ($refund_result == 1): ?>
                <div class="alert alert-success">
                    Refund has made successfully. <a href="orders.php" class="alert-link">Back to orders</a>
                </div>
            <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        are you sure that you want to refund the order <strong>#<?php echo make_safe($order['order_id']); ?></strong> ?<br />
                        you will pay <?php echo make_safe($order['amount']).' '.make_safe($order['currency']); ?> to <strong><?php echo make_safe($customer['username']).' ('.make_safe($customer['email']).')'; ?></strong><br />
                        this action can't be reversed.
                    </div>
                    <div class="alert alert-warning">
                        Note : This action will not cancel the order, you have to cancel it manually.<br />
                        <a href="orders.php?case=details&id=<?php echo make_safe($order['id']); ?>" class="alert-link">#<?php echo make_safe($order['order_id']); ?></a>
                    </div>
                    <form method="post" action="">
                        <button type="submit" name="refund" class="btn btn-danger">Refund</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
</div>
    </div>
</div>
<?php

break;
case 'details';
    $id = make_safe(intval($_GET['id']));
    $order = $general->single_order($id);
    $service = $services->service($order['service_id']);
    $_SESSION['admin_session_id'] = session_id();
    ?>
<div class="page-container">
    <div class="page-header">
        <h4>#<?php echo make_safe($order['order_id']); ?> <?php echo make_safe($service['title']); ?></h4>
        <div class="actions">
            <?php if ($order['completed'] == 0 && $order['start_datetime'] == 0): ?><a href="javascript:startOrder(<?php echo make_safe($order['id']); ?>);" class="btn btn-secondary btn-sm">Start</a><?php endif; ?>
            <?php if ($order['completed'] == 0 && $order['deleted'] == 0): ?><a href="javascript:cancelOrder(<?php echo make_safe($order['id']); ?>);" class="btn btn-danger btn-sm">Cancel</a><?php endif; ?>
            <?php if ($order['start_datetime'] > 0 && $order['completed'] == 0 && $order['deleted'] == 0): ?><a href="javascript:completeOrder(<?php echo make_safe($order['id']); ?>);" class="btn btn-success btn-sm">Complete</a><?php endif; ?>
            <?php if($order['payment_method'] == 'paypal' OR $order['payment_method'] == 'stripe' ): ?>
            <a href="orders.php?case=refund_sale&id=<?php echo make_safe($order['id']); ?>" class="btn btn-danger btn-sm">Refund</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="form">
        <div class="order-details">
            <div class="row">
                <div class="col-sm-6 col-md-4">
                    <div class="details-box yellow">
                        <i class="ri-user-line"></i>
                        <div class="details-box-content">
                            <span>Customer</span>
                            <a href="customers.php?case=details&id=<?php echo make_safe($order['customer_id']); ?>"><?php echo make_safe(get_customer($order['customer_id'])); ?></a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="details-box blue">
                        <i class="ri-money-dollar-box-line"></i>
                        <div class="details-box-content">
                            <span>Price</span>
                            <?php echo make_safe($order['amount']).'<br />'.make_safe($order['currency']); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="details-box red">
                        <i class="ri-calendar-line"></i>
                        <div class="details-box-content">
                            <span>Order Created</span>
                            <?php echo make_safe(date('Y-n-j', $order['order_datetime'])); ?><br />
                            <?php echo make_safe(date('h:i a', $order['order_datetime'])); ?>
                        </div>
                    </div>
                </div>
                <?php if ($order['start_datetime'] > 0): ?>
                <div class="col-sm-6 col-md-4">
                    <div class="details-box purple">
                        <i class="ri-calendar-2-line"></i>
                        <div class="details-box-content">
                            <span>Order Started</span>
                            <?php echo make_safe(date('Y-n-j', $order['start_datetime'])); ?><br />
                            <?php echo make_safe(date('h:i a', $order['start_datetime'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($order['expected_datetime'] > 0): ?>
                <div class="col-sm-6 col-md-4">
                    <div class="details-box">
                        <i class="ri-calendar-event-line"></i>
                        <div class="details-box-content">
                            <span>Expected Completion</span>
                            <?php echo make_safe(date('Y-n-j', $order['expected_datetime'])); ?><br />
                            <?php echo make_safe(date('h:i a', $order['expected_datetime'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($order['complete_datetime'] > 0): ?>
                <div class="col-sm-6 col-md-4">
                    <div class="details-box green">
                        <i class="ri-calendar-check-line"></i>
                        <div class="details-box-content">
                            <span>Completion</span>
                            <?php echo make_safe(date('Y-n-j', $order['complete_datetime'])); ?><br />
                            <?php echo make_safe(date('h:i a', $order['complete_datetime'])); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            </div>
            <div class="messages">
                <h3>Messages</h3>
                <?php if (!empty($service['requirements'])) : ?>
                <div class="message requirements">
                    <h4>Requirements</h4>
                    <?php echo htmlspecialchars_decode($service['requirements'],ENT_QUOTES); ?>
                </div>
                <?php endif; ?>
                <?php
                $messages = $general->order_messages($order['id']);
                if ($messages != 0): foreach ($messages AS $message):
                ?>
                    <div class="message <?php if ($message['sender_id'] == 0): ?>admin-sender<?php endif; ?>" id="message-<?echo make_safe($message['id']); ?>">
                        <h4><?php if ($message['sender_id'] == 0): ?>Admin<?php else: echo make_safe(get_customer($message['customer_id'])); endif; ?>, On <?php echo make_safe(date('Y-n-j h:i a', $message['datetime'])); ?></h4>
                        <p><?php echo nl2br($message['message']); ?></p>
                        <?php
                        $attachments = $general->message_attachments($message['id']);
                        if ($attachments != 0) :
                            ?>
                            <div class="attachments">
                                <h5>Attachments</h5>
                                <ul>
                                <?php foreach ($attachments AS $attachment): ?>
                                <li><a href="download.php?id=<?php echo make_safe($attachment['id']); ?>" target="_blank"><?php echo make_safe($attachment['attachment_filename']); ?></a></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <div class="send-message">
            <div id="ajax-result-message"></div>
                <form method="post" class="form" id="message-form" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="message">Message <span>*</span></label>
                        <textarea id="message" name="message" class="form-control" rows="5" placeholder="Write your message here" onkeyup="checkLength(this);"></textarea>
                        <input type="hidden" name="customer_id" value="<?php echo make_safe($order['customer_id']); ?>" />
                        <input type="hidden" name="order_id" value="<?php echo make_safe($order['id']); ?>" />
                        <input type="hidden" name="service_id" value="<?php echo make_safe($service['id']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="message">Attachments</label>
                        <input type="file" name="files[]" class="upload-files" data-jfiler-limit="10" showThumbs="false">
                    </div>
                    <div class="form-actions">
                        <button type="submit" id="message-submit" class="btn btn-sm btn-secondary" disabled="true">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <?php
    $general->make_message_readed($id);
break;
default;
?>
<div class="page-container">


    <div class="page-header">
        <h4>All Orders</h4>
    </div>
    <div class="form">
    <?php
    if (isset($message)) {echo normalize_input($message);}
    $page = 1;
    $size = 20;
    if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
    $sqls = "SELECT * FROM ss_sales ORDER BY id DESC";
    $query = $mysqli->query($sqls);
    $total_records = $query->num_rows;
    if ($total_records == 0) {
        echo notification('warning','There Are No Active Orders.');
    } else {
        $pagination = new Pagination();
        $pagination->setLink("?page=%s");
        $pagination->setPage($page);
        $pagination->setSize($size);
        $pagination->setAlign('justify-content-end');
        $pagination->setTotalRecords($total_records);
        $get = "SELECT * FROM ss_sales ORDER BY id DESC ".$pagination->getLimitSql();
        $q = $mysqli->query($get);
        ?>
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
                while ($row = $q->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                        <?php if ($row['deleted'] == 1) { ?>
                            <span class="badge badge-danger">Deleted</span>
                        <?php } else { ?>
                            <?php if ($row['completed'] == 0) { ?>
                                <?php if ($row['start_datetime'] == 0) : ?>
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
                                <h3><a href="orders.php?case=details&id=<?php echo make_safe($row['id']); ?>">#<?php echo make_safe($row['order_id']); ?></a></h3>
                                <?php echo make_safe(get_service_title($row['service_id'])); ?>
                            </div>
                        </td>
                        <td>
                            <div class="customer-details"><a href="customers.php?case=details&id=<?php echo make_safe($row['customer_id']); ?>"><?php echo make_safe(get_customer($row['customer_id'])); ?></a></div>
                            <div><?php echo make_safe(date('d F, Y',$row['order_datetime'])); ?></div>
                        </td>
                        <td width="120"><?php echo make_safe($row['amount']).' '.make_safe($row['currency']); ?></td>
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