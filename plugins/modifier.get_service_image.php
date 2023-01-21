<?php
function smarty_modifier_get_service_image($id) {
global $mysqli;
$sql = "SELECT * FROM ss_services_images WHERE service_id='$id'";
$query = $mysqli->query($sql);
while ($row = $query->fetch_assoc()) {
    $rows[] = $row;
}
return $rows;
}
?>
