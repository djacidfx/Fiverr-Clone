<?php
if (isset($_COOKIE[$options['users_cookie_name']])) {
	$cookie = explode('_',$_COOKIE[$options['users_cookie_name']]);
	if (!empty($cookie[0]) AND !empty($cookie[1])) {
		$sql = "SELECT * FROM ss_customers WHERE username='$cookie[0]' AND active='1' LIMIT 1";
		$query = $mysqli->query($sql);
		if ($query->num_rows != 0) {
		$row = $query->fetch_assoc();
			if ($row['password'] == $cookie[1]) {
                $_SESSION['ss_solo_user'] = $row['id'];
			}
		}
	}
}
?>