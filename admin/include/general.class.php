<?php 
class General {

	var $mysqli;



    public function __construct($mysqli) {
        $this->db =& $mysqli;
    }

    function check_email($email)
    {
        $sql = "SELECT email FROM ss_admin WHERE email='$email' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function single_order($id) {

        $query = $this->db->query("SELECT * FROM ss_sales WHERE id='$id'");
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function single_support_message($id) {

        $query = $this->db->query("SELECT * FROM ss_support WHERE id='$id'");
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $this->db->query("UPDATE ss_support SET seen='1' WHERE id='$id'");
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function admin($id)
    {
        $sql = "SELECT * FROM ss_admin WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }

    }

    function order_messages($order_id) {
        $sql = "SELECT * FROM ss_messages WHERE sale_id='$order_id' AND deleted='0' ORDER BY id ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    function new_orders() {
        $sql = "SELECT * FROM ss_sales WHERE start_datetime='0' AND deleted='0' ORDER BY id DESC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    function unreaded_orders_messages() {
        $sql = "SELECT ss_sales.id AS sid,ss_sales.order_id,ss_sales.service_id,ss_sales.customer_id AS sale_customer_id,ss_sales.order_datetime,ss_sales.completed,ss_sales.deleted,ss_messages.id,ss_messages.sender_id,ss_messages.customer_id,ss_messages.readed,ss_messages.datetime,ss_messages.deleted FROM ss_messages JOIN ss_sales ON ss_messages.sale_id=ss_sales.id WHERE ss_messages.sender_id!='0' AND ss_messages.readed='0' AND ss_messages.deleted='0' GROUP BY ss_messages.sale_id ORDER BY ss_messages.id DESC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    function email_template($slug)
    {
        $sql = "SELECT * FROM ss_email_templates WHERE template_slug='$slug' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function unreaded_support_messages() {
        $sql = "SELECT * FROM ss_support WHERE seen='0' AND replied='0' AND deleted='0' ORDER BY datetime DESC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    function message_tmp_attachments($session) {
        $sql = "SELECT * FROM ss_messages_attachments_temp WHERE session_id='$session'";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    function single_message($id)
    {
        $sql = "SELECT * FROM ss_messages WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function single_customer($id)
    {
        $sql = "SELECT * FROM ss_customers WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function customer_orders($customer_id) {
        $sql = "SELECT * FROM ss_sales WHERE customer_id='$customer_id' ORDER BY id DESC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    function message_attachments($message_id) {
        $sql = "SELECT * FROM ss_messages_attachments WHERE message_id='$message_id' ORDER BY id ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }
	
	function set_options($data,$set) {
        unset($data['save']);
        unset($data['options_set']);
        foreach ($data AS $key=>$value) {
            if (is_array($value)) {
                $value = implode(',',$value);
            }
            $check = $this->db->query("SELECT option_name FROM ss_options WHERE option_name='$key'");
            $value = $this->db->real_escape_string(htmlspecialchars($value,ENT_QUOTES));
            if ($check->num_rows == 0) {
            $excute = $this->db->query("INSERT INTO ss_options (option_name,option_value,option_default,option_set) VALUES ('$key','$value','$value','$set')");
            } else {
            $excute = $this->db->query("UPDATE ss_options SET option_value='$value' WHERE option_name='$key'");
            }
            if ($excute) {
            $message = 'All Changes Saved.';
            } else {
            $message = notification('danger','Error Happened.');
            }
        }
        return $message;
	}
	
	function get_options($set) {
        $options = array();
        $query = $this->db->query("SELECT * FROM ss_options WHERE option_set='$set' ORDER BY id ASC");
        while ($row = $query->fetch_assoc()) {
            $options[$row["option_name"]] = $row["option_value"];
        }
        return $options;
	}

    function set_theme_options($data,$theme) {
        unset($data['save']);
        foreach ($data AS $key=>$value) {
            if (is_array($value)) {
                $value = implode(',',$value);
            }
            $check = $this->db->query("SELECT option_name FROM ss_theme_options WHERE option_name='$key'");
            $value = $this->db->real_escape_string(htmlspecialchars($value,ENT_QUOTES));
            if ($check->num_rows == 0) {
                $excute = $this->db->query("INSERT INTO ss_theme_options (option_name,option_value,theme) VALUES ('$key','$value','$theme')");
            } else {
                $excute = $this->db->query("UPDATE ss_theme_options SET option_value='$value' WHERE option_name='$key'");
            }
            if ($excute) {
                $message = notification('success','All Changes Saved.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function get_theme_options($theme) {
        $options = array();
        $query = $this->db->query("SELECT * FROM ss_theme_options WHERE theme='$theme' ORDER BY id ASC");
        while ($row = $query->fetch_assoc()) {
            $options[$row["option_name"]] = $row["option_value"];
        }
        return $options;
    }

	function get_all_options() {
        $options = array();
        $query = $this->db->query("SELECT * FROM ss_options ORDER BY id ASC");
        while ($row = $query->fetch_assoc()) {
            $options[strtolower($row["option_set"]).'_'.$row["option_name"]] = $row["option_value"];
        }
        return $options;
	}

	function change_password($request,$id) {
        $current_password = make_safe(xss_clean($request['current_password']));
        $new_password = make_safe(xss_clean($request['new_password']));
        $confirm_password = make_safe(xss_clean($request['confirm_password']));
        $row = $this->admin($id);
        if (empty($current_password)) {
            $message = notification('warning','Please Insert The Current Password.');
        } elseif (empty($new_password)) {
            $message = notification('warning','Please Insert The New Password.');
        } elseif (empty($confirm_password)) {
            $message = notification('warning','Please Confirm The New Password.');
        } elseif ($new_password != $confirm_password) {
            $message = notification('warning','New Password Isn\'t Match the Confirmation.');
        } elseif ($row['password'] != md5($current_password)) {
            $message = notification('warning','The Current Password Is Wrong.');
        } else {
            $encoded_password = md5($new_password);
            $sql = "UPDATE ss_admin SET password='$encoded_password' WHERE id='$id'";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','The Password is Changed Successfully.');
            } else {
                $message = notification('danger','Error Happened');
            }
        }
        return $message;
    }

    function change_account($request,$id) {
        $username = make_safe(xss_clean($request['username']));
        $email = make_safe(xss_clean($request['email']));

        if (empty($username)) {
            $message = notification('warning','Please Insert Username.');
        } elseif (empty($email)) {
            $message = notification('warning','Please Insert E-Mail.');
        } else {
            $sql = "UPDATE ss_admin SET username='$username',email='$email' WHERE id='$id'";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','The Account Details is Changed Successfully.');
            } else {
                $message = notification('danger','Error Happened');
            }
        }
        return $message;
    }

    function orders_count($completed) {
        $sql = "SELECT deleted,completed FROM ss_sales WHERE deleted='0' AND completed='$completed'";
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    function customers_count() {
        $sql = "SELECT deleted FROM ss_customers WHERE deleted='0'";
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    function start_period() {
        $sql = "SELECT year FROM ss_customers ORDER BY id ASC LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function statistics_customers($month,$year) {
        $sql = "SELECT month,year FROM ss_customers WHERE month='$month' AND year='$year'";
        $query = $this->db->query($sql);
        return 0+$query->num_rows;
    }

    function statistics_sales($month,$year) {
        $sql = "SELECT month,year FROM ss_sales WHERE month='$month' AND year='$year'";
        $query = $this->db->query($sql);
        return 0+$query->num_rows;
    }

    function unread_messages_count() {
        $sql = "SELECT * FROM ss_messages WHERE sender_id!='0' AND readed='0'";
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    function make_message_readed($sale_id) {
        $sql = "UPDATE ss_messages SET readed='1' WHERE sender_id!='0' AND sale_id='$sale_id'";
        $this->db->query($sql);
    }
}
?>