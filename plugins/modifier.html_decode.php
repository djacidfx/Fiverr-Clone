<?php
function smarty_modifier_html_decode($string) {
       return html_entity_decode(htmlspecialchars_decode($string, ENT_QUOTES));
}

?>