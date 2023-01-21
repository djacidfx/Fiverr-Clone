<?php
include('include/autoloader.php');
use Razorpay\Api\Api;
$slug = make_safe($_GET['slug']);
$service = $general->service($slug);
if ($service == 0) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
foreach ($service AS $key => $value) {
    $smarty->assign('service_' . $key, $value);
}

if (isset($_SESSION['ss_solo_user'])) {
    $price = $service['price'];
    $title = $service['title'];
    $delivery = $service['delivery']*(60*60*24);
    $_SESSION['current_service_id'] = $service['id'];




    if ($options['payment_allow_razorpay'] == 1) {
        if ($options['payment_razorpay_test_mode'] == 'on') {
            $keyId = $options['payment_razorpay_test_key_id'];
            $keySecret = $options['payment_razorpay_test_key_secret'];
        }

        if ($options['payment_razorpay_live_mode'] == 'on') {
            $keyId = $options['payment_razorpay_live_key_id'];
            $keySecret = $options['payment_razorpay_live_key_secret'];
        }



        $api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
        $orderData = [
            'receipt'         => uniqid(),
            'amount'          => $price * 100,
            'currency'        => $options['payment_site_currency'],
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $api->order->create($orderData);

        $razorpayOrderId = $razorpayOrder['id'];

        $_SESSION['razorpay_order_id'] = $razorpayOrderId;

        $displayAmount = $amount = $orderData['amount'];


        $data = [
            "key"               => $keyId,
            "amount"            => $amount,
            "name"              => $title,
            "description"       => $service['description'],
            "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
            "prefill"           => [
                "name"              => $user['username'],
                "email"             => $user['email'],
                "contact"           => "",
            ],
            "notes"             => [
                "service_id"           => $service['id'],
                "customer_id" => $user['id'],
            ],
            "theme"             => [
                "color"             => "#F37254"
            ],
            "order_id"          => $razorpayOrderId,
        ];



        $json = json_encode($data);
        $smarty->assign('razorpay_json', $json);
    }
    if ($options['payment_allow_paypal'] == 1) {
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
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');
        $item = new \PayPal\Api\Item();
        $item->setName($title);
        $item->setCurrency($options['payment_site_currency']);
        $item->setQuantity(1);
        $item->setPrice($price);

        $itemList = new \PayPal\Api\ItemList();
        $itemList->setItems(array($item));
        $amount = new \PayPal\Api\Amount();
        $amount->setTotal($price);
        $amount->setCurrency($options['payment_site_currency']);

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);

        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl(rtrim($options['general_siteurl'],'/').'/process-order')
            ->setCancelUrl(rtrim($options['general_siteurl'],'/'));

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($apiContext);
            $approval_link = $payment->getApprovalLink();
            $smarty->assign('approval_link',$approval_link);
        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            // This will print the detailed information on the exception.
            //REALLY HELPFUL FOR DEBUGGING
            die($ex->getData());
        }
    }
    if ($options['payment_allow_stripe'] == 1) {
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
        $smarty->assign('stripe_pub_key',$pubkey);
        if(isset($_POST['stripeToken'])) {
            // Chargeble amount
            $description = $title;

            try {

                $charge = Stripe_Charge::create(array(
                        "amount" => $price * 100,
                        "currency" => strtolower($options['payment_site_currency']),
                        "source" => $_POST['stripeToken'],
                        "description" => $description)
                );

                if (isset($charge->card->address_zip_check) and $charge->card->address_zip_check == "fail") {
                    throw new Exception("zip_check_invalid");
                } else if (isset($charge->card->address_line1_check) and $charge->card->address_line1_check == "fail") {
                    throw new Exception("address_check_invalid");
                } else if (isset($charge->card->cvc_check) and $charge->card->cvc_check == "fail") {
                    throw new Exception("cvc_check_invalid");
                }
                // Payment has succeeded, no exceptions were thrown or otherwise caught

                $result = "success";

            } catch (Stripe_CardError $e) {

                $error = $e->getMessage();
                $result = "declined";

            } catch (Stripe_InvalidRequestError $e) {
                $result = "declined";
            } catch (Stripe_AuthenticationError $e) {
                $result = "declined";
            } catch (Stripe_ApiConnectionError $e) {
                $result = "declined";
            } catch (Stripe_Error $e) {
                $result = "declined";
            } catch (Exception $e) {

                if ($e->getMessage() == "zip_check_invalid") {
                    $result = "declined";
                } else if ($e->getMessage() == "address_check_invalid") {
                    $result = "declined";
                } else if ($e->getMessage() == "cvc_check_invalid") {
                    $result = "declined";
                } else {
                    $result = "declined";
                }
            }
            if ($result == 'success') {
                $transaction_id = $charge->id;
                $amount = $charge->amount / 100;
                $datetime = time();
                $day = date('j');
                $month = date('n');
                $year = date('Y');
                $ip = getRealIpAddr();
                $uorder_id = uniqid();
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
                $add = $mysqli->query("INSERT INTO ss_sales (service_id,order_id,payment_method,customer_id,transaction_id,amount,currency,order_datetime,day,month,year,completed,start_datetime,expected_datetime,complete_datetime) VALUES ('$service[id]','$uorder_id','stripe','$_SESSION[ss_solo_user]','$transaction_id','$amount','$options[payment_site_currency]','$datetime','$day','$month','$year','$completed','$start_datetime','$expected_datetime','$complete_datetime')");
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
                            'email' => $options['mail_reciption_email'],
                            'username' => $options['general_seo_title'],
                            'title' => $title,
                            'body' => $body
                        );
                        $send_email = send_email($options, $content);
                    }


                    header('Location:' . rtrim($options['general_siteurl'], '/') . '/dashboard/orders/order/' . $uniq_order_id);
                } else {
                    die($mysqli->error);
                    header('Location:' . rtrim($options['general_siteurl'], '/'));

                }
            } else {
                header('Location:' . rtrim($options['general_siteurl'], '/'));
            }
        }
    }
}
$smarty->assign('current_year', date('Y'));
$smarty->assign('seo_title',$options['general_seo_title']);
$smarty->assign('seo_description',$options['general_seo_description']);
$smarty->assign('seo_keywords',$options['general_seo_keywords']);
$smarty->display('pay.html');

?>