<?php
session_start();
header("Content-type: text/html; charset=utf8");
set_time_limit(0);
if(!isset($_SESSION['microncer_solo'])) {
header("location:login.php");
exit();
}
include('include/autoloader.php');
$general = new General($mysqli);
$options = $general->get_all_options();
$case = make_safe(xss_clean($_GET['case']));
$action = make_safe(xss_clean($_POST['action']));
if ($action == "sort_categories"){
    $records = $_POST['records'];
    $counter = 1;
    foreach ($records as $record) {
        $sql = "UPDATE ss_categories SET category_order='$counter' WHERE id='$record'";
        $query = $mysqli->query($sql);
        $counter = $counter + 1;
    }
}
if ($action == 'update') {
    if (isset($_POST)) {
        if (!empty($_FILES['uploadFile']['name'])) {
            sleep(1);
            $source_path = $_FILES['uploadFile']['tmp_name'];
            $target_path = '../upload/updates/' . $_FILES['uploadFile']['name'];
            $file = '../upload/updates/'.$_FILES['uploadFile']['name'];
            $path = '../upload/updates/';
            if(move_uploaded_file($source_path, $target_path))
            {
                $zip = new ZipArchive;
                $res = $zip->open($file);
                if ($res === TRUE) {
                    // extract it to the path we determined above
                    $zip->extractTo($path);
                    $zip->close();
                    sleep(1);
                    search_for_sql($path);
                    sleep(1);
                    recurse_copy($path,'../');
                    sleep(1);
                    rrmdir($path);
                } else {
                    echo "Doh! I couldn't open $file";
                }
            }

        }
    }
}

if ($action == "sort_pages"){
	$records = $_POST['records'];
	$counter = 1;
	foreach ($records as $record) {
		$sql = "UPDATE ss_pages SET page_order='$counter' WHERE id='$record'";
		$query = $mysqli->query($sql);
		$counter = $counter + 1;	
	}
}

if ($action == "sort_slides"){
    $records = $_POST['records'];
    $counter = 1;
    foreach ($records as $record) {
        $sql = "UPDATE ss_slider SET slide_order='$counter' WHERE id='$record'";
        $query = $mysqli->query($sql);
        $counter = $counter + 1;
    }
}

if ($action == "sort_links"){
	$records = $_POST['records'];
	$counter = 1;
	foreach ($records as $record) {
		$sql = "UPDATE links SET link_order='$counter' WHERE id='$record'";
		$query = $mysqli->query($sql);
		$counter = $counter + 1;	
	}
}


if ($action == "delete_service_image") {
    $id = (int) make_safe($_POST['id']);
    if (empty($id)) {
        header("location:login.php");
        exit();
    }
    $image = $services->service_image($id);
    if (file_exists('../upload/services/'.normalize_input($image['service_id']).'/'.normalize_input($image['filename']))) {
        @unlink('../upload/services/'.normalize_input($image['service_id']).'/'.normalize_input($image['filename']));
    }
    $mysqli->query("DELETE FROM ss_services_images WHERE id='$id'");
}
if ($action == "delete_service"){
    $id = (int) make_safe($_POST['id']);
    if (!empty($id)) {
        $mysqli->query("UPDATE ss_services SET deleted='1' WHERE id='$id'");
        $mysqli->query("UPDATE ss_sales SET deleted='1' WHERE service_id='$id'");
        $mysqli->query("UPDATE ss_support SET deleted='1' WHERE service_id='$id'");
    }
}
if ($action == "delete_customer"){
    $id = (int) make_safe($_POST['id']);
    if (!empty($id)) {
        $mysqli->query("UPDATE ss_customers SET deleted='1' WHERE id='$id'");
        $mysqli->query("UPDATE ss_sales SET deleted='1' WHERE customer_id='$id'");
        $mysqli->query("UPDATE ss_messages SET deleted='1' WHERE customer_id='$id'");
        $mysqli->query("UPDATE ss_support SET deleted='1' WHERE user_id='$id'");
    }
}

if ($action == "delete_page"){
    $id = (int) make_safe($_POST['id']);
    if (!empty($id)) {
        $mysqli->query("DELETE FROM ss_pages WHERE id='$id'");
    }
}

if ($action == "delete_slide"){
    $id = (int) make_safe($_POST['id']);
    if (!empty($id)) {
        $mysqli->query("DELETE FROM ss_slider WHERE id='$id'");
    }
}

