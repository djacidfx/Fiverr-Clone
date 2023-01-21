<?php
session_start();
if (isset($_SESSION['microncer_solo'])) {
unset($_SESSION['microncer_solo']);
session_destroy();
echo "<meta http-equiv='refresh' content='0;URL=login.php'>";
}
?>