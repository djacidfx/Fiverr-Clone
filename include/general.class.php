<?php 
class General {

	private $mysqli;

    function set_connection($mysqli) {
        $this->db =& $mysqli;
    }

	function check_username($username)
	{
	    $sql = "SELECT username FROM ss_customers WHERE username='$username' LIMIT 1";
        $query = $this->db->query($sql);
        return $query->num_rows;
	}
	
	function check_email($email)
	{
        $sql = "SELECT email FROM ss_customers WHERE email='$email' LIMIT 1";
        $query = $this->db->query($sql);
        return $query->num_rows;
	}
	
	function customer($id)
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

    function customer_by_email($email)
    {
        $sql = "SELECT * FROM ss_customers WHERE email='$email' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }
	
	function get_options($set) {
        $options = array();
        $query = $this->db->query("SELECT * FROM ss_options WHERE option_set='$set' ORDER BY id ASC");
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

    function get_theme_options($theme) {
        $options = array();
        $query = $this->db->query("SELECT * FROM ss_theme_options WHERE theme='$theme' ORDER BY id ASC");
        while ($row = $query->fetch_assoc()) {
            $options[strtolower('theme_'.$row["option_name"])] = $row["option_value"];
        }
        return $options;
    }
	
	
	function latest_services($number)
	{
        $sql = "SELECT * FROM ss_services WHERE active='1' AND deleted='0' ORDER BY featured DESC, id DESC LIMIT $number";
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

    function services_number()
    {
        $sql = "SELECT active,deleted FROM ss_services WHERE active='1' AND deleted='0'";
        $query = $this->db->query($sql);
        return $query->num_rows;
    }
	
	
	function pages()
	{
        $sql = "SELECT * FROM ss_pages ORDER BY page_order ASC";
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
	
	function page($slug)
	{
        $sql = "SELECT * FROM ss_pages WHERE slug='$slug' LIMIT 1";
        $query = $this->db->query($sql);
		if ($query->num_rows == 0) {
			return 0;
		} else {
			$row = $query->fetch_assoc();
			return $row;
		}
	}

    function service($slug)
    {
        $sql = "SELECT * FROM ss_services WHERE slug='$slug' AND deleted='0' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function service_by_id($id)
    {
        $sql = "SELECT * FROM ss_services WHERE id='$id' AND deleted='0' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }


    function service_images($service_id) {
        $sql = "SELECT * FROM ss_services_images WHERE service_id='$service_id'";
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

    function user_order($order_id,$customer_id)
    {
        $sql = "SELECT * FROM ss_sales WHERE order_id='$order_id' AND customer_id='$customer_id' AND deleted='0' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function user_orders($customer_id)
    {
        $sql = "SELECT * FROM ss_sales WHERE customer_id='$customer_id' AND deleted='0' LIMIT 1";
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

    function single_message($id,$customer_id)
    {
        $sql = "SELECT * FROM ss_messages WHERE id='$id' AND customer_id='$customer_id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
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

    function message_attachments($message_id,$customer_id) {
        $sql = "SELECT * FROM ss_messages_attachments WHERE message_id='$message_id' AND customer_id='$customer_id' ORDER BY id ASC";
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

    function make_message_readed($message_id) {
        $sql = "UPDATE ss_messages SET readed='1' WHERE id='$message_id'";
        $this->db->query($sql);
    }

    function order_messages($order_id,$customer_id) {
        $sql = "SELECT * FROM ss_messages WHERE sale_id='$order_id' AND customer_id='$customer_id' AND deleted='0' ORDER BY id ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
                $this->make_message_readed($row['id']);
            }
            return $rows;
        }
    }

    function unread_messages_count($customer_id) {
        $sql = "SELECT * FROM ss_messages WHERE sender_id!='$customer_id' AND customer_id='$customer_id' AND deleted='0' AND readed='0'";
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    function slider()
    {
        $sql = "SELECT * FROM ss_slider ORDER BY slide_order ASC";
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

    function related($id,$title,$number)
    {
        $sql = "SELECT * FROM ss_services WHERE active='1' AND deleted='0' AND id!='$id' AND MATCH (title) AGAINST ('$title' IN BOOLEAN MODE) ORDER BY id DESC LIMIT $number";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            $sql2 = "SELECT * FROM ss_services WHERE active='1' AND deleted='0' AND id!='$id' ORDER BY rand() LIMIT $number";
            $query2 = $this->db->query($sql2);
            if ($query2->num_rows == 0) {
                return 0;
            } else {
                while ($row = $query2->fetch_assoc()) {
                    $rows[] = $row;
                }
                return $rows;
            }
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    function check_twitter_login($user_id,$username,$email)
    {
        if (!empty($user_id)) {
            $user = $this->db->query("SELECT * FROM ss_customers WHERE email='$email' AND oauth_id='$user_id'");
            if ($user->num_rows == 0) {
                $datetime = time();
                $encoded_password = md5($datetime);
                $activation_code = genRandomString(24);
                $day = date('j');
                $month = date('n');
                $year = date('Y');

                $add = $this->db->query("INSERT INTO ss_customers (username,password,email,activation_code,datetime,last_active,active,day,month,year,provider,oauth_id) VALUES ('$username','$encoded_password','$email','$activation_code','$datetime','$datetime','1','$day','$month','$year','twitter','$user_id')");
                if ($add) {
                    session_start();
                    $_SESSION['ss_solo_user'] = $this->db->insert_id;
                    return $this->db->insert_id;
                }

            } else {
                $userRow = $user->fetch_assoc();
                session_start();
                $_SESSION['ss_solo_user'] = $userRow['id'];
                return $userRow['id'];
            }
        } else {
            return 0;
        }
    }

    function check_envato_login($username,$email)
    {
        if (!empty($username) AND !empty($email)) {
            $user = $this->db->query("SELECT * FROM ss_customers WHERE email='$email' AND provider='envato'");
            if ($user->num_rows == 0) {
                $datetime = time();
                $encoded_password = md5($datetime);
                $activation_code = genRandomString(24);
                $day = date('j');
                $month = date('n');
                $year = date('Y');

                $add = $this->db->query("INSERT INTO ss_customers (username,password,email,activation_code,datetime,last_active,active,day,month,year,provider,oauth_id) VALUES ('$username','$encoded_password','$email','$activation_code','$datetime','$datetime','1','$day','$month','$year','envato','')");
                if ($add) {
                    session_start();
                    $_SESSION['ss_solo_user'] = $this->db->insert_id;
                    return $this->db->insert_id;
                }

            } else {
                $userRow = $user->fetch_assoc();
                session_start();
                $_SESSION['ss_solo_user'] = $userRow['id'];
                return $userRow['id'];
            }
        } else {
            return 0;
        }
    }

    function update_customer_activity($user_id) {
        $datetime = time();
        $sql = "UPDATE ss_customers SET last_active='$datetime' WHERE id='$user_id'";
        $query = $this->db->query($sql);
    }

    function main_categories($order)
    {
        $sql = "SELECT * FROM ss_categories ORDER BY $order";
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

    function category($id)
    {
        $sql = "SELECT * FROM ss_categories WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }
	
}
?>