<?php
function smarty_modifier_get_service_title($id) {
global $mysqli;
$sql = "SELECT title FROM ss_services WHERE id='$id' ORDER BY id ASC LIMIT 1";
$query = $mysqli->query($sql);
    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        return $row['title'];
    }
}
?>
