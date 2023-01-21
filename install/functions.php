<?php
function make_safe($str)
{   global $mysqli;
    return $mysqli->real_escape_string(trim($str));
}

function normalize_input($input) {
    return trim($input);
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
		$ip=$_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function genRandomString($length) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

function notification($type,$text) {
	return '<div class="alert alert-'.$type.'">'.$text.'</div>';
}

function curPageURL() 
{
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"])) {
	$https = $_SERVER["HTTPS"]; 
	}
	if (isset($https) AND $https == "on") {$pageURL .= "s";} else {$pageURL .= "";}
	$pageURL .= "://";
	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	return $pageURL;
}