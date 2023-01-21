<?php
function smarty_modifier_substr($year) {
    $short_year = substr($year, -2);
    return $short_year;
}
?>
