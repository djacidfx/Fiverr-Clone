<?php
include('include/autoloader.php');
error_reporting(E_ERROR);
if (isset($_GET['case'])) {
    $case = make_safe(xss_clean($_GET['case']));
} else {
    $case = '';
}
if (isset($_POST['action'])) {
    $action = make_safe(xss_clean($_POST['action']));
} else {
    $action = '';
}
if ($action == 'facebook_login') {
    $email   = make_safe($_POST['email']);
    $facebook_auth_id   = make_safe($_POST['facebook_auth_id']);
    $username   = make_safe(strtolower(str_replace(' ','',$_POST['username'])));

    if (empty($email) or empty($facebook_auth_id)) {
        $fbl['status'] = 0;
        $fbl['msg']    = ' Missing Info';
        $result         = json_encode($fbl);
        die($result);
    }

    $user = $mysqli->query("SELECT * FROM ss_customers WHERE email='$email' AND oauth_id='$facebook_auth_id'");
    if ($user->num_rows == 0) {
        $datetime = time();
        $encoded_password = md5($datetime);
        $activation_code = genRandomString(24);
        $day = date('j');
        $month = date('n');
        $year = date('Y');

        $add = $mysqli->query("INSERT INTO ss_customers (username,password,email,activation_code,datetime,last_active,active,day,month,year,provider,oauth_id) VALUES ('$username','$encoded_password','$email','$activation_code','$datetime','$datetime','1','$day','$month','$year','facebook','$facebook_auth_id')");
        if ($add) {
            $_SESSION['ss_solo_user'] = $mysqli->insert_id;
            $fbl['status'] = 1;
        } else {
            $fbl['status'] = 0;
            $fbl['msg']    = $language['error_happened'];
        }

    } else {
        $userRow = $user->fetch_assoc();
        $_SESSION['ss_solo_user'] = $userRow['id'];
        $general->update_customer_activity($userRow['id']);
        $fbl['status'] = 1;
    }
    $result = json_encode($fbl);
    die($result);
}
switch ($case) {
    case 'register';
        if (isset($_POST)) {
            $username = make_safe($_POST['username']);
            $password = make_safe($_POST['password']);
            $confirm_password = make_safe($_POST['confirm_password']);
            $email = make_safe($_POST['email']);
            $check_username = $general->check_username($username);
            $check_email = $general->check_email($email);
            $validemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
            $recaptcha_response = trim($_POST['g-recaptcha-response']);
            $check_capatcha = verify_recaptcha($recaptcha_response,$options['api_recaptcha_private']);
            if (empty($username)) {
                echo notification('warning',$language['insert_username']);
            } elseif (mb_strlen($username,'UTF-8') < 5 OR mb_strlen($username,'UTF-8') > 24) {
                echo notification('warning',$language['username_min_max']);
            } elseif ($check_username != 0) {
                echo notification('warning',$language['username_existed_before']);
            } elseif (empty($email)) {
                echo notification('warning',$language['insert_email']);
            } elseif (preg_match($validemail, $email) === false) {
                echo notification('warning',$language['insert_valid_email']);
            } elseif ($check_email != 0) {
                echo notification('warning',$language['email_exists_before']);
            } elseif (empty($password)) {
                echo notification('warning',$language['insert_password']);
            } elseif (mb_strlen($password,'UTF-8') < 6) {
                echo notification('warning',$language['password_min_max']);
            } elseif (empty($confirm_password)) {
                echo notification('warning',$language['insert_password_confirm']);
            } elseif ($password != $confirm_password) {
                echo notification('warning',$language['password_no_match']);
            } elseif ($options['api_allow_signup_recaptcha'] == 1 AND $check_capatcha == 0) {
                echo notification('warning',$language['invalid_captcha']);
            } else {
                $datetime = time();
                $encoded_password = md5($password);
                $activation_code = genRandomString(24);
                $day = date('j');
                $month = date('n');
                $year = date('Y');
                if ($options['users_activate_new_users'] == 'direct') {
                    $active = 1;
                } else {
                    $active = 0;
                    $template = $general->email_template('account-activation');
                    $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
                    $confirmation_link = rtrim($options['general_siteurl'],"/").'/user-activation/'.md5($email).'/'.normalize_input($activation_code);
                    $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
                    $arr_1 = array('{LINK}','{SITENAME}','{SITELINK}');
                    $arr_2 = array(normalize_input($confirmation_link),normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
                    $body = str_replace($arr_1,$arr_2,$cont);


                    $content = array(
                        'email' => $email,
                        'username' => $username,
                        'title' => $title,
                        'body' => $body
                    );
                    $send_email = send_email($options,$content);

                }
                $add = $mysqli->query("INSERT INTO ss_customers (username,password,email,activation_code,datetime,last_active,active,day,month,year) VALUES ('$username','$encoded_password','$email','$activation_code','$datetime','$datetime','$active','$day','$month','$year')");
                if ($add) {
                    if ($options['users_activate_new_users'] == 'direct') {
                        echo notification('success',$language['success_registered']);
                    } else {
                        echo notification('success',$language['activation_link_sent'].' : <b>'.make_safe($email).'</b>');
                    }
                } else {
                    echo notification('danger',$language['error_happened']);
                }
            }
        }
        break;
    case 'login';
        if (isset($_POST) AND count($_POST) > 0) {
            $username = make_safe(xss_clean($_POST["username"]));
            $password = make_safe(xss_clean($_POST["password"]));
            $remember = make_safe(xss_clean($_POST["remember"]));
            $encoded_password = md5($password);
            if (empty($username)) {
                echo notification('warning',$language['insert_username']);
            } elseif (empty($password)) {
                echo notification('warning',$language['insert_passsword']);
            } else {
                $sql = "SELECT * FROM ss_customers WHERE (username='$username' OR email='$username') AND password='$encoded_password' AND deleted='0' LIMIT 1";
                $query = $mysqli->query($sql);
                if ($query->num_rows == 0) {
                    echo notification('warning',$language['incorrect_login']);
                } else {
                    $row = $query->fetch_assoc();
                    if ($row['active'] == 0) {
                        echo notification('warning',$language['account_not_active']);
                    } else {
                        $_SESSION['ss_solo_user'] = $row['id'];
                        $datetime = time();
                        $general->update_customer_activity($row['id']);
                        if ($remember == 1 AND isset($options['users_cookie_name'])) {
                            setcookie(normalize_input($options['users_cookie_name']), normalize_input($row['username'].'_'.$row['password']), normalize_input($datetime+(60*60*24*$options['users_cookie_expiration_time'])), '/');
                        }
                        echo 1;
                    }
                }
            }
        }
    break;
    case 'logout';
        if (!isset($_SESSION['ss_solo_user'])) {
            die('you can not access this page');
        } else {
            if (isset($_POST)) {
                if ($_POST['logout'] == 1) {
                    unset($_SESSION['ss_solo_user']);
                    if (isset($_COOKIE["$options[users_cookie_name]"])) {
                        setcookie(normalize_input($options['users_cookie_name']), '', time()-(60*60*24*7), '/');
                    }
                    echo 1;
                }
            }
        }
        break;
        case 'offline_payment';
            if (isset($_SESSION['ss_solo_user'])) {
                $service_id = make_safe($_POST['service_id']);
                $customer_id = make_safe($_POST['customer_id']);
                if ($_SESSION['ss_solo_user'] == $customer_id) {
                    $service = $general->service_by_id($service_id);
                    $delivery = $service['delivery'] * (60 * 60 * 24);
                    $payer_id = $customer_id;
                    $customer = $general->customer($customer_id);
                    $transaction_id = 'offline';
                    $amount = $service['price'];
                    $datetime = time();
                    $day = date('j');
                    $month = date('n');
                    $year = date('Y');
                    $order_id = uniqid();
                    $ip = getRealIpAddr();
                    if ($service['digital_download'] == 1) {
                        $completed = 1;
                        $start_datetime = time();
                        $expected_datetime = time();
                        $complete_datetime = time();
                    } else {
                        $completed = 0;
                        $start_datetime = 0;
                        $expected_datetime = 0;
                        $complete_datetime = 0;
                    }
                    $add = $mysqli->query("INSERT INTO ss_sales (service_id,order_id,payment_method,customer_id,transaction_id,amount,currency,order_datetime,day,month,year,completed,start_datetime,expected_datetime,complete_datetime) VALUES ('$service[id]','$order_id','offline','$customer_id','$transaction_id','$amount','$options[payment_site_currency]','$datetime','$day','$month','$year','$completed','$start_datetime','$expected_datetime','$complete_datetime')");
                    if ($add) {
                        $order_id = $mysqli->insert_id;
                        $uniq_order_id = get_unique_order_id($order_id);
                        if (isset($options['mail_order_placed_email']) and $options['mail_order_placed_email'] == 1) {
                            $dashboard_link = rtrim($options['general_siteurl'], '/') . '/admin/';

                            $template = $general->email_template('new-order');
                            $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
                            $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
                            $arr_1 = array('{LINK}', '{SITENAME}', '{SITELINK}');
                            $arr_2 = array(normalize_input($dashboard_link), normalize_input($options['general_seo_title']), normalize_input($options['general_siteurl']));
                            $body = str_replace($arr_1, $arr_2, $cont);


                            $content = array(
                                'email' => normalize_input($options['mail_reciption_email']),
                                'username' => normalize_input($options['general_seo_title']),
                                'title' => normalize_input($title),
                                'body' => normalize_input($body)
                            );
                            $send_email = send_email($options, $content);
                        }
                        $result = array(
                            'code' => 1,
                            'order_id' => $uniq_order_id
                        );

                    } else {
                        $result = array(
                            'code' => 0,
                            'order_id' => 0
                        );
                    }
                    echo json_encode($result);
                }
            }
        break;
    case 'upload_images';
        if (!empty($_FILES['files']['name'])) {
            include('include/class.uploader.php');
            $extensions = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'zip', 'doc', 'docs');
            $uploader = new Uploader();
            $data = $uploader->upload($_FILES['files'], array(
                'limit' => 10, //Maximum Limit of files. {null, Number}
                'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
                'extensions' => $extensions, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                'required' => false, //Minimum one file is required for upload {Boolean}
                'uploadDir' => 'upload/tmp_images/', //Upload directory {String}
                'title' => array('name'), //New file name {null, String, Array} *please read documentation in README.md
                'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
                'perms' => null, //Uploaded file permisions {null, Number}
                'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
                'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
                'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
                'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
                'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
                'onRemove' => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
            ));

            if($data['isComplete']){
                $files = $data['data'];
                print_r($files);
                foreach ($data['data']['files'] AS $file) {
                    if (file_exists($file)) {
                        $filename = str_replace('upload/tmp_images/','',$file);
                        $session_id = $_SESSION['user_session_id'];
                        $user_id = $_SESSION['ss_solo_user'];
                        $mysqli->query("INSERT INTO ss_messages_attachments_temp (user_id,filename,session_id) VALUES ('$user_id','$filename','$session_id')");
                    }
                }
            }

            if($data['hasErrors']){
                $errors = $data['errors'];
                print_r($errors);
            }

            function onFilesRemoveCallback($removed_files){
                global $mysqli;
                foreach($removed_files as $key=>$value){
                    $file = 'upload/tmp_images/' . $value;

                    $session_id = $_SESSION['user_session_id'];

                    $query = $mysqli->query("DELETE FROM ss_messages_attachments_temp WHERE filename='$value' AND session_id='$session_id'");
                    if ($query) {
                        if (!empty($value)) {
                            unlink($file);
                        }
                    }
                }

                return $removed_files;
            }
        }
        break;
    case 'remove_image';
        if(isset($_POST['file'])){
            $file = 'upload/tmp_images/' . $_POST['file'];
            $session_id = $_SESSION['user_session_id'];
            if(file_exists($file)){
                $to = $_POST['file'];
                $query = $mysqli->query("DELETE FROM ss_messages_attachments_temp WHERE filename='$to' AND session_id='$session_id'");
                if ($query) {
                    if (!empty($_POST['file'])) {
                        unlink($file);
                    }
                }
            }
        }
        break;

    case 'submit_message';
        if (isset($_POST)) {
            $message = make_safe($_POST['message']);
            $service_id = make_safe($_POST['service_id']);
            $order_id = make_safe($_POST['order_id']);

            if (empty($message)) {
                echo notification('warning',$language['insert_message']);
            } else {
                $datetime = time();
                $user_id = $_SESSION['ss_solo_user'];
                if (isset($_SESSION['user_session_id'])) {
                    $session_id = $_SESSION['user_session_id'];
                } else {
                    $_SESSION['user_session_id'] = session_id();
                    $session_id = $_SESSION['user_session_id'];
                }

                $images = $general->message_tmp_attachments($session_id);
                $add = $mysqli->query("INSERT INTO ss_messages (service_id,sale_id,customer_id,sender_id,message,datetime,readed) VALUES ('$service_id','$order_id','$user_id','$user_id','$message','$datetime','0')");
                if ($add) {
                    $message_id = $mysqli->insert_id;
                    if ($images != 0) {
                        foreach ($images AS $image) {
                            if (!is_dir('upload/attachments/'.$user_id)) {
                                mkdir('upload/attachments/'.$user_id,0755);
                                copy("upload/attachments/.htaccess", "upload/attachments/".normalize_input($user_id)."/.htaccess");
                            }
                            $attachment_type = getExtension($image['filename']);
                            $filename = time().uniqid().'.'.$attachment_type;
                            rename("upload/tmp_images/".$image['filename'], "upload/attachments/".normalize_input($user_id)."/".normalize_input($filename));
                            if (file_exists("upload/tmp_images/".$image['filename'])) {
                                unlink("upload/tmp_images/".$image['filename']);
                            }
                            $attachment_size = filesize("upload/attachments/".$user_id."/".$filename);
                            $mysqli->query("INSERT INTO ss_messages_attachments (message_id,attachment_filename,attachment_file,customer_id,attachment_type,attachment_size) VALUES ('$message_id','$image[filename]','$filename','$user_id','$attachment_type','$attachment_size')");
                            $mysqli->query("DELETE FROM ss_messages_attachments_temp WHERE session_id='$_SESSION[user_session_id]' AND filename='$image[filename]'");
                        }
                    }
                    unset($_SESSION['user_session_id']);
                    session_regenerate_id();
                    $inserted_message = $general->single_message($message_id,$user_id);
                    foreach ($inserted_message AS $key=>$value) {
                        $smarty->assign('message_'.$key,$value);
                    }
                    $message_attachments = $general->message_attachments($message_id,$user_id);
                    $smarty->assign('message_attachments',$message_attachments);
                    $smarty->display('ajax-single-message.html');
                }

            }
        }
        break;

    case 'service_inquiries';
        if (isset($_POST) AND count($_POST) > 0) {
            $title = make_safe($_POST['title']);
            $message = make_safe($_POST['message']);
            $service_id = make_safe($_POST['service_id']);
            $username = make_safe($_POST['username']);
            $email = make_safe($_POST['email']);
            $user_id = make_safe($_POST['user_id']);
            $validemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
            if ($user_id == 0 AND $options['api_allow_service_recaptcha'] == 1) {
                $recaptcha_response = trim($_POST['g-recaptcha-response']);
                $check_capatcha = verify_recaptcha($recaptcha_response,$options['api_recaptcha_private']);
            }

            if (empty($message)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_message'])
                );

            } elseif (empty($username)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_name'])
                );

            } elseif (empty($email)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_email'])
                );

            } elseif (preg_match($validemail, $email) === false) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_valid_email'])
                );
            } elseif ($user_id == 0 AND $options['api_allow_service_recaptcha'] == 1 AND $check_capatcha == 0) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['invalid_captcha'])
                );
            } else {
                $datetime = time();
                $add = $mysqli->query("INSERT INTO ss_support (source,service_id,user_id,username,email,title,message,datetime,seen) VALUES ('inquiry form','$service_id','$user_id','$username','$email','$title','$message','$datetime','0')");
                if ($add) {
                    $result = array(
                        'code' => 1,
                        'message' => notification('success',$language['message_sent_success'])
                    );
                } else {
                    $result = array(
                        'code' => 0,
                        'message' => notification('danger',$language['error_happened'])
                    );
                }

            }
            echo json_encode($result);
        }
        break;

    case 'change_password';
        if (isset($_POST) AND count($_POST) > 0) {
            $current_password = xss_clean($_POST["current_password"]);
            $new_password = xss_clean($_POST["new_password"]);
            $confirm_new_password = xss_clean($_POST["confirm_new_password"]);
            if (empty($current_password)) {
                echo notification('warning',$language['insert_current_message']);
            } elseif (empty($new_password)) {
                echo notification('warning',$language['insert_password']);
            } elseif (empty($confirm_new_password)) {
                echo notification('warning',$language['insert_password_confirm']);
            } elseif (mb_strlen($new_password,'UTF-8') < 6) {
                echo notification('warning',$language['password_min_max']);
            } elseif ($new_password != $confirm_new_password) {
                echo notification('warning',$language['password_no_match']);
            } else {
                $customer = $general->customer($_SESSION['ss_solo_user']);
                if ($customer['password'] != md5($current_password)) {
                    echo notification('warning',$language['current_password_incorrect']);
                } else {
                    $encoded_password = md5($new_password);
                    $update = $mysqli->query("UPDATE ss_customers SET password='$encoded_password' WHERE id='$_SESSION[ss_solo_user]'");
                    if ($update) {
                        echo notification('success',$language['change_password_succeess']);
                    } else {
                        echo notification('danger',$language['error_happened']);
                    }
                }
            }

        }
        break;
    case 'contact';
        if (isset($_POST) AND count($_POST) > 0) {
            $name = make_safe(xss_clean($_POST['name']));
            $email = make_safe(xss_clean($_POST['email']));
            $title = make_safe(xss_clean($_POST['title']));
            $content = make_safe(xss_clean($_POST['content']));
            if (isset($options['api_allow_contact_recaptcha']) AND $options['api_allow_contact_recaptcha'] == 1) {
                $recaptcha_response = trim($_POST['g-recaptcha-response']);
                $check_capatcha = verify_recaptcha($recaptcha_response,$options['api_recaptcha_private']);
            }
            $validemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";

            if (empty($name)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_name'])
                );
            } elseif (empty($email)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_email'])
                );
            } elseif (preg_match($validemail, $email) === false) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_valid_email'])
                );
            } elseif (empty($title)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_message_title'])
                );
            } elseif (empty($content)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_message_content'])
                );
            } elseif (isset($options['apis_allow_contact_recaptcha']) AND $options['apis_allow_contact_recaptcha'] == 1 AND $check_capatcha == 0) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['invalid_captcha'])
                );
            } else {
                $datetime = time();
                $add = $mysqli->query("INSERT INTO ss_support (source,service_id,user_id,username,email,title,message,datetime,seen) VALUES ('contact form','0','0','$name','$email','$title','$content','$datetime','0')");
                if ($add) {
                    $result = array(
                        'code' => 1,
                        'message' => notification('success',$language['message_sent_success'])
                    );
                } else {
                    $result = array(
                        'code' => 0,
                        'message' => notification('danger',$language['error_happened'])
                    );
                }
            }

            echo json_encode($result);
        }
        break;
    case 'forget_password';
        if (isset($_POST)) {
            if (isset($options['api_allow_signup_recaptcha']) AND $options['api_allow_signup_recaptcha'] == 1) {
                $recaptcha_response = trim($_POST['g-recaptcha-response']);
                $check_capatcha = verify_recaptcha($recaptcha_response,$options['api_recaptcha_private']);
            }
            $email = make_safe($_POST['email']);
            $check_email = $general->check_email($email);
            $validemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
            if (empty($email)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_email'])
                );
            } elseif (preg_match($validemail, $email) === false) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_valid_email'])
                );
            } elseif ($check_email == 0) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['email_not_existed'])
                );
            } elseif (isset($options['api_allow_signup_recaptcha']) AND $options['api_allow_signup_recaptcha'] == 1 AND $check_capatcha == 0) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['invalid_captcha'])
                );

            } else {
                $get = $mysqli->query("SELECT activation_code,email,username FROM ss_customers WHERE email='$email'");
                $row = $get->fetch_assoc();
                $reset_password_link = rtrim($options['general_siteurl'],"/").'/reset-password/'.md5($email).'/'.$row['activation_code'];
                $template = $general->email_template('forget-password');
                $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
                $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
                $arr_1 = array('{LINK}','{SITENAME}','{SITELINK}');
                $arr_2 = array(normalize_input($reset_password_link),normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
                $body = str_replace($arr_1,$arr_2,$cont);
                $customer = $general->customer_by_email($email);
                $content = array(
                    'email' => normalize_input($email),
                    'username' => normalize_input($customer['username']),
                    'title' => normalize_input($title),
                    'body' => normalize_input($body)
                );
                $send_email = send_email($options,$content);
                if ($send_email) {
                    $result = array(
                        'code' => 1,
                        'message' => notification('success',$language['reset_password_success'])
                    );
                } else {
                    $result = array(
                        'code' => 0,
                        'message' => notification('danger',$language['error_happened'])
                    );
                }
            }
            echo json_encode($result);
        }
        break;
    case 'reset_password';
        if (isset($_POST)) {
            $new_password = make_safe($_POST['new_password']);
            $confirm_new_password = make_safe($_POST['confirm_new_password']);
            $user_id = intval(make_safe(xss_clean($_POST['user_id'])));
            if (empty($new_password)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['insert_passsword'])
                );
            } elseif (mb_strlen($new_password,'UTF-8') < 6) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['password_min_max'])
                );
            } elseif (empty($confirm_new_password)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['confirm_password'])
                );
            } elseif ($new_password != $confirm_new_password) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',$language['password_no_match'])
                );
            } else {
                $encoded_password = md5($new_password);
                $activation_code = genRandomString(24);
                $update = $mysqli->query("UPDATE ss_customers SET password='$encoded_password',activation_code='$activation_code' WHERE id='$user_id'");
                if ($update) {
                    $result = array(
                        'code' => 1,
                        'message' => notification('success',$language['password_saved'])
                    );
                } else {
                    $result = array(
                        'code' => 0,
                        'message' => notification('danger',$language['error_happened'])
                    );
                }
            }
            echo json_encode($result);
        }
        break;
    default;
}
?>