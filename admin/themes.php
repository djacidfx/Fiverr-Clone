<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Theme Options</h1>
    </div>
<?php
$general_s = $general->get_options('General');
$options = $general->get_theme_options($general_s['site_theme']);
if (isset($_GET['case'])) {
    $case = make_safe($_GET['case']);
} else {
    $case = '';
}
switch ($case) {
    case 'logo';
        if (isset($_POST['save'])) {
            $up = new fileDir('../upload/');
            if (!empty($_FILES['logo']['name'])) {
                $info = getimagesize($_FILES['logo']['tmp_name']);
                if ($info === FALSE) {
                    $logo = $_POST['old_logo'];
                } elseif (($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                    $logo = $_POST['old_logo'];
                } else {

                    $logo = $up->upload($_FILES['logo']);
                    $up->delete($_POST['old_logo']);
                }
            } else {
                $logo = $_POST['old_logo'];
            }
            if (!empty($_FILES['favicon']['name'])) {
                $info = getimagesize($_FILES['favicon']['tmp_name']);
                if ($info === FALSE) {
                    $favicon = $_POST['old_favicon'];
                } elseif (($info[2] !== IMAGETYPE_PNG)) {
                    $favicon = $_POST['old_favicon'];
                } else {
                    $favicon = $up->upload($_FILES['favicon']);
                    $up->delete($_POST['old_favicon']);
                }
            } else {
                $favicon = normalize_input($_POST['old_favicon']);
            }
            $array = array(
                'logo'    				=> normalize_input($logo),
                'favicon' 				=> normalize_input($favicon)
            );
            $message = $general->set_theme_options($array,$general_s['site_theme']);
        }


        include('../themes/'.$general_s['site_theme'].'/options/logo-options.php');

        break;
    case 'fonts';

        if (isset($_POST['save'])) {
            $message = $general->set_theme_options($_POST,$general_s['site_theme']);
        }
        include('../themes/'.$general_s['site_theme'].'/options/fonts-options.php');

        break;
default;
if (isset($_POST['save'])) {
$message = $general->set_theme_options($_POST,$general_s['site_theme']);
}
include('../themes/'.$general_s['site_theme'].'/options/basic-options.php');

}
?>			


<?php
include('footer.php');
?>