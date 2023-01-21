<?php
function smarty_modifier_get_image_size($list_id,$image)
{
return filesize('upload/lists/'.$list_id.'/'.$image);
}

?>