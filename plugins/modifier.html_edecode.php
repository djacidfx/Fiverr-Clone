<?php
function smarty_modifier_html_edecode($string) {
       return html_entity_decode($string, ENT_QUOTES);
}
?>