<?php
if (isset($options['users_enable_envato_login']) AND $options['users_enable_envato_login'] == 1) {
	if (!empty($options['users_envato_oauth_client_id']) AND !empty($options['users_envato_oauth_client_secret'])) {
		include 'envato.class.php';
		$client_id = $options['users_envato_oauth_client_id'];
		$redirect_uri = $options['general_siteurl'];
		$client_secret = $options['users_envato_oauth_client_secret'];

		$envato = new Envato($client_id,$client_secret,$redirect_uri);
		if(isset($_GET["code"])) {
			$tokens = $envato->get_tokens($_GET['code']);
			$envato->set_access_token($tokens['access_token']);

			$username = $envato->user_username();
			$email = $envato->user_email();
			$result = $general->check_envato_login($username,$email);
			if ($result != 1) {
				header('Location: '.rtrim($options['general_siteurl'],'/'));
			}
		} else {
			$envato_login_url = $envato->login_url();
			$smarty->assign('envato_login_url',$envato_login_url);
		}
	}
}
?>
