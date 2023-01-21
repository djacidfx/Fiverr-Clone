<?php
function smarty_modifier_get_message_attachments($id) {
global $mysqli;
$sql = "SELECT * FROM ss_messages_attachments WHERE message_id='$id'";
$query = $mysqli->query($sql);
if ($query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}
}
?>
