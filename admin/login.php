<?php
session_start();
error_reporting(E_ERROR);
include("../include/config.php");
include("../include/connect.php");
include("include/functions.php");
include("include/nocsrf.php");
if (isset($_SESSION['microncer_solo'])) {
    header("location:index.php");
    exit;
}
switch ($_GET['do']) {
case 'login';
if (isset($_POST)) {
try
{
NoCSRF::check('login_token', $_POST, true, 6000*10, false );
$username = make_safe(xss_clean($_POST['username']));
$password = make_safe(xss_clean($_POST['password']));
if (!empty($username) AND !empty($password)) {
$sql = "SELECT * FROM ss_admin WHERE username='$username' OR email='$username' LIMIT 1";
$query = $mysqli->query($sql);
if ($query->num_rows > 0) {
$row = $query->fetch_assoc();
if ($row['password'] == md5($password)) {
$_SESSION['microncer_solo'] = $row['id'];
echo 1;		
} else {
echo 0;
}
} else {
echo 0;
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
$login_token = NoCSRF::generate('login_token');
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Microncer Solo | Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="themes/default/libs/bootstrap/bootstrap.min.css">
    <link href="themes/default/css/login.css" rel="stylesheet">
    <script src="themes/default/libs/jquery/jquery.min.js"></script>
    <script src="themes/default/libs/bootstrap/bootstrap.min.js"></script>

	<script type="text/javascript">
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('button[name="login"]').on('click',function() {
                    var username = $('input[name="username"]').val();
                    var password = $('input[name="password"]').val();
                    var login_token = $('input[name="login_token"]').val();
                    if (username == "") {
                        $("div#message").html('<div class="alert alert-danger">Insert Username, Please.</div>');
                        return false;
                    }
                    if (password == "") {
                        $("div#message").html('<div class="alert alert-danger">Insert Password, Please.</div>');
                        return false;
                    }
                    $(this).append(' <span class="sp-circle"></span>');
                    var dataString = 'username='+ username + '&password=' + password+'&login_token='+login_token;
                    $.ajax({
                          type: "POST",
                          url: 'login.php?do=login',
                          data: dataString,
                          success: function(result) {
                          if (result == 1) {
                              setTimeout(
                                  function()
                                  {
                                      document.location.href = 'index.php';
                                  }, 2000);

                          } else {
                              $('button[name="login"] span').remove();
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
                        <label for="username">Username or E-Mail</label>
                        <input class="form-control" name="username" id="username" type="text" autofocus>
                    </div>
                    <div class="form-group">
                    <label for="password">Password <a href="forget-password.php">Forget Password ?</a></label>
                        <input class="form-control" name="password" id="password" type="password">
                    </div>
                    <input type="hidden" name="login_token" id="login_token" value="<?php echo make_safe($login_token); ?>" />
                    <button type="submit" id="login" name="login" class="btn btn-success">Login</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php 
}
?>