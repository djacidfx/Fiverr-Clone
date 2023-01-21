<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Dashboard</h1>
    </div>
    
  
<div class="row">
    <div class="col-12">
    <?php if ($options['payment_allow_paypal'] == 0 AND $options['payment_allow_stripe'] == 0): ?>
        <div class="alert alert-warning">
            You must allow at least one payment method, <a href="setting.php?case=payment" class="alert-link">go to payment settings</a>
        </div>
    <?php endif; ?>
    <?php if ($options['payment_stripe_test_mode'] == 'on' AND $options['payment_paypal_sandbox'] == 'sandbox'): ?>
        <div class="alert alert-warning">
            Your payment methods are on Test mode, don't forget to go live when you start your website <a href="setting.php?case=payment" class="alert-link">go to payment settings</a>
        </div>
    <?php endif; ?>
    <?php if ($options['mail_mail_method'] == 'sendgrid' AND empty($options['mail_sendgrid_api_key'])): ?>
        <div class="alert alert-warning">
            Make sure that your mail settings are valid <a href="setting.php?case=mail" class="alert-link">go to mail settings</a>
        </div>
    <?php endif; ?>
    </div>
</div>

<div class="row">
<?php
$new_orders = $general->new_orders();
?>
    <div class="col-lg-4">
        <div class="widget">
            <div class="widget-title">
                <h5>New Orders <a href="orders.php">See All</a></h5>
            </div>
            <div class="widget-content scrollbar">
                <?php if ($new_orders == 0) : ?>
                <div class="empty-widget">
                    <p><i class="icon icon-handshake"></i>There is nothing to display here.</p>
                </div>
                <?php else: ?>
                <ul class="messages-list">
                    <?php
                    foreach ($new_orders AS $nod):
                        ?>
                        <li>
                            <a href="orders.php?case=details&id=<?php echo make_safe($nod['id']); ?>">
                                <span class="order-id">#<?php echo make_safe($nod['order_id']); ?></span>
                                <span class="service"><?php echo make_safe(get_service_title($nod['service_id'])); ?></span>
                                <span class="customer"><strong><?php echo make_safe(get_customer($nod['customer_id'])); ?></strong>, On <?php echo make_safe(date('d F, Y h:i a',$nod['order_datetime'])); ?></span>
                            </a>
                        </li>
                        <?php
                    endforeach;
                    ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="col-lg-8">
        <?php
        $start = $general->start_period();

            if (!isset($_GET['year']) OR empty($_GET['year']) OR $_GET['year'] > date('Y')) :
                $current_year = date('Y');
            else:
                $current_year = (int) $_GET['year'];
            endif;
            ?>
            <div class="widget">
                <div class="widget-title">
                    <h5>Statistics For <span class="text-secondary"><?php echo normalize_input($current_year); ?></span></h5>
                    <?php if ($start != 0) { ?>
                        <form method="GET" name="menu">
                            <select name="selectedPage" onchange="changePage(this.form.selectedPage)" class="form-control">
                                <option>Select Year</option>
                                <?php
                                echo generate_statics_select($start['year'],$current_year);
                                ?>
                            </select>
                        </form>
                     <?php } ?>
                </div>
                <div class="widget-content">
                    <?php if ($start == 0) { ?>
                        <div class="empty-widget">
                            <p><i class="icon icon-b-chart"></i>There is nothing to display here.</p>
                        </div>
                    <?php } else { ?>
                    <script>
                        jQuery(document).ready(function( $ ) {

                            Chart.defaults.global.defaultFontColor = '#666';
                            Chart.defaults.global.defaultFontFamily = 'Roboto';
                            Chart.defaults.global.defaultFontSize = 14;


                            var barChartData = {
                                labels: [
                                    <?php for($i=1;$i<13;$i++) { ?>
                                    '<?php echo month_name($i); ?>',
                                    <?php } ?>
                                ],
                                datasets: [{
                                    label: 'Customers',
                                    backgroundColor: 'rgba(56, 156, 255, 0.2)',
                                    borderColor:'rgba(56, 156, 255, 0.5)',
                                    borderWidth: 1,
                                    data: [
                                        <?php for($i=1;$i<13;$i++) { ?>
                                        <?php echo make_safe($general->statistics_customers($i,$current_year)); ?>,
                                        <?php } ?>
                                    ]
                                }, {
                                    label: 'Sales',
                                    backgroundColor: 'rgba(22, 197, 156, 0.2)',
                                    borderColor:'rgba(22, 197, 156, 0.5)',
                                    borderWidth: 1,
                                    data: [
                                        <?php for($i=1;$i<13;$i++) { ?>
                                        <?php echo make_safe($general->statistics_sales($i,$current_year)); ?>,
                                        <?php } ?>
                                    ]
                                }]

                            };



                            new Chart($('#earnings-chart'), {
                                type: 'bar',
                                data: barChartData,
                                options: {
                                    scales: {
                                        categoryPercentage: 1.0,
                                        barPercentage: 0.5,
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true,
                                                display: true,
                                                stepSize: 5
                                            },
                                            gridLines: {
                                                display: false,
                                                drawBorder: false
                                            },
                                        }],
                                        xAxes: [{
                                            ticks: {
                                                display: true
                                            },
                                            gridLines: {
                                                drawBorder: true,
                                                display: true
                                            },
                                            barThickness: 20
                                        }]
                                    },
                                    layout: {
                                        padding: {
                                            left: 20,
                                            right: 20,
                                            top: 20,
                                            bottom: 20
                                        }
                                    },
                                    legend: {
                                        display: true
                                    },
                                    maintainAspectRatio: false,
                                    responsive: true
                                }
                            });
                        });
                    </script>

                    <canvas id="earnings-chart" class="chart-canvas" height="350"></canvas>
                    <?php }  ?>
                </div>
            </div>

    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?php
        $unreaded_orders_messages = $general->unreaded_orders_messages();
        ?>
        <div class="widget">
            <div class="widget-title">
                <h5>Unreaded Orders Messages <a href="messages.php">See All</a></h5>
            </div>
            <div class="widget-content scrollbar">
                <?php if ($unreaded_orders_messages == 0) : ?>
                    <div class="empty-widget">
                        <p><i class="icon icon-newsletter"></i>There is nothing to display here.</p>
                    </div>
                <?php else: ?>
                    <ul class="messages-list">
                        <?php
                        foreach ($unreaded_orders_messages AS $uom):
                            ?>
                            <li>
                                <a href="orders.php?case=details&id=<?php echo make_safe($uom['sid']); ?>#message-<?php echo $uom['id']; ?>">
                                    <span class="order-id">#<?php echo make_safe($uom['order_id']); ?></span>
                                    <span class="service"><?php echo make_safe(get_service_title($uom['service_id'])); ?></span>
                                    <span class="customer"><strong><?php echo make_safe(get_customer($uom['customer_id'])); ?></strong>, On <?php echo make_safe(date('d F, Y h:i a',$uom['datetime'])); ?></span>
                                </a>
                            </li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <?php
        $unreaded_support_messages = $general->unreaded_support_messages();
        ?>
        <div class="widget">
            <div class="widget-title">
                <h5>Unreaded Support Messages <a href="support.php">See All</a></h5>
            </div>
            <div class="widget-content scrollbar">
                <?php if ($unreaded_support_messages == 0) : ?>
                    <div class="empty-widget">
                        <p><i class="icon icon-support-17"></i>There is nothing to display here.</p>
                    </div>
                <?php else: ?>
                    <ul class="messages-list">
                        <?php
                        foreach ($unreaded_support_messages AS $usm):
                            ?>
                            <li>
                                <a href="support.php?case=details&id=<?php echo make_safe($usm['id']); ?>">
                                    <span class="order-id"><strong class="badge badge-<?php if($usm['source'] == 'contact form'): ?>secondary<?php else: ?>warning<?php endif; ?>"><?php echo make_safe($usm['source']); ?></strong> <?php echo make_safe($usm['title']); ?></span>
                                    <span class="customer"><strong><?php echo make_safe($usm['username']); ?></strong>, On <?php echo make_safe(date('d F, Y h:i a',$usm['datetime'])); ?></span>
                                </a>
                            </li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php

include('footer.php');
?>