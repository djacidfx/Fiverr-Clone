<?php
session_start();
error_reporting(E_ERROR);
include("../include/config.php");
include("../include/connect.php");
include("include/functions.php");
include("include/nocsrf.php");
switch ($_GET['do']) {
case 'reset_password';
if (isset($_POST)) {
try
{
NoCSRF::check('rpass_token', $_POST, true, 600*10, false );
$new_password = make_safe(xss_clean($_POST['new_password']));
$confirm_new_password = make_safe(xss_clean($_POST['confirm_new_password']));
$admin_id = make_safe(xss_clean($_POST['admin_id']));
if (empty($new_password)) {
    echo notification('warning','Please Insert your new password.');
} elseif (empty($confirm_new_password)) {
    echo notification('warning','Please confirm your new password.');
} elseif ($new_password != $confirm_new_password) {
    echo notification('warning','password must match the confirmation.');
} else {
    $enc_pass = md5($new_password);
    $update = $mysqli->query("UPDATE ss_admin SET password='$enc_pass' WHERE id='$admin_id'");
    if ($update) {
        $new_activation_code = genRandomString(24);
        $mysqli->query("UPDATE ss_admin SET activation_code='$activation_code' WHERE id='$admin_id'");
        echo 1;
    } else {
        echo notification('danger','error happened.');
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
$rpass_token = NoCSRF::generate('rpass_token');
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
                $("#save_pass").on('click',function() {
                    var new_password = $("input#new_password").val();
                    var confirm_new_password = $("input#confirm_new_password").val();
                    var admin_id = $("input#admin_id").val();
                    var rpass_token = $("input#rpass_token").val();
                    if (new_password == "") {
                   $("div#message").html('<div class="alert alert-danger">Insert New Password, Please.</div>');
                   return false;
                }
                if (confirm_new_password == "") {
                    $("div#message").html('<div class="alert alert-danger">Confirm New Password, Please.</div>');
                    return false;
                }
                $("#save_pass").append("<i class='sp sp-circle'></i>");
                var dataString = 'new_password='+ new_password + '&confirm_new_password='+confirm_new_password+'&admin_id='+admin_id+'&rpass_token='+rpass_token;
                $.ajax({
                  type: "POST",
                  url: 'reset-password.php?do=reset_password',
                  data: dataString,
                  success: function(result) {
                  if (result == 1) {
                      $("#reset_pass").html("Save Password");
                      $("div#message").html('<div class="alert alert-success">You saved your password successfully, <a href="login.php" class="alert-link">Login</a></div>');

                  } else {
                      $("#reset_pass").html("Save Password");
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
<div id="wrapper">
    <div class="container">
        <div class="row login-row">
            <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3">
                <div class="logo">
                    <img src="themes/default/images/logo.svg" />
                    <h1>Microncer<span>Solo</span></h1>
                    <h3>Administration</h3>
                </div>

				<div class="login-panel">
                    <?php
                    $a = make_safe(xss_clean($_GET['a']));
                    $e = make_safe(xss_clean($_GET['e']));
                    if (empty($a) OR empty($e)) {
                        echo notification('warning','You have followed wrong link or your link has expired.');
                    } else {
                        $query = $mysqli->query("SELECT * FROM ss_admin WHERE activation_code='$a'");
                        if ($query->num_rows == 0) {
                            echo notification('warning','You have followed wrong link or your link has expired.');
                        } else {
                            $row = $query->fetch_row();
                            if (md5($row['email']) != $e) {
                                echo notification('warning','You have followed wrong link or your link has expired.');
                            } else {
                                ?>
                                <div id="message"></div>
                                <form class="form" action="" method="POST" role="form">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input class="form-control" name="new_password" id="new_password" type="password" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_new_password">Confirm New Password</label>
                                        <input class="form-control" name="confirm_new_password" id="confirm_new_password" type="password">
                                    </div>
                                    <input type="hidden" name="rpass_token" id="rpass_token" value="<?php echo make_safe($rpass_token); ?>" />
                                    <input type="hidden" name="admin_id" id="admin_id" value="<?php echo make_safe($row['id']); ?>" />
                                    <button type="submit" id="save_pass" name="save_pass" class="btn btn-success">Save Password</button>
                                </form>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php 
}
?>