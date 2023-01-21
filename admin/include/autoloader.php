<?php
error_reporting(E_ERROR);
include("../include/config.php");
include("../include/connect.php");
require '../include/mailer/PHPMailerAutoload.php';
require('../include/sendgrid-php/sendgrid-php.php');
require '../include/paypal-sdk/autoload.php';
require '../include/stripe-sdk/Stripe.php';
include("include/functions.php");
include("include/upload.class.php");
include("include/pagination.php");
include("include/nocsrf.php");
include("include/general.class.php");
include("include/categories.class.php");
include("include/pages.class.php");
include("include/services.class.php");
include("include/templates.class.php");
include("include/slider.class.php");
$general = new General($mysqli);
$pages = new Pages($mysqli);
$categories = new Categories($mysqli);
$services = new Services($mysqli);
$slider = new Slider($mysqli);
$templates = new Templates($mysqli);
$options = $general->get_all_options();
$parts = explode('/', make_safe($_SERVER["PHP_SELF"]));
$currenttab = $parts[count($parts) - 1];
$version = get_current_version_number();
?>