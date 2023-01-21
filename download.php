<?php
include('include/autoloader.php');
if (!isset($_SESSION['ss_solo_user'])) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}


$id = make_safe(htmlspecialchars($_GET['id'], ENT_QUOTES));
$get = $mysqli->query("SELECT * FROM ss_messages_attachments WHERE id='$id' AND customer_id='$_SESSION[ss_solo_user]'");
if ($get->num_rows == 0) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
$row = $get->fetch_assoc();
$file = 'upload/attachments/'.$row['customer_id'].'/' . $row['attachment_file'];
if (file_exists($file)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $row['attachment_filename'] . '"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
}
?>