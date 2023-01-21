<?php
session_start();
error_reporting(E_ERROR);
include("../include/config.php");
include("../include/connect.php");
include("include/functions.php");
include("include/nocsrf.php");
switch ($_GET['do']) {
case 'forget_password';
if (isset($_POST)) {
try
{
NoCSRF::check('fpass_token', $_POST, true, 600*10, false );
$email = make_safe(xss_clean($_POST['email']));
if (empty($email)) {
    echo notification('warning','Please Insert your email.');
} else {
    $admin = $general->check_email($email);
    if ($admin == 0) {
        echo notification('warning','entered email is invalid.');
    } else {
        $confirmation_link = rtrim($options['general_siteurl'],"/").'/admin/reset-password.php?e='.md5($email).'&a='.normalize_input($admin['activation_code']);
        $title = "Reset Password Link";
        $cont = file_get_contents('../include/email-templates/mail.html');
        $arr_1 = array('{HEADER}','{INSTRUCTIONS}','{LINK}','{LINKTEXT}','{SITENAME}','{SITELINK}');
        $arr_2 = array('Re','Click on the Link Below to Reset Your Password',normalize_input($confirmation_link),'Reset Password',normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
        $body = str_replace($arr_1,$arr_2,$cont);
        $content = array(
            'email' => normalize_input($admin['email']),
            'username' => normalize_input($admin['username']),
            'title' => normalize_input($title),
            'body' => normalize_input($body)
        );
        $send_email = send_email($options,$content);
        if ($send_email) {
            echo 1;
        } else {
            echo notification('danger','email wasn\'t sent, please check your server mail setting.');
        }
    }
}
}
catch ( Exception $e )
{
die($e->getMessage() . ' Form ignored.');
}
}
break;
default;
$fpass_token = NoCSRF::generate('fpass_token');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Microncer Solo | Forget Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="themes/default/libs/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="themes/default/css/login.css" rel="stylesheet">
    <script src="themes/default/libs/jquery/jquery.min.js"></script>
    <script src="themes/default/libs/bootstrap/bootstrap.min.js"></script>

	<script type="text/javascript">
        (function($) {
            "use strict";
            $(document).ready(function() {
                $("#reset_pass").on('click',function() {
                    var email = $("input#email").val();
                    var fpass_token = $("input#fpass_token").val();
                    if (email == "") {
                   $("div#message").html('<div class="alert alert-danger">Insert Email, Please.</div>');
                   return false;
                }
                $("#reset_pass").append("<i class='sp sp-circle'></i>");
                var dataString = 'email='+ email + '&fpass_token='+fpass_token;
                $.ajax({
                  type: "POST",
                  url: 'forget-password.php?do=forget_password',
                  data: dataString,
                  success: function(result) {
                  if (result == 1) {
                      $("#reset_pass").html("Reset Password");
                      $("div#message").html('<div class="alert alert-success">Reset password is sent to your entered email address.</div>');
                  } else {
                      $("#reset_pass").html("Reset Password");
                      $("div#message").html('<div class="alert alert-danger">Error Happened</div>');
                  }
                  }
                 });
                return false;
                });
            });
    })(jQuery);


    </script>
</head>

<body class="login-body">
    <div class="login-row">
        <div class="logo">
            <img src="themes/default/images/logo.svg" />
            <h1>Microncer<span>Solo</span></h1>
            <h3>Administration</h3>
        </div>
        <div class="login-panel">
        <div id="message"></div>

            <form class="form" action="" method="POST" role="form">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input class="form-control" name="email" id="email" type="text" autofocus>
                    </div>
                    <input type="hidden" name="fpass_token" id="fpass_token" value="<?php echo make_safe($fpass_token); ?>" />
                    <button type="submit" id="reset_pass" name="reset_pass" class="btn btn-success">Reset Password</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php 
}
?>