<?php
class Templates extends General {


    function edit_template($request,$id) {

        $template_code = htmlspecialchars($request['template_code'],ENT_QUOTES);

        if (empty($template_code)) {
            $message = notification('warning','Insert The Title Please.');
        } else {
            $sql = "UPDATE ss_email_templates SET template_code='$template_code' WHERE id='$id'";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','Template is Edited Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function template($id)
    {
        $sql = "SELECT * FROM ss_email_templates WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

}