<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Account</h1>
    </div>
<?php
if (!empty($_GET['case'])) {
    $case = make_safe($_GET['case']);
} else {
    $case = '';
}
switch ($case) {
    case 'change_password';
        if (isset($_POST['submit'])) {
            $message = $general->change_password($_POST, $_SESSION['microncer_solo']);
        }
        ?>
        <div class="page-container">
            <div class="page-header">
                <h4>Change Password</h4>
            </div>
            <div class="form">
                <?php if (isset($message)) {
                    echo normalize_input($message);
                } ?>
                <form role="form" method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password <span class="required">*</span></label>
                        <input type="password" class="form-control" name="current_password" id="current_password"/>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password <span class="required">*</span></label>
                        <input type="password" class="form-control" name="new_password" id="new_password"/>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password"/>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
     break;
    default;
        if (isset($_POST['submit'])) {
            $message = $general->change_account($_POST,$_SESSION['microncer_solo']);
        }
        $row = $general->admin($_SESSION['microncer_solo']);
        ?>
        <div class="page-container">
            <div class="page-header">
                <h4> Edit Account Info</h4>
            </div>
            <div class="form">
                <?php if (isset($message)) {echo normalize_input($message);} ?>
                <form role="form" method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" class="form-control" name="username" id="username" value="<?php echo make_safe($row['username']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="email">E-Mail <span class="required">*</span></label>
                        <input type="text" class="form-control" name="email" id="email" value="<?php echo make_safe($row['email']); ?>" />
                    </div>
                    <div class="form-actions">
                    <button type="submit" name="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
            </div>
        </div>
    <?php
}
include('footer.php');
?>