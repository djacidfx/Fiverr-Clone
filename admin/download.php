<?php
session_start();
if(!isset($_SESSION['servshop_admin'])) {
    header("location:login.php");
    exit;
}
include('include/autoloader.php');

$id = make_safe(htmlspecialchars($_GET['id'], ENT_QUOTES));
$get = $mysqli->query("SELECT * FROM ss_messages_attachments WHERE id='$id'");
$row = $get->fetch_assoc();
$file = '../upload/attachments/'.$row['customer_id'].'/' . $row['attachment_file'];
if (file_exists($file)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $row['attachment_filename'] . '"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
}
?>