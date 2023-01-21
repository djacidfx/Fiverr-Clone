<?php
function smarty_modifier_get_datetime($timestamp,$is_time = 0) {
$date = date('F j, Y',$timestamp);
$time = date('h:i a',$timestamp);
if (isset($is_time) AND $is_time == 1) {
return $date.' '.$time;
} else {
return $date;
}
}
?>