if ($action == "start_order") {
    $id = (int) abs($_POST['id']);
    if (empty($id)) {
        header("location:login.php");
        exit();
    }
    $order = $general->single_order($id);
    $service = $services->service($order['service_id']);
    $start_datetime = time();
    $expected_datetime = normalize_input($start_datetime+($service['delivery']*24*60*60));
    $update = $mysqli->query("UPDATE ss_sales SET start_datetime='$start_datetime', expected_datetime='$expected_datetime' WHERE id='$id'");
    if ($update) {
        if (isset($options['mail_order_start_email']) AND $options['mail_order_start_email'] == 1) {
            $customer = $general->single_customer($order['customer_id']);
            $message_link = rtrim($options['general_siteurl'],"/").'/dashboard/orders/order/'.normalize_input($order['order_id']);

            $template = $general->email_template('order-started');
            $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
            $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
            $arr_1 = array('{ORDERID}','{LINK}','{SITENAME}','{SITELINK}');
            $arr_2 = array(normalize_input($order['order_id']),normalize_input($message_link),normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
            $body = str_replace($arr_1,$arr_2,$cont);


            $content = array(
                'email' => normalize_input($customer['email']),
                'username' => normalize_input($customer['username']),
                'title' => normalize_input($title),
                'body' => normalize_input($body)
            );
            $send_email = send_email($options,$content);
        }
    }
}

if ($action == "complete_order") {
    $id = (int) abs($_POST['id']);
    if (empty($id)) {
        header("location:login.php");
        exit();
    }
    $order = $general->single_order($id);
    $service = $services->service($order['service_id']);
    $complete_datetime = time();
    $update = $mysqli->query("UPDATE ss_sales SET complete_datetime='$complete_datetime',completed='1' WHERE id='$id'");
    if ($update) {
        if (isset($options['mail_order_complete_email']) AND $options['mail_order_complete_email'] == 1) {
            $customer = $general->single_customer($order['customer_id']);
            $message_link = rtrim($options['general_siteurl'], "/") . '/dashboard/orders/order/' . normalize_input($order['order_id']);

            $template = $general->email_template('order-completed');
            $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
            $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
            $arr_1 = array('{ORDERID}','{LINK}','{SITENAME}','{SITELINK}');
            $arr_2 = array(normalize_input($order['order_id']),normalize_input($message_link),normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
            $body = str_replace($arr_1,$arr_2,$cont);


            $content = array(
                'email' => normalize_input($customer['email']),
                'username' => normalize_input($customer['username']),
                'title' => normalize_input($title),
                'body' => normalize_input($body)
            );
            $send_email = send_email($options, $content);
        }
    }
}

if ($action == "cancel_order") {
    $id = (int) abs($_POST['id']);
    if (empty($id)) {
        header("location:login.php");
        exit();
    }
    $order = $general->single_order($id);
    $service = $services->service($order['service_id']);
    $start_datetime = 0;
    $expected_datetime = 0;
    $update = $mysqli->query("UPDATE ss_sales SET start_datetime='$start_datetime', expected_datetime='$expected_datetime',deleted='1' WHERE id='$id'");
    if ($update) {
        if (isset($options['mail_order_cancel_email']) AND $options['mail_order_cancel_email'] == 1) {
            $customer = $general->single_customer($order['customer_id']);
            $message_link = rtrim($options['general_siteurl'], "/") . '/dashboard/orders/order/' . normalize_input($order['order_id']);

            $template = $general->email_template('order-cancelled');
            $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
            $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
            $arr_1 = array('{ORDERID}','{LINK}','{SITENAME}','{SITELINK}');
            $arr_2 = array(normalize_input($order['order_id']),normalize_input($message_link),normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
            $body = str_replace($arr_1,$arr_2,$cont);



            $content = array(
                'email' => normalize_input($customer['email']),
                'username' => normalize_input($customer['username']),
                'title' => normalize_input($title),
                'body' => normalize_input($body)
            );
            $send_email = send_email($options, $content);
        }
    }
}

switch ($case) {
    case 'change_heading_font':
$heading_font = $_GET['heading_font'];
echo '<link href="http://fonts.googleapis.com/css?family='.$heading_font.'" rel="stylesheet" type="text/css">';
echo '<style>
.example-header {
	font-family: '.str_replace('+',' ',$heading_font).';
	background:#f5f5f5;
	padding:5px;
	font-size:24px;
	font-weight:bold;
}
</style>';
echo 'The quick brown fox jumps over the lazy dog';
break;
    case 'change_paragraph_font':
    $body_font = $_GET['body_font'];
    echo '<link href="http://fonts.googleapis.com/css?family='.$body_font.'" rel="stylesheet" type="text/css">';
    echo '<style>
.example-paragraph {
	font-family: '.str_replace('+',' ',$body_font).';
	background:#f5f5f5;
	padding:5px;
	font-size:16px;
	font-weight:400;
}
</style>';
    echo 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.';
break;
    case 'upload_images';
        if (!empty($_FILES['files']['name'])) {
            include('include/class.uploader.php');
            $extensions = array('jpg', 'png', 'jpeg', 'gif', 'webp');
            $uploader = new Uploader();
            $data = $uploader->upload($_FILES['files'], array(
                'limit' => 10, //Maximum Limit of files. {null, Number}
                'maxSize' => 2, //Maximum Size of files {null, Number(in MB's)}
                'extensions' => $extensions, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                'required' => false, //Minimum one file is required for upload {Boolean}
                'uploadDir' => '../upload/tmp_images/', //Upload directory {String}
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
                foreach ($data['data']['files'] AS $file) {
                    if (file_exists($file)) {
                        $filename = str_replace('../upload/tmp_images/','',normalize_input($file));
                        $session_id = normalize_input($_SESSION['admin_session_id']);
                        $mysqli->query("INSERT INTO ss_services_images_temp (filename,session_id) VALUES ('$filename','$session_id')");
                    }
                }
            }

            if($data['hasErrors']){
                $errors = $data['errors'];
            }

            function onFilesRemoveCallback($removed_files){
                global $mysqli;
                foreach($removed_files as $key=>$value){
                    $file = '../upload/tmp_images/' . normalize_input($value);

                    $session_id = normalize_input($_SESSION['admin_session_id']);

                    $query = $mysqli->query("DELETE FROM ss_services_images_temp WHERE filename='$value' AND session_id='$session_id'");
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
            $file = '../upload/tmp_images/' . normalize_input($_POST['file']);
            $session_id = $_SESSION['admin_session_id'];
            if(file_exists($file)){
                $to = normalize_input($_POST['file']);
                $query = $mysqli->query("DELETE FROM ss_services_images_temp WHERE filename='$to' AND session_id='$session_id'");
                if ($query) {
                    if (!empty($_POST['file'])) {
                        unlink($file);
                    }
                }
            }
        }
        break;

    case 'upload_attachments';
        if (!empty($_FILES['files']['name'])) {
            include('include/class.uploader.php');
            $extensions = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'zip', 'doc', 'docs', 'webp');
            $uploader = new Uploader();
            $data = $uploader->upload($_FILES['files'], array(
                'limit' => 10, //Maximum Limit of files. {null, Number}
                'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
                'extensions' => $extensions, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                'required' => false, //Minimum one file is required for upload {Boolean}
                'uploadDir' => '../upload/tmp_images/', //Upload directory {String}
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
                        $filename = str_replace('../upload/tmp_images/','',normalize_input($file));
                        $session_id = normalize_input($_SESSION['admin_session_id']);
                        $user_id = 0;
                        $mysqli->query("INSERT INTO ss_messages_attachments_temp (user_id,filename,session_id) VALUES ('$user_id','$filename','$session_id')");
                    }
                }
            }

            if($data['hasErrors']){
                $errors = $data['errors'];
            }

            function onFilesRemoveCallback($removed_files){
                global $mysqli;
                foreach($removed_files as $key=>$value){
                    $file = '../upload/tmp_images/' . normalize_input($value);

                    $session_id = normalize_input($_SESSION['admin_session_id']);

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
    case 'remove_attachment';
        if(isset($_POST['file'])){
            $file = '../upload/tmp_images/' . normalize_input($_POST['file']);
            $session_id = normalize_input($_SESSION['admin_session_id']);
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
            $user_id = $_POST['customer_id'];
            if (empty($message)) {
                echo notification('warning',"Insert Message Please");
            } else {
                $datetime = time();

                $session_id = normalize_input($_SESSION['admin_session_id']);
                $images = $general->message_tmp_attachments($session_id);
                $add = $mysqli->query("INSERT INTO ss_messages (service_id,sale_id,customer_id,sender_id,message,datetime,readed) VALUES ('$service_id','$order_id','$user_id','0','$message','$datetime','0')");
                if ($add) {
                    $message_id = $mysqli->insert_id;
                    if ($images != 0) {
                        foreach ($images AS $image) {
                            if (!is_dir('../upload/attachments/'.$user_id)) {
                                mkdir('../upload/attachments/'.$user_id,0755);
                                copy("../upload/attachments/.htaccess", "../upload/attachments/".$user_id."/.htaccess");
                            }
                            $attachment_type = getExtension($image['filename']);
                            $filename = time().uniqid().'.'.$attachment_type;
                            rename("../upload/tmp_images/".normalize_input($image['filename']), "../upload/attachments/".normalize_input($user_id)."/".normalize_input($filename));
                            if (file_exists("../upload/tmp_images/".$image['filename'])) {
                                unlink("../upload/tmp_images/".$image['filename']);
                            }
                            $attachment_size = filesize("../upload/attachments/".normalize_input($user_id)."/".normalize_input($filename));
                            $mysqli->query("INSERT INTO ss_messages_attachments (message_id,attachment_filename,attachment_file,customer_id,attachment_type,attachment_size) VALUES ('$message_id','$image[filename]','$filename','$user_id','$attachment_type','$attachment_size')");
                            $mysqli->query("DELETE FROM ss_messages_attachments_temp WHERE session_id='$_SESSION[admin_session_id]' AND filename='$image[filename]'");
                        }
                    }
                    unset($_SESSION['admin_session_id']);
                    session_regenerate_id();
                    $inserted_message = $general->single_message($message_id);

                    $message_attachments = $general->message_attachments($message_id);
                    $result = '<div class="message admin-sender"><h4>Admin, On '.date('Y-n-j h:i a', normalize_input($inserted_message['datetime'])).'</h4><p>'.nl2br(normalize_input($inserted_message['message'])).'</p>';
                    if ($message_attachments != 0) {
                        $result .= '<div class="attachments"><h5>Attachments</h5><ul>';
                        foreach ($message_attachments AS $attachment):
                            $result .= '<li><a href="download.php?id='.normalize_input($attachment['id']).'" target="_blank">'.normalize_input($attachment['attachment_filename']).'</a></li>';
                        endforeach;
                        $result .= '</ul></div>';
                    }
                    $result .=' </div>';
                    echo normalize_input($result);
                    $customer = $general->single_customer($user_id);
                    $order = $general->single_order($order_id);
                    $message_link = rtrim($options['general_siteurl'],"/").'/dashboard/orders/order/'.normalize_input($order['order_id']).'#'.normalize_input($message_id);

                    $template = $general->email_template('new-message');
                    $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
                    $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
                    $arr_1 = array('{ORDERID}','{LINK}','{SITENAME}','{SITELINK}');
                    $arr_2 = array(normalize_input($order['order_id']),normalize_input($message_link),normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
                    $body = str_replace($arr_1,$arr_2,$cont);



                    $content = array(
                        'email' => normalize_input($customer['email']),
                        'username' => normalize_input($customer['username']),
                        'title' => normalize_input($title),
                        'body' => normalize_input($body)
                    );

                    $send_email = send_email($options,$content);
                }

            }
        }
        break;

    case 'submit_reply';
        if (isset($_POST)) {
            $message_id = (int) make_safe($_POST['message_id']);
            $reply = make_safe($_POST['reply']);
            $to_username = make_safe($_POST['to_username']);
            $to_email = make_safe($_POST['to_email']);
            $title = make_safe($_POST['title']);
            if (empty($reply)) {
                $result = array(
                    'code' => 0,
                    'message' => notification('warning',"Insert Your Reply Please")
                );
            } else {
                $datetime = time();

                $template = $general->email_template('new-reply');
                $cont = html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES));
                $title = htmlspecialchars_decode($template['template_name'], ENT_QUOTES);
                $arr_1 = array('{USERNAME}','{REPLY}','{SITENAME}','{SITELINK}');
                $arr_2 = array(normalize_input($to_username),normalize_input($reply),normalize_input($options['general_seo_title']),normalize_input($options['general_siteurl']));
                $body = str_replace($arr_1,$arr_2,$cont);




                $content = array(
                    'email' => normalize_input($to_email),
                    'username' => normalize_input($to_username),
                    'title' => normalize_input($title),
                    'body' => normalize_input($body)
                );
                $send_email = send_email($options,$content);
                if ($send_email) {
                    $update = $mysqli->query("UPDATE ss_support SET replied='1', seen='1', reply_datetime='$datetime' WHERE id='$message_id'");
                    $result = array(
                        'code' => 1,
                        'message' => notification('success',"Email sent successfully <a class='alert-link' href='support.php'>Back to support page</a>.")
                    );
                } else {
                    $result = array(
                        'code' => 0,
                        'message' => notification('warning',"Email not sent, please check your <a class='alert-link' href='setting.php?case=mail'>Mail Setting</a> and make sure all options are correct.")
                    );
                }

            }
            echo json_encode($result);
        }
        break;

    case 'restore_default_template';
        if (isset($_POST)) {
            $template_id = (int) make_safe($_POST['template_id']);
            if (!empty($template_id)) {
                $query = $mysqli->query("SELECT * FROM ss_email_templates WHERE id='$template_id'");
                $row = $query->fetch_assoc();
                $update = $mysqli->query("UPDATE ss_email_templates SET template_code='$row[template_default]' WHERE id='$template_id'");
            }
        }
        break;

        case 'save_options';
            if (isset($_POST)) {
                $message = $general->set_options($_POST,$_POST['options_set']);
                if ($message) {
                    $result = array(
                        'code' => 1,
                        'message' => $message
                    );
                }

                echo json_encode($result);
            }
        break;

default;
}