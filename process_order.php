<?php
include('include/autoloader.php');
if (!isset($_SESSION['ss_solo_user'])) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
$method = make_safe($_GET['method']);
switch ($method) {
    case 'razorpay';
        if ($options['payment_razorpay_test_mode'] == 'on') {
            $keyId = $options['payment_razorpay_test_key_id'];
            $keySecret = $options['payment_razorpay_test_key_secret'];
        }

        if ($options['payment_razorpay_live_mode'] == 'on') {
            $keyId = $options['payment_razorpay_live_key_id'];
            $keySecret = $options['payment_razorpay_live_key_secret'];
        }


        $success = true;

        $error = "Payment Failed";

        if (empty($_POST['razorpay_payment_id']) === false) {
            $api = new Api($keyId, $keySecret);

            try
            {
                // Please note that the razorpay order ID must
                // come from a trusted source (session here, but
                // could be database or something else)
                $attributes = array(
                    'razorpay_order_id' => $_SESSION['razorpay_order_id'],
                    'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                    'razorpay_signature' => $_POST['razorpay_signature'],
                );

                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }



        if ($success === true) {
            $service = $general->service_by_id($_POST['service_id']);
            $delivery = $service['delivery'] * (60 * 60 * 24);
            $razorpay_order_id = $_SESSION['razorpay_order_id'];
            $razorpay_payment_id = $_POST['razorpay_payment_id'];
            $price = $service['price'];
            $datetime = time();
            $day = date('j');
            $month = date('n');
            $year = date('Y');
            $order_id = uniqid();
            $ip = getRealIpAddr();
            if ($service['digital_download'] == 1) {
                $completed = 1;
                $start_datetime = time();
                $expected_datetime = time();
                $complete_datetime = time();
            } else {
                $completed = 0;
                $start_datetime = 0;
                $expected_datetime = 0;
                $complete_datetime = 0;
            }
            $add = $mysqli->query("INSERT INTO ss_sales (service_id,order_id,payment_method,customer_id,transaction_id,amount,currency,order_datetime,day,month,year,completed,start_datetime,expected_datetime,complete_datetime) VALUES ('$service[id]','$order_id','razorpay','$_SESSION[ss_solo_user]','$razorpay_payment_id','$price','$options[payment_site_currency]','$datetime','$day','$month','$year','$completed','$start_datetime','$expected_datetime','$complete_datetime')");
            if ($add) {
                $order_id = $mysqli->insert_id;
                unset($_SESSION['current_service_id']);
                $uniq_order_id = get_unique_order_id($order_id);
                if (isset($options['mail_order_placed_email']) and $options['mail_order_placed_email'] == 1) {
                    $dashboard_link = rtrim($options['general_siteurl'], '/') . '/admin/';

                    $template = $general->email_template('new-order');
                    $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
                    $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
                    $arr_1 = array('{LINK}', '{SITENAME}', '{SITELINK}');
                    $arr_2 = array(normalize_input($dashboard_link), normalize_input($options['general_seo_title']), normalize_input($options['general_siteurl']));
                    $body = str_replace($arr_1, $arr_2, $cont);


                    $content = array(
                        'email' => normalize_input($options['mail_reciption_email']),
                        'username' => normalize_input($options['general_seo_title']),
                        'title' => normalize_input($title),
                        'body' => normalize_input($body)
                    );
                    $send_email = send_email($options, $content);
                }
                header('Location:' . rtrim($options['general_siteurl'], '/') . '/dashboard/orders/order/' . $uniq_order_id);
            } else {
                header('Location:' . rtrim($options['general_siteurl'], '/'));
            }
        }
    break;
    default;
        if ($options['payment_allow_paypal'] == 1) {
            if ($options['payment_paypal_sandbox'] == 'sandbox') {
                $apiContext = new \PayPal\Rest\ApiContext(
                    new \PayPal\Auth\OAuthTokenCredential(
                        $options['payment_paypal_test_client_id'],     // ClientID
                        $options['payment_paypal_test_client_secret']      // ClientSecret
                    )
                );
                $apiContext->setConfig(
                    array(
                        'log.LogEnabled' => false,
                        'log.FileName' => 'PayPal.log',
                        'log.LogLevel' => 'DEBUG',
                        'mode' => 'sandbox'
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
// Get payment object by passing paymentId
            $paymentId = $_GET['paymentId'];
            $payment = PayPal\Api\Payment::get($paymentId, $apiContext);
            $payerId = $_GET['PayerID'];

// Execute payment with payer id
            $execution = new PayPal\Api\PaymentExecution();
            $execution->setPayerId($payerId);

            try {
                // Execute payment
                $result = $payment->execute($execution, $apiContext);
                if ($result->state == 'approved') {
                    $arr = json_decode($result, true);
                    $service = $general->service_by_id($_SESSION['current_service_id']);
                    $delivery = $service['delivery'] * (60 * 60 * 24);
                    $payer_id = $arr['payer']['payer_info']['payer_id'];
                    $payer_email = $arr['payer']['payer_info']['email'];
                    $transaction_id = $arr['transactions'][0]['related_resources'][0]['sale']['id'];
                    $amount = $arr['transactions'][0]['amount']['total'];
                    $datetime = time();
                    $day = date('j');
                    $month = date('n');
                    $year = date('Y');
                    $order_id = uniqid();
                    $ip = getRealIpAddr();
                    if ($service['digital_download'] == 1) {
                        $completed = 1;
                        $start_datetime = time();
                        $expected_datetime = time();
                        $complete_datetime = time();
                    } else {
                        $completed = 0;
                        $start_datetime = 0;
                        $expected_datetime = 0;
                        $complete_datetime = 0;
                    }
                    $add = $mysqli->query("INSERT INTO ss_sales (service_id,order_id,payment_method,customer_id,transaction_id,amount,currency,order_datetime,day,month,year,completed,start_datetime,expected_datetime,complete_datetime) VALUES ('$service[id]','$order_id','paypal','$_SESSION[ss_solo_user]','$transaction_id','$amount','$options[payment_site_currency]','$datetime','$day','$month','$year','$completed','$start_datetime','$expected_datetime','$complete_datetime')");
                    if ($add) {
                        $order_id = $mysqli->insert_id;
                        unset($_SESSION['current_service_id']);
                        $uniq_order_id = get_unique_order_id($order_id);
                        if (isset($options['mail_order_placed_email']) and $options['mail_order_placed_email'] == 1) {
                            $dashboard_link = rtrim($options['general_siteurl'], '/') . '/admin/';

                            $template = $general->email_template('new-order');
                            $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
                            $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
                            $arr_1 = array('{LINK}', '{SITENAME}', '{SITELINK}');
                            $arr_2 = array(normalize_input($dashboard_link), normalize_input($options['general_seo_title']), normalize_input($options['general_siteurl']));
                            $body = str_replace($arr_1, $arr_2, $cont);


                            $content = array(
                                'email' => normalize_input($options['mail_reciption_email']),
                                'username' => normalize_input($options['general_seo_title']),
                                'title' => normalize_input($title),
                                'body' => normalize_input($body)
                            );
                            $send_email = send_email($options, $content);
                        }
                        header('Location:' . rtrim($options['general_siteurl'], '/') . '/dashboard/orders/order/' . $uniq_order_id);
                    } else {
                        header('Location:' . rtrim($options['general_siteurl'], '/'));
                    }
                }


            } catch (PayPal\Exception\PayPalConnectionException $ex) {
                die($ex);
            } catch (Exception $ex) {
                die($ex);
            }
        }
}

?>