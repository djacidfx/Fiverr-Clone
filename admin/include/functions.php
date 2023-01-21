<?php
function make_safe($str)
{   global $mysqli;
    return $mysqli->real_escape_string(trim($str));
}

function normalize_input($input) {
    return trim($input);
}

function get_current_version_number() {
    $versionFile = file_get_contents('version.xml');
    $version = new SimpleXMLElement($versionFile);
    return $version;
}
function getExtension($str)
{
    $i = strrpos($str,".");
    if (!$i) { return ""; }
    $l = strlen($str) - $i;
    $ext = substr($str,$i+1,$l);
    return $ext;
}

function search_for_sql($src) {
    global $mysqli;
    $files = glob($src."*.sql");
    if (count($files) > 0) {
        foreach($files AS $file) {
            $sql = file_get_contents($file);
            $mysqli->multi_query($sql);
        }
    }
}


function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' ) && (!in_array(getExtension($file),array('zip','sql')))) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") {
                    rrmdir($dir."/".$object);
                    rmdir($dir."/".$object);
                } else {
                    unlink   ($dir."/".$object);
                }
            }
        }
        reset($objects);
    }
}




function currencies() {
    $currencies = array(
        'Australian dollar' => 'AUD',
        'Canadian dollar' => 'CAD',
        'Euro' => 'EUR',
        'United States dollar' => 'USD',
        'Pound sterling' => 'GBP',
        'Russian ruble' => 'RUB',
        'Swedish krona' => 'SEK',
        'Indian rupee' => 'INR'
    );
    return $currencies;
}

function customer_orders($customer_id) {
    global $mysqli;
    $customer_id = make_safe($customer_id);
    $sql = "SELECT * FROM ss_sales WHERE customer_id='$customer_id' AND deleted='0'";
    $query = $mysqli->query($sql);
    return $query->num_rows;
}

function get_category($category_id) {
    global $mysqli;
    $category_id = make_safe($category_id);
    $sql = "SELECT * FROM ss_categories WHERE id='$category_id'";
    $query = $mysqli->query($sql);
    if($query->num_rows == 0) {
        return 'Uncategorized';
    } else {
        $row = $query->fetch_assoc();
        return $row['category'];
    }
}

function get_unreaded_messages_number($sale_id) {
    global $mysqli;
    $sale_id = make_safe($sale_id);
    $sql = "SELECT * FROM ss_messages WHERE sale_id='$sale_id' AND sender_id!='0' AND readed='0'";
    $query = $mysqli->query($sql);
    return $query->num_rows;
}

function get_service_title($service_id) {
    global $mysqli;
    $service_id = make_safe($service_id);
    $sql = "SELECT title FROM ss_services WHERE id='$service_id'";
    $query = $mysqli->query($sql);
    $row = $query->fetch_assoc();
    return $row['title'];
}

function get_order_id($id) {
    global $mysqli;
    $id = make_safe($id);
    $sql = "SELECT order_id FROM ss_sales WHERE id='$id'";
    $query = $mysqli->query($sql);
    $row = $query->fetch_assoc();
    return $row['order_id'];
}

function get_customer($customer_id) {
    global $mysqli;
    $customer_id = make_safe($customer_id);
    $sql = "SELECT username FROM ss_customers WHERE id='$customer_id'";
    $query = $mysqli->query($sql);
    $row = $query->fetch_assoc();
    return $row['username'];
}


function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function notification($type,$text) {
return '<div class="alert alert-'.$type.'">'.$text.'</div>';
}


function genRandomString($length) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}

function genRandomNumber($length) {
    $characters = '0123456789';
    $string = '';    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}
 
function xss_clean($data)
{
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
                // Remove really unwanted tags
                $old_data = $data;
                $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
}

function slugit($title)
{
$slugged = url_slug(
	"$title", 
	array(
		'delimiter' => '-',
		'lowercase' => true
	)
);
$string = str_replace('quot-','',$slugged);
$string = str_replace('-quot','',$string);
$string = str_replace('-amp','',$string);
$string = str_replace('amp-','',$string);
return $string;
}

// function to prepare the slugging
function url_slug($str, $options = array()) {
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
	
	$defaults = array(
		'delimiter' => '-',
		'limit' => null,
		'lowercase' => true,
		'replacements' => array(),
		'transliterate' => true,
	);
	
	// Merge options
	$options = array_merge($defaults, $options);
	
	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
		'ß' => 'ss', 
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
		'ÿ' => 'y',

		// Latin symbols
		'©' => '(c)',

		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 

		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',

		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
		'Ž' => 'Z', 
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z', 

		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
		'Ż' => 'Z', 
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',

		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);
	
	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
	
	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}
	
	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
	
	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
	
	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
	
	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);
	
	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

function empty_templates_cache($str){
         if(is_file($str)){
             return @unlink($str);
         }
         elseif(is_dir($str)){
             $scan = glob(rtrim($str,'/').'/*.php');
             foreach($scan as $index=>$path){
			 if (str_replace($str,'',$path) === 'index.html') continue;
                 empty_templates_cache($path);
             }
         return true;
		 }
}

function generate_statics_select($year,$selected_year) {
	$result = '';
	for($i=$year; $i < date('Y')+1; $i++) {
	    if ($i == $selected_year):
            $result .= '<option value="?year='.$i.'" selected>'.$i.'</option>';
	    else:
            $result .= '<option value="?year='.$i.'">'.$i.'</option>';
        endif;
    }
return $result;	
}

