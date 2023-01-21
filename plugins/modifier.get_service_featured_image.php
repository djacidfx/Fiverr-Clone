<?php
function smarty_modifier_get_service_featured_image($id) {
global $mysqli;
$sql = "SELECT * FROM ss_services_images WHERE service_id='$id' ORDER BY id ASC LIMIT 1";
$query = $mysqli->query($sql);
    if ($query->num_rows == 0) {
        $cover = 'upload/services/no-image.jpg';
    } else {
        $row = $query->fetch_assoc();
        $cover = 'upload/services/'.$row['service_id'].'/'.$row['filename'];
    }
    return $cover;
}
?>
