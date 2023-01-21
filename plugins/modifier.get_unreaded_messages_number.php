<?php
function smarty_modifier_get_unreaded_messages_number($sale_id,$customer_id) {
global $mysqli;
$sql = "SELECT * FROM ss_messages WHERE sale_id='$sale_id' AND customer_id='$customer_id' AND sender_id='0' AND readed='0'";
$query = $mysqli->query($sql);
return $query->num_rows;
}
?>