function month_name($month) {
$month_lang = array(
1 => 'Jan',
2 => 'Feb',
3 => 'Mar',
4 => 'Apr',
5 => 'May',
6 => 'Jun',
7 => 'Jul',
8 => 'Aug',
9 => 'Sep',
10 => 'Oct',
11 => 'Nov',
12 => 'Dec'
);
return $month_lang[$month];
}

function content_words_count($string) {
$string = trim(strip_tags($string));
$words = count(explode(" ", $string));	
return $words;	
}

function title_to_keywords($title) {
	$searchs = array();
	$tags = explode('-',slugit($title));
		foreach ($tags AS $tag) {
			if (mb_strlen($tag,'UTF-8') > 4) {
			$searchs[] .= trim($tag);
			}
		}
	if (count($searchs) > 0) {
	return implode(',',$searchs);
	} else {
	return false;	
	}
}





function curPageURL() 
{
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"])) {
 $https = $_SERVER["HTTPS"]; 
 }
 if (isset($https) AND $https == "on") {$pageURL .= "s";} else {$pageURL .= "";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function send_simple_mail($from,$to,$title,$body) {
    $header = "From:$from \r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";
    $header .= 'Cc: somebody@domain.com' . "\r\n";

    $send = mail ($to,$title,$body,$header);
    if ($send) {
        return true;
    } else {
        return false;
    }
}

function send_smtp_mail($host,$port,$username,$password,$to,$to_username,$title,$body,$site_title) {
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();                               // Set mailer to use SMTP
    $mail->Host = "$host";  					   // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                        // Enable SMTP authentication
    $mail->Username = "$username";                 // SMTP username
    $mail->Password = "$password";                 // SMTP password
    $mail->SMTPSecure = 'tls';                     // Enable TLS encryption, `ssl` also accepted
    $mail->Port = $port;             			   // TCP port to connect to
    $mail->From = "$username";
    $mail->FromName = "$site_title";
    $mail->AddCC($username);
    $mail->addAddress("$to", "$to_username");      // Add a recipient
    $mail->isHTML(true);                           // Set email format to HTML
    $mail->Subject = "$title";
    $mail->Body    = "$body";
    $mail->AltBody = "".strip_tags($body)."";
    if ($mail->send()) {
        return true;
    } else {
        return false;
    }
}

function send_sendgrid_mail($to,$to_username,$title,$body) {
    global $options;
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($options['mail_sender_email'], $options['mail_sender_name']);
    $email->setSubject($title);
    $email->addTo($to, $to_username);
    $email->addCc($options['mail_sender_email'], $options['mail_sender_name']);
    $email->addContent("text/html", $body);
    $sendgrid = new \SendGrid($options['mail_sendgrid_api_key']);
    try {
        $response = $sendgrid->send($email);
        if ($response->statusCode() == 202) {
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
}

function send_email($options,$content) {
    if ($options['mail_mail_method'] == 'mail') {
        $send_email = send_simple_mail($options['mail_send_email'],$content['email'],$content['title'],$content['body']);
    } elseif ($options['mail_mail_method'] == 'sendgrid') {
        $send_email = send_sendgrid_mail($content['email'],$content['username'],$content['title'],$content['body']);
    } else {
        $send_email = send_smtp_mail($options['mail_smtp_host'],$options['mail_smtp_port'],$options['mail_smtp_username'],$options['mail_smtp_password'],$content['email'],$content['username'],$content['title'],$content['body'],$options['general_seo_title']);
    }
    if ($send_email) {
        return true;
    } else {
        return false;
    }
}
function google_fonts($google_api_key) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/webfonts/v1/webfonts?key=" . $google_api_key);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $fonts_list = json_decode(curl_exec($ch), true);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($http_code != 200) {
        return null;
    } else {
        return $fonts_list;
    }
}

function help_links($link) {
    $links = array(
        'categories' => 'https://microncer.com/docs/categories/',
        'services' => 'https://microncer.com/docs/services/',
        'customers' => 'https://microncer.com/docs/customers/',
        'orders' => 'https://microncer.com/docs/orders/',
        'messages' => 'https://microncer.com/docs/messages/',
        'email_templates' => 'https://microncer.com/docs/e-mail-templates/',
        'pages' => 'https://microncer.com/docs/pages/',
        'slider' => 'https://microncer.com/docs/slider/',
        'support' => 'https://microncer.com/docs/support/',
        'updates' => 'https://microncer.com/docs/how-to-update/',
        'settings_general' => 'https://microncer.com/docs/general-settings/',
        'settings_payment' => 'https://microncer.com/docs/payment-settings/',
        'settings_customers' => 'https://microncer.com/docs/customers-settings/',
        'settings_apis' => 'https://microncer.com/docs/apis-settings/',
        'settings_mail' => 'https://microncer.com/docs/mail-settings/',
        'settings_social' => 'https://microncer.com/docs/social-settings/',
        'install_upload_files' => 'https://microncer.com/docs/upload-files/',
        'install_create_db' => 'https://microncer.com/docs/create-database/',
        'install_edit_config' => 'https://microncer.com/docs/edit-config-file/',
        'install_run_installation' => 'https://microncer.com/docs/run-installation/',
        'apis_paypal' => 'https://microncer.com/docs/paypal-api/',
        'apis_stripe' => 'https://microncer.com/docs/stripe-api/',
        'apis_recaptcha' => 'https://microncer.com/docs/recaptcha-api/',
        'apis_sendgrid' => 'https://microncer.com/docs/sendgrid-api/',
        'apis_facebook' => 'https://microncer.com/docs/facebook-api/',
        'apis_twitter' => 'https://microncer.com/docs/twitter-api/',
        'apis_envato' => 'https://microncer.com/docs/envato-api/',
    );
    return $links[$link];
}
?>