<?php include('header.php'); ?>
<div class="big-title">
    <h1>Settings</h1>
</div>
<?php
if (!empty($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
$options = $general->get_all_options();
switch ($case) {
case 'clear_cache';
if (isset($_POST['save'])) {
	$folder = '../cache';
	$delete = empty_templates_cache($folder);
	if ($delete) {
	$message = notification('success','All Cache Files Are Cleared.');
	} else {
	$message = notification('danger','Error Happened.');
	}
}   
?>
<div class="page-container">
<div class="page-header">
   <h4>Clear Cache</h4>
</div>
<div class="form">
	<?php if (isset($message)) {echo normalize_input($message);} else { ?>
		<form role="form" method="POST" action="">
              <div class="alert alert-warning">Are you sure that you want to clear all cached files ?</div>
            <div class="form-actions">
		  <button type="submit" name="save" class="btn btn-danger">Clear Cache</button>
            </div>
		</form>
    <?php } ?>
</div>
</div>
<?php 
break;
case 'social';
?>
<div class="page-container">
<div class="page-header">
    <h4>Social Settings</h4>
    <div class="actions">
        <a href="<?php echo help_links('settings_social'); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="ri-question-line"></i></a>
    </div>
</div>
<div class="form">
	<div class="ajax-form-response"></div>
		<form role="form" method="POST" action="" class="ajax-options-form">
		  <div class="form-group">
			<label for="facebook_account">Facebook Page</label>
			<input type="text" class="form-control" name="facebook_account" id="facebook_account" placeholder="https://facebook.com/yourpage" value="<?php echo make_safe($options['social_facebook_account']); ?>" />
		  </div>
		  <div class="form-group">
			<label for="twitter_account">Twitter Account</label>
			<input type="text" class="form-control" name="twitter_account" id="twitter_account" placeholder="http://twitter.com/your_id" value="<?php echo make_safe($options['social_twitter_account']); ?>" />
		  </div>
		  <div class="form-group">
			<label for="linkedin_account">Linkedin Account</label>
			<input type="text" class="form-control" name="linkedin_account" id="linkedin_account" placeholder="http://linkedin.com/account" value="<?php echo make_safe($options['social_linkedin_account']); ?>" />
		  </div>
		  <div class="form-group">
			<label for="youtube_account">Youtube Channel</label>
			<input type="text" class="form-control" name="youtube_account" id="youtube_account" placeholder="http://youtube.com/user/channel_name" value="<?php echo make_safe($options['social_youtube_account']); ?>" />
		  </div>
            <div class="form-group">
                <label for="instagram_account">Instagram Account</label>
                <input type="text" class="form-control" name="instagram_account" id="instagram_account" placeholder="http://instagram.com/user_id" value="<?php echo make_safe($options['social_instagram_account']); ?>" />
            </div>
            <div class="form-actions">
                <input type="hidden" name="options_set" value="Social" />
                <button type="submit" name="save" class="btn btn-dark">Save</button>
            </div>
		</form>
</div>
</div>
<?php 
break;

    case 'payment';
        ?>
<div class="page-container">
        <div class="page-header">
            <h4>Payment Settings</h4>
            <div class="actions">
                <a href="<?php echo help_links('settings_payment'); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="ri-question-line"></i></a>
            </div>
        </div>
        <div class="form">
            <div class="ajax-form-response"></div>
            <form role="form" method="POST" action="" class="ajax-options-form">
                <div class="form-group">
                    <label for="site_currency">Site Currency</label>
                    <select name="site_currency" id="site_currency" class="form-control">
                        <?php
                        $currencies = currencies();
                        foreach ($currencies as $key=>$value):
                            ?>
                            <option value="<?php echo normalize_input($value); ?>" <?php if($options['payment_site_currency'] == $value) {echo 'SELECTED';} ?>><?php echo '('.$value.') '.$key; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="manual">
                    <div class="form-group">
                        <label>
                            <input type="hidden" name="allow_offline" value="0" />
                            <input data-toggle="toggle" data-size="mini" type="checkbox" name="allow_offline" id="allow_offline" value="1" <?php if (isset($options['payment_allow_offline']) AND $options['payment_allow_offline'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable <strong>Offline Payments</strong> as Payment Method</span>
                        </label>
                    </div>
                    <div class="offline-wrapper payment-method">
                        <fieldset>
                            <legend>Offline Payment Setting</legend>
                            <div class="form-group">
                                <label for="offline_payment_title">Offline Payment Title</label>
                                <input type="text" class="form-control" name="offline_payment_title" id="offline_payment_title" value="<?php echo make_safe($options['payment_offline_payment_title']); ?>" />
                                <p class="help">What title you want to use for Offline Payments ? example : Cash, Bank Transfer .. etc.</p>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="paypal">
                <div class="form-group">
                    <label>
                        <input type="hidden" name="allow_paypal" value="0" />
                        <input data-toggle="toggle" data-size="mini" type="checkbox" name="allow_paypal" id="allow_paypal" value="1" <?php if (isset($options['payment_allow_paypal']) AND $options['payment_allow_paypal'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable <strong>PayPal</strong> as Payment Method</span>
                    </label>
                </div>
                <div class="paypal-wrapper payment-method">
                    <fieldset>
                        <legend>PayPal Setting <a href="<?php echo help_links('apis_paypal'); ?>" target="_blank"><i class="ri-question-fill"></i></a></legend>
                <div class="form-group">
                    <label>
                        <input type="hidden" name="paypal_sandbox" value="live" />
                        <input data-toggle="toggle" data-size="mini" type="checkbox" name="paypal_sandbox" id="paypal_sandbox" value="sandbox" <?php if (isset($options['payment_paypal_sandbox']) AND $options['payment_paypal_sandbox'] == 'sandbox') : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">PayPal SandBox</span>
                    </label>
                </div>
                <div class="paypal-live">
                    <h3>LIVE API CREDENTIALS</h3>
                    <div class="form-group">
                        <label for="paypal_client_id">PayPal Client ID</label>
                        <input type="text" class="form-control" name="paypal_client_id" id="paypal_client_id" value="<?php echo make_safe($options['payment_paypal_client_id']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="paypal_client_secret">PayPal Client Secret</label>
                        <input type="text" class="form-control" name="paypal_client_secret" id="paypal_client_secret" value="<?php echo make_safe($options['payment_paypal_client_secret']); ?>" />
                    </div>
                </div>
                <div class="paypal-sandbox">
                    <h3>SANDBOX API CREDENTIALS</h3>
                    <div class="form-group">
                        <label for="paypal_test_client_id">PayPal Client ID</label>
                        <input type="text" class="form-control" name="paypal_test_client_id" id="paypal_test_client_id" value="<?php echo make_safe($options['payment_paypal_test_client_id']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="paypal_test_client_secret">PayPal Client Secret</label>
                        <input type="text" class="form-control" name="paypal_test_client_secret" id="paypal_test_client_secret" value="<?php echo make_safe($options['payment_paypal_test_client_secret']); ?>" />
                    </div>
                </div>
                    </fieldset>
                </div>
                </div>
                <div class="stripe">
                <div class="form-group">
                    <label>
                        <input type="hidden" name="allow_stripe" value="0" />
                        <input data-toggle="toggle" data-size="mini" type="checkbox" name="allow_stripe" id="allow_stripe" value="1" <?php if (isset($options['payment_allow_stripe']) AND $options['payment_allow_stripe'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable <strong>Stripe</strong> as Payment Method</span>
                    </label>
                </div>
                <div class="stripe-wrapper payment-method">
                    <fieldset>
                        <legend>Stripe Setting<a href="<?php echo help_links('apis_stripe'); ?>" target="_blank"><i class="ri-question-fill"></i></a></legend>
                <div class="form-group">
                    <label>
                        <input type="hidden" name="stripe_test_mode" value="off" />
                        <input data-toggle="toggle" data-size="mini" type="checkbox" name="stripe_test_mode" id="stripe_test_mode" value="on" <?php if (isset($options['payment_stripe_test_mode']) AND $options['payment_stripe_test_mode'] == 'on') : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Stripe Test Mode</span>
                    </label>
                </div>
                <div class="stripe-live">
                <h3>STRIPE LIVE CREDENTIALS</h3>
                <div class="form-group">
                    <label for="stripe_public_live_key">Stripe Public Live Key</label>
                    <input type="text" class="form-control" name="stripe_public_live_key" id="stripe_public_live_key" value="<?php echo make_safe($options['payment_stripe_public_live_key']); ?>" />
                </div>
                <div class="form-group">
                    <label for="stripe_private_live_key">Stripe Private Live Key</label>
                    <input type="text" class="form-control" name="stripe_private_live_key" id="stripe_private_live_key" value="<?php echo make_safe($options['payment_stripe_private_live_key']); ?>" />
                </div>
                </div>
                <div class="stripe-test">
                <h3>STRIPE TEST CREDENTIALS</h3>
                <div class="form-group">
                    <label for="stripe_public_test_key">Stripe Public Test Key</label>
                    <input type="text" class="form-control" name="stripe_public_test_key" id="stripe_public_test_key" value="<?php echo make_safe($options['payment_stripe_public_test_key']); ?>" />
                </div>
                <div class="form-group">
                    <label for="stripe_private_test_key">Stripe Private Test Key</label>
                    <input type="text" class="form-control" name="stripe_private_test_key" id="stripe_private_test_key" value="<?php echo make_safe($options['payment_stripe_private_test_key']); ?>" />
                </div>
                </div>
                    </fieldset>
                </div>
                </div>
                <div class="razorpay">
                    <div class="form-group">
                        <label>
                            <input type="hidden" name="allow_razorpay" value="0" />
                            <input data-toggle="toggle" data-size="mini" type="checkbox" name="allow_razorpay" id="allow_razorpay" value="1" <?php if (isset($options['payment_allow_razorpay']) AND $options['payment_allow_razorpay'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable <strong>RazorPay</strong> as Payment Method</span>
                        </label>
                    </div>
                    <div class="razorpay-wrapper payment-method">
                        <fieldset>
                            <legend>RazorPay Setting<a href="<?php echo help_links('apis_stripe'); ?>" target="_blank"><i class="ri-question-fill"></i></a></legend>
                            <div class="form-group">
                                <label>
                                    <input type="hidden" name="razorpay_test_mode" value="off" />
                                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="razorpay_test_mode" id="razorpay_test_mode" value="on" <?php if (isset($options['payment_razorpay_test_mode']) AND $options['payment_razorpay_test_mode'] == 'on') : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">RazorPay Test Mode</span>
                                </label>
                            </div>
                            <div class="razorpay-live">
                                <h3>RazorPay LIVE Mode</h3>
                                <div class="form-group">
                                    <label for="razorpay_live_key_id">RazorPay Live Key ID</label>
                                    <input type="text" class="form-control" name="razorpay_live_key_id" id="razorpay_live_key_id" value="<?php echo make_safe($options['payment_razorpay_live_key_id']); ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="razorpay_live_key_secret">RazorPay Live Key Secret</label>
                                    <input type="text" class="form-control" name="razorpay_live_key_secret" id="razorpay_live_key_secret" value="<?php echo make_safe($options['payment_razorpay_live_key_secret']); ?>" />
                                </div>
                            </div>
                            <div class="razorpay-test">
                                <h3>RazorPay TEST Mode</h3>
                                <div class="form-group">
                                    <label for="razorpay_test_key_id">RazorPay Test Key ID</label>
                                    <input type="text" class="form-control" name="razorpay_test_key_id" id="razorpay_test_key_id" value="<?php echo make_safe($options['payment_razorpay_test_key_id']); ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="razorpay_test_key_secret">RazorPay Test Key Secret</label>
                                    <input type="text" class="form-control" name="razorpay_test_key_secret" id="razorpay_test_key_secret" value="<?php echo make_safe($options['payment_razorpay_test_key_secret']); ?>" />
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="form-actions">
                    <input type="hidden" name="options_set" value="Payment" />
                <button type="submit" name="save" class="btn btn-dark">Save</button>
                </div>
            </form>
        </div>
</div>
        <?php
        break;
case 'apis';
?>
<div class="page-container">
<div class="page-header">
    <h4>API's Settings</h4>
    <div class="actions">
        <a href="<?php echo help_links('settings_apis'); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="ri-question-line"></i></a>
    </div>
</div>
<div class="form">
	<div class="ajax-form-response"></div>
		<form role="form" method="POST" action="" class="ajax-options-form">
            <div class="form-group">
                <label>
                    <input type="hidden" name="allow_signup_recaptcha" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="allow_signup_recaptcha" id="allow_signup_recaptcha" value="1" <?php if (isset($options['api_allow_signup_recaptcha']) AND $options['api_allow_signup_recaptcha'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable ReCaptcha in Signup Page ?</span>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="allow_contact_recaptcha" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="allow_contact_recaptcha" id="allow_contact_recaptcha" value="1" <?php if (isset($options['api_allow_contact_recaptcha']) AND $options['api_allow_contact_recaptcha'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable ReCaptcha in Contact Page ?</span>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="allow_service_recaptcha" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="allow_service_recaptcha" id="allow_service_recaptcha" value="1" <?php if (isset($options['api_allow_service_recaptcha']) AND $options['api_allow_service_recaptcha'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable ReCaptcha in Service Inquiries Form ?</span>
                </label>
            </div>
		  <div class="form-group">
			<label for="recaptcha_public">ReCaptcha Site Key <a href="<?php echo help_links('apis_recaptcha'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
			<input type="text" class="form-control" name="recaptcha_public" id="recaptcha_public" value="<?php if (isset($options['api_recaptcha_public'])) : echo make_safe($options['api_recaptcha_public']); endif; ?>" />
		  </div>
		  <div class="form-group">
			<label for="recaptcha_private">ReCaptcha Secret Key<a href="<?php echo help_links('apis_recaptcha'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
			<input type="text" class="form-control" name="recaptcha_private" id="recaptcha_private" value="<?php if (isset($options['api_recaptcha_private'])) : echo make_safe($options['api_recaptcha_private']); endif; ?>" />
		  </div>
		  <div class="form-group">
			<label for="ga_tracking_number">Google Analytics Tracking Number</label>
			<input type="text" class="form-control" name="ga_tracking_number" id="ga_tracking_number" value="<?php if (isset($options['api_ga_tracking_number'])) : echo make_safe($options['api_ga_tracking_number']); endif; ?>" placeholder="UA-64616308-1" />
		  </div>
            <div class="form-actions">
                <input type="hidden" name="options_set" value="API" />
		  <button type="submit" name="save" class="btn btn-dark">Save</button>
            </div>
		</form>
</div>
</div>
<?php 
break;

case 'mail';
?>
<div class="page-container">
<div class="page-header">
    <h4>Mail Settings</h4>
    <div class="actions">
        <a href="<?php echo help_links('settings_mail'); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="ri-question-line"></i></a>
    </div>
</div>
<div class="form">
		<div class="ajax-form-response"></div>
		<form role="form" method="POST" action="" class="ajax-options-form">
		  <div class="form-group">
			<label>
			<input type="hidden" name="enable_contact" value="0" />
                <input data-toggle="toggle" data-size="mini" type="checkbox" name="enable_contact" id="enable_contact" value="1" <?php if (isset($options['mail_enable_contact']) AND $options['mail_enable_contact'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable Contact Form ?</span>
		    </label>
		  </div>
		  <div class="form-group">
			<label for="reciption_email">Reciption E-Mail</label>
			<input type="text" class="form-control" name="reciption_email" id="reciption_email" value="<?php if (isset($options['mail_reciption_email'])) : echo make_safe($options['mail_reciption_email']); endif; ?>" />
		  </div>
		  <div class="form-group">
			<label for="mail_method">Send Mail Method</label>
              <div><input type="radio" name="mail_method" onclick="javascript:ShowDiv('mail_div');" value="mail" <?php if ($options['mail_mail_method'] == 'mail') {echo 'CHECKED';} ?> /> <span class="checkbox-label">PHP Mail Function</span></div>
              <div><input type="radio" name="mail_method" onclick="javascript:ShowDiv('smtp_div');" value="smtp" <?php if ($options['mail_mail_method'] == 'smtp') {echo 'CHECKED';} ?> /> <span class="checkbox-label">SMTP</span></div>
              <div><input type="radio" name="mail_method" onclick="javascript:ShowDiv('sendgrid_div');" value="sendgrid" <?php if ($options['mail_mail_method'] == 'sendgrid') {echo 'CHECKED';} ?> /> <span class="checkbox-label">SendGrid API (Recommended)</span></div>
		  </div>
		  <div id="mail_div">
		  <div class="form-group">
			<label for="send_email">E-Mail</label>
			<input type="text" class="form-control" name="send_email" id="send_email" value="<?php echo make_safe($options['mail_send_email']); ?>" />
		  </div>
		  </div>
		  <div id="smtp_div">
		  <div class="form-group">
			<label for="smtp_host">SMTP Host</label>
			<input type="text" class="form-control" name="smtp_host" id="smtp_host" value="<?php echo make_safe($options['mail_smtp_host']); ?>" />
		  </div>
		  <div class="form-group">
			<label for="smtp_port">SMTP Port</label>
			<input type="text" class="form-control" name="smtp_port" id="smtp_port" value="<?php echo make_safe($options['mail_smtp_port']); ?>" />
		  </div>
		  <div class="form-group">
			<label for="smtp_username">SMTP Username</label>
			<input type="text" class="form-control" name="smtp_username" id="smtp_username" value="<?php echo make_safe($options['mail_smtp_username']); ?>" />
		  </div>
		  <div class="form-group">
			<label for="smtp_password">SMTP Password</label>
			<input type="text" class="form-control" name="smtp_password" id="smtp_password" value="<?php echo make_safe($options['mail_smtp_password']); ?>" />
		  </div>
		  
		  </div>
		  <div id="sendgrid_div">
		  <div class="form-group">
			<label for="sender_email">Sender E-Mail</label>
			<input type="text" class="form-control" name="sender_email" id="sender_email" value="<?php echo make_safe($options['mail_sender_email']); ?>" />
		  </div>
            <div class="form-group">
                <label for="sender_name">Sender Name</label>
                <input type="text" class="form-control" name="sender_name" id="sender_name" value="<?php echo make_safe($options['mail_sender_name']); ?>" />
            </div>
		  <div class="form-group">
			<label for="sendgrid_api_key">SendGrid API Key <a href="<?php echo help_links('apis_sendgrid'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
			<input type="text" class="form-control" name="sendgrid_api_key" id="sendgrid_api_key" value="<?php echo make_safe($options['mail_sendgrid_api_key']); ?>" />
		  </div>
			</div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="order_placed_email" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="order_placed_email" id="order_placed_email" value="1" <?php if (isset($options['mail_order_placed_email']) AND $options['mail_order_placed_email'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Recieve email when a customer place new order ?</span>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="order_start_email" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="order_start_email" id="order_start_email" value="1" <?php if (isset($options['mail_order_start_email']) AND $options['mail_order_start_email'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Send email to customer when the order is started ?</span>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="order_complete_email" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="order_complete_email" id="order_complete_email" value="1" <?php if (isset($options['mail_order_complete_email']) AND $options['mail_order_complete_email'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Send email to customer when the order is completed ?</span>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="order_cancel_email" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="order_cancel_email" id="order_cancel_email" value="1" <?php if (isset($options['mail_order_cancel_email']) AND $options['mail_order_cancel_email'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Send email to customer when the order is cancelled ?</span>
                </label>
            </div>
            <div class="form-actions">
                <input type="hidden" name="options_set" value="Mail" />
		  <button type="submit" name="save" class="btn btn-dark">Save</button>
            </div>
		</form>
</div>
</div>
<?php 
break;
case 'customers'; ?>

<div class="page-container">
        <div class="page-header">
            <h4>Customers Setting</h4>
            <div class="actions">
                <a href="<?php echo help_links('settings_customers'); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="ri-question-line"></i></a>
            </div>
        </div>
        <div class="form">
            <div class="ajax-form-response"></div>
            <form role="form" method="POST" action="" class="ajax-options-form">
                <div class="form-group">
                    <label>Activate New Customers</label>
                    <div><input type="radio" name="activate_new_users" value="by_email" <?php if (isset($options['users_activate_new_users']) AND $options['users_activate_new_users'] == 'by_email') {echo 'CHECKED';} ?> /> <span class="checkbox-label">By E-Mail</span></div>
                    <div><input type="radio" name="activate_new_users" value="direct" <?php if (isset($options['users_activate_new_users']) AND $options['users_activate_new_users'] == 'direct') {echo 'CHECKED';} ?> /> <span class="checkbox-label">Direct Activation</span></div>
                </div>
                <div class="form-group">
                    <label for="cookie_name">Cookie Name</label>
                    <input type="text" class="form-control" name="cookie_name" id="cookie_name" value="<?php if (isset($options['users_cookie_name'])) { echo make_safe($options['users_cookie_name']); } ?>" />
                </div>
                <div class="form-group">
                    <label for="cookie_expiration_time">Cookie Expiration Time</label>
                    <select class="form-control" name="cookie_expiration_time" id="cookie_expiration_time">
                        <?php
                        for ($c=1;$c<11;$c++) {
                            ?>
                            <option value="<?php echo normalize_input($c); ?>" <?php if (isset($options['users_cookie_expiration_time']) AND $options['users_cookie_expiration_time'] == $c) {echo 'SELECTED';} ?>><?php echo normalize_input($c); ?> Day(s)</option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <input type="hidden" name="enable_facebook_login" value="0" />
                        <input data-toggle="toggle" data-size="mini" type="checkbox" name="enable_facebook_login" id="enable_facebook_login" value="1" <?php if (isset($options['users_enable_facebook_login']) AND $options['users_enable_facebook_login'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable Facebook Login</span>
                    </label>
                </div>
                <div id="facebook-login">
                    <div class="form-group">
                        <label for="facebook_app_id">Facebook App ID <a href="<?php echo help_links('apis_facebook'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
                        <input type="text" class="form-control" name="facebook_app_id" id="facebook_app_id" value="<?php if(isset($options['users_facebook_app_id'])): echo make_safe($options['users_facebook_app_id']); endif; ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        <input type="hidden" name="enable_twitter_login" value="0" />
                        <input data-toggle="toggle" data-size="mini" type="checkbox" name="enable_twitter_login" id="enable_twitter_login" value="1" <?php if (isset($options['users_enable_twitter_login']) AND $options['users_enable_twitter_login'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable Twitter Login</span>
                    </label>
                </div>
                <div id="twitter-login">
                    <div class="form-group">
                        <label for="twitter_api_key">Twitter API Key <a href="<?php echo help_links('apis_twitter'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
                        <input type="text" class="form-control" name="twitter_api_key" id="twitter_api_key" value="<?php if(isset($options['users_twitter_api_key'])): echo make_safe($options['users_twitter_api_key']); endif; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="twitter_api_secret_key">Twitter API Secret Key <a href="<?php echo help_links('apis_twitter'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
                        <input type="text" class="form-control" name="twitter_api_secret_key" id="twitter_api_secret_key" value="<?php if(isset($options['users_twitter_api_secret_key'])): echo make_safe($options['users_twitter_api_secret_key']); endif; ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        <input type="hidden" name="enable_envato_login" value="0" />
                        <input data-toggle="toggle" data-size="mini" type="checkbox" name="enable_envato_login" id="enable_envato_login" value="1" <?php if (isset($options['users_enable_envato_login']) AND $options['users_enable_envato_login'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Enable Envato Login</span>
                    </label>
                </div>
                <div id="envato-login">
                    <div class="form-group">
                        <label for="envato_oauth_client_id">OAuth client ID<a href="<?php echo help_links('apis_envato'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
                        <input type="text" class="form-control" name="envato_oauth_client_id" id="envato_oauth_client_id" value="<?php if(isset($options['users_envato_oauth_client_id'])): echo make_safe($options['users_envato_oauth_client_id']); endif; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="envato_oauth_client_secret">OAuth client Secret<a href="<?php echo help_links('apis_envato'); ?>" target="_blank"><i class="ri-question-fill"></i></a></label>
                        <input type="text" class="form-control" name="envato_oauth_client_secret" id="envato_oauth_client_secret" value="<?php if(isset($options['users_envato_oauth_client_secret'])): echo make_safe($options['users_envato_oauth_client_secret']); endif; ?>" />
                    </div>
                </div>
                <div class="form-actions">
                    <input type="hidden" name="options_set" value="Users" />
                <button type="submit" name="save" class="btn btn-flat btn-dark">Save</button>
                </div>
            </form>
        </div>
</div>
        <?php
        break;
default;
    ?>
<div class="page-container">
<div class="page-header">
    <h4>General Settings</h4>
    <div class="actions">
        <a href="<?php echo help_links('settings_general'); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="ri-question-line"></i></a>
    </div>
</div>
<div class="form">
		<div class="ajax-form-response"></div>
		<form role="form" method="POST" action="" class="ajax-options-form">
		  <div class="form-group">
			<label for="siteurl">Site Url</label>
			<input type="text" class="form-control" name="siteurl" id="siteurl" placeholder="http://www.domain.com" value="<?php if(isset($options['general_siteurl'])): echo make_safe($options['general_siteurl']); endif; ?>" />
		  </div>
		  <div class="form-group">
			<label for="seo_title">Site Name</label>
			<input type="text" class="form-control" name="seo_title" id="seo_title" placeholder="your site title" value="<?php if(isset($options['general_seo_title'])):  echo make_safe($options['general_seo_title']); endif; ?>" />
		  </div>
		  
		  <div class="form-group">
			<label for="seo_keywords">SEO Keywords</label>
			<input type="text" class="form-control tags" name="seo_keywords" id="seo_keywords" placeholder="" value="<?php if(isset($options['general_seo_keywords'])):  echo make_safe($options['general_seo_keywords']); endif; ?>" />
		  </div>
		  <div class="form-group">
			<label for="seo_description">SEO Description</label>
			<textarea class="form-control" name="seo_description" id="seo_description" rows="3" placeholder="some words about the site .. don't exceed 255 character."><?php if(isset($options['general_seo_description'])): echo make_safe($options['general_seo_description']); endif; ?></textarea>
		  </div>
		  <div class="form-group">
			<label for="site_language">Site Language</label>
			<select name="site_language" id="site_language" class="form-control">
				<?php
				$lpath = '../languages/';
				$lresults = glob($lpath . "*");
					foreach ($lresults as $lresult) {
						if ($lresult === '.' or $lresult === '..') continue;
						if(is_dir($lresult)) {
						
						echo "
						<option value='".str_replace($lpath,'',$lresult)."' ";
						if (isset($options['general_site_language']) AND $options['general_site_language'] == str_replace($lpath,'',$lresult)) {
						echo 'SELECTED';
						}
						echo ">".ucfirst(str_replace($lpath,'',$lresult))."</option>";		
						}
						}
						?>						
			</select>
		   </div>
		   <div class="form-group">
			<label for="site_theme">Site Theme</label>
			<select name="site_theme" id="site_theme" class="form-control">
				<?php
				$path = '../themes/';
				$results = glob($path . "*");
					foreach ($results as $result) {
						if ($result === '.' or $result === '..') continue;
						if(is_dir($result)) {
						
						echo "
						<option value='".str_replace($path,'',$result)."'";
						if (isset($options['general_site_theme']) AND $options['general_site_theme'] == str_replace($path,'',$result)) {
						echo 'SELECTED';
						}
						echo ">".ucfirst(str_replace($path,'',$result))."</option>";		
						}
						}
						?>						
			</select>
		   </div>
            <div class="form-actions">
                <input type="hidden" name="options_set" value="General" />
		        <button type="submit" name="save" class="btn btn-dark">Save</button>
            </div>
		</form>
</div>
</div>
<?php } ?>
<?php include('footer.php'); ?>
 