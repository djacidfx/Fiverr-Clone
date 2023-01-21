<?php
require "twitteroauth-master/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
if (isset($options['users_enable_twitter_login']) AND $options['users_enable_twitter_login'] == 1) {
	if (!empty($options['users_twitter_api_key']) AND !empty($options['users_twitter_api_secret_key'])) {
		define('TW_CONSUMER_KEY', $options['users_twitter_api_key']);
		define('TW_CONSUMER_SECRET', $options['users_twitter_api_secret_key']);
		define('TW_REDIRECT_URL', $options['general_siteurl']);
		if (!isset($_SESSION['ss_solo_user'])) {
			if ($_GET['oauth_token'] || $_GET['oauth_verifier']) {
				$connection = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
				$access_token = $connection->oauth('oauth/access_token', array('oauth_verifier' => $_REQUEST['oauth_verifier'], 'oauth_token' => $_GET['oauth_token']));
				$connection = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
				$user_info = $connection->get('account/verify_credentials', array('include_email' => true));
				$oauth_token = $access_token['oauth_token'];
				$oauth_token_secret = $access_token['oauth_token_secret'];
				$user_id = $user_info->id;
				$username = $user_info->screen_name;
				$email = $user_info->email;
				$result = $general->check_twitter_login($user_id, $username, $email);
				$general->update_customer_activity($result);
				if ($result != 1) {
					header('Location: ' . rtrim($options['general_siteurl'], '/'));
				}
			} else {
				$connection = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET);
				$request_token = $connection->oauth("oauth/request_token", array("oauth_callback" => TW_REDIRECT_URL));
				$_SESSION['oauth_token'] = $request_token['oauth_token'];
				$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
				$twitter_login_url = $connection->url("oauth/authorize", array("oauth_token" => $request_token['oauth_token']));
				$smarty->assign('twitter_login_url', $twitter_login_url);
			}
		}
	}
}
?>
