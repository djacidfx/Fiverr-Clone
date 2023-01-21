<?php
include('include/autoloader.php');
if (!isset($_SESSION['ss_solo_user'])) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}


$id = make_safe(htmlspecialchars($_GET['id'], ENT_QUOTES));
$order = $general->user_order($id,$_SESSION['ss_solo_user']);
if ($order == 0) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
$get = $mysqli->query("SELECT * FROM ss_services WHERE id='$order[service_id]' LIMIT 2");
if ($get->num_rows == 0) {
    header('Location: '.rtrim($options['general_siteurl'],"/").'/not-found');
    exit;
}
$row = $get->fetch_assoc();
$file = 'upload/digital/' . $row['digital_download_file'];
$ext = getExtension($file);
$filename = $row['slug'].'.'.$ext;
if (file_exists($file)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
}
?>