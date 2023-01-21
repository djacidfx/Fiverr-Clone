<?php
session_start();
if(!isset($_SESSION['microncer_solo'])) {
header("location:login.php");
exit;
}
include('include/autoloader.php');
$unreaded_messages = $general->unread_messages_count();
if (isset($_GET['case'])) {
    $case = make_safe($_GET['case']);
} else {
    $case = '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">	
    <title>Microncer Solo | Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="themes/default/libs/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="themes/default/libs/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="themes/default/css/icons.css">
    <link rel="stylesheet" href="themes/default/libs/sweetalert/sweetalert2.css">
    <link rel="stylesheet" href="themes/default/libs/jBox/jBox.all.css">
    <link rel="stylesheet" href="themes/default/libs/bootstrap-toggle/bootstrap4-toggle.min.css">
    <link href="themes/default/libs/filer/jquery.filer.css" type="text/css" rel="stylesheet" />
    <link href="themes/default/libs/filer/jquery.filer-dragdropbox-theme.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="themes/default/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="themes/default/css/style.css?v=123">
    <link rel="stylesheet" href="themes/default/css/media.css">
	<script src="themes/default/libs/jquery/jquery.min.js"></script>
	<script src="themes/default/libs/jquery-ui/jquery-ui.js"></script>
    <script src="themes/default/libs/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="themes/default/libs/jBox/jBox.all.min.js"></script>
    <script src="themes/default/libs/jquery-checkall/jquery_checkall.js"></script>
    <script src="themes/default/libs/tinymce/tinymce.min.js"></script>
    <script src="themes/default/libs/tinymce/tinymce-function.js"></script>
    <script src="themes/default/libs/filer/jquery.filer.min.js"></script>
    <script src="themes/default/js/filer-functions.js"></script>
    <script src="themes/default/libs/sweetalert/sweetalert2.min.js"></script>
    <script src="themes/default/libs/bootstrap-toggle/bootstrap4-toggle.min.js"></script>
    <script src="themes/default/libs/perfect-scrollbar/perfect-scrollbar.jquery.min.js"></script>
    <script src="themes/default/libs/tagsinput/tagsinput.js"></script>
    <script src="themes/default/libs/chart/chart.min.js"></script>
	<script src="themes/default/js/functions.js"></script>

</head>
<body class="skin-base animate">

    <?php
    $current_page = str_replace('.php', '', $currenttab);

    ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo"><img src="themes/default/images/logo.svg" /></a>
            <a href="index.php" class="sidebar-logo-text">Microncer<span>Solo</span></a>
        </div>
        <div class="sidebar-body scrollbar">
            <div class="nav-group <?php if(in_array($current_page, array('index','categories','services','customers','orders','messages','pages','support','slider','updates','email_templates'))) {echo 'show'; } ?>">
                <div class="nav-group-label">Management</div>
                <ul class="nav-sidebar style-two">
                    <li class="nav-item <?php if ($current_page == 'index') {echo 'active';} ?>"><a class="nav-link" href="index.php"><i class="ri-dashboard-3-line"></i><span>Dashboard</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'categories') {echo 'active';} ?>"><a class="nav-link" href="categories.php"><i class="ri-file-list-line"></i><span>Categories</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'services') {echo 'active';} ?>"><a class="nav-link" href="services.php"><i class="ri-briefcase-4-line"></i><span>Services</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'customers') {echo 'active';} ?>"><a class="nav-link" href="customers.php"><i class="ri-user-3-line"></i><span>Customers</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'orders') {echo 'active';} ?>"><a class="nav-link" href="orders.php"><i class="ri-service-line"></i><span>Orders</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'messages') {echo 'active';} ?>"><a class="nav-link" href="messages.php"><i class="ri-mail-line"></i><span>Messages <?php if($unreaded_messages > 0): ?><span class="badge badge-warning"><?php echo make_safe($unreaded_messages); ?></span><?php endif; ?></span></a></li>
                    <li class="nav-item <?php if ($current_page == 'pages') {echo 'active';} ?>"><a class="nav-link" href="pages.php"><i class="ri-pages-line"></i><span>Pages</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'support') {echo 'active';} ?>"><a class="nav-link" href="support.php"><i class="ri-customer-service-2-line"></i><span>Support</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'slider') {echo 'active';} ?>"><a class="nav-link" href="slider.php"><i class="ri-slideshow-line"></i><span>Slider</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'email_templates') {echo 'active';} ?>"><a class="nav-link" href="email_templates.php"><i class="ri-pencil-ruler-line"></i><span>E-Mail Templates</span></a></li>
                    <li class="nav-item <?php if ($current_page == 'updates') {echo 'active';} ?>"><a class="nav-link" href="updates.php"><i class="ri-refresh-line"></i><span>System Updates</span></a></li>
                </ul>
            </div>
            <div class="nav-group <?php if($current_page == 'setting') {echo 'show'; } ?>">
                <div class="nav-group-label">Settings</div>
                <ul class="nav-sidebar style-two">
                    <li class="nav-item <?php if ($current_page == 'setting' AND empty($case)) {echo 'active';} ?>">
                        <a href="setting.php" class="nav-link"><span>General Settings</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'setting' AND $case == 'payment') {echo 'active';} ?>">
                        <a href="setting.php?case=payment" class="nav-link"><span>Payment Settings</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'setting' AND $case == 'customers') {echo 'active';} ?>">
                        <a href="setting.php?case=customers" class="nav-link"><span>Customers Settings</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'setting' AND $case == 'apis') {echo 'active';} ?>">
                        <a href="setting.php?case=apis" class="nav-link"><span>API's Settings</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'setting' AND $case == 'mail') {echo 'active';} ?>">
                        <a href="setting.php?case=mail" class="nav-link"><span>Mail Settings</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'setting' AND $case == 'social') {echo 'active';} ?>">
                        <a href="setting.php?case=social" class="nav-link"><span>Social Settings</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'setting' AND $case == 'clear_cache') {echo 'active';} ?>">
                        <a href="setting.php?case=clear_cache" class="nav-link"><span>Clear Cache</span></a>
                    </li>
                </ul>
            </div>
            <div class="nav-group <?php if($current_page == 'themes') {echo 'show'; } ?>">
                <div class="nav-group-label">Themes</div>
                <ul class="nav-sidebar style-two">
                    <li class="nav-item <?php if ($current_page == 'themes' AND empty($case)) {echo 'active';} ?>">
                        <a href="themes.php" class="nav-link"><span>Basic Options</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'themes' AND $case == 'logo') {echo 'active';} ?>">
                        <a href="themes.php?case=logo" class="nav-link"><span>Logo Options</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'themes' AND $case == 'fonts') {echo 'active';} ?>">
                        <a href="themes.php?case=fonts" class="nav-link"><span>Fonts Options</span></a>
                    </li>
                </ul>
            </div>
            <div class="nav-group <?php if($current_page == 'account') {echo 'show'; } ?>">
                <div class="nav-group-label">
                    <span>Admin Account</span>
                </div>
                <ul class="nav-sidebar style-two">
                    <li class="nav-item <?php if ($current_page == 'account' AND empty($case)) {echo 'active';} ?>">
                        <a href="account.php" class="nav-link"><span>Edit Info</span></a>
                    </li>
                    <li class="nav-item <?php if ($current_page == 'account' AND $case == 'change_password') {echo 'active';} ?>">
                        <a href="account.php?case=change_password" class="nav-link"><span>Change Password</span></a>
                    </li>
                </ul>
            </div>
            <div class="nav-group">
                <a href="../"  class="nav-link single-link" target="_blank"><span>View Website</span></a>
            </div>
            <div class="nav-group">
                <a href="javascript:ConfirmLogOut();" class="nav-link logout-link"><span>Logout</span></a>
            </div>
        </div>
        <div class="sidebar-footer">
            <div class="avatar-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h6>Microncer Solo</h6>
                </div>
                <span>Version <strong><?php echo $version->number; ?></strong> - <?php echo $version->released; ?></span>
            </div>
        </div>
    </div>

    <div class="content">
    <div class="mobile-nav d-block d-xs-block d-sm-block d-md-block d-lg-none">
        <div class="mobile-header">
            <div class="container">
                <div class="row">
                    <div class="col-3"><a id="mobileMenu" href="#" class="menu-toggle"><i class="ri-menu-2-fill"></i> Menu</a></div>
                    <div class="col-6 text-center"><a href="./" class="mini-logo"><img src="themes/default/images/logo.svg" /></a></div>
                    <div class="col-3 text-right"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container padding-top">
