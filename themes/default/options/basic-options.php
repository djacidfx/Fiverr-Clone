<?php 
// prevent direct access
if (!isset($options)) {
die('You Can not Access Directly');	
}
?>
<div class="page-container">
    <div class="page-header page-heading">
        <h4>Basic Options</h4>
    </div>
    <form role="form" method="POST" action="">
        <div class="form">
        <?php if (isset($message)) {echo normalize_input($message);} ?>
            <div class="form-group">
                <label for="theme_direction">Theme Direction</label>
                <select name="theme_direction" id="theme_direction" class="form-control">
                    <option value="ltr" <?php if ($options['theme_direction'] == 'ltr') {echo 'SELECTED';} ?>>Left to Right</option>
                    <option value="rtl" <?php if ($options['theme_direction'] == 'rtl') {echo 'SELECTED';} ?>>Right to Left</option>
                </select>
            </div>
            <div class="form-group">
                <label for="home_services_number">Number Of Services in Homepage</label>
                <input type="number" name="home_services_number" id="home_services_number" class="form-control" value="<?php echo make_safe($options['home_services_number']); ?>" placeholder="6" />
            </div>
            <div class="form-group">
                <label for="all_services_number">Number Of Services in each page in services page</label>
                <input type="number" name="all_services_number" id="all_services_number" class="form-control" value="<?php echo make_safe($options['all_services_number']); ?>" placeholder="12" />
            </div>
            <div class="form-group">
                <label for="related_services_number">Number Of Related Services In Single Service Page</label>
                <select name="related_services_number" id="related_services_number" class="form-control">
                <?php for ($r=1;$r<11;$r++) { ?>
                    <option value="<?php echo make_safe($r); ?>" <?php if ($options['related_services_number'] == $r) {echo 'SELECTED';} ?>><?php echo make_safe($r); ?></option>
                <?php } ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" name="save" class="btn btn-dark">Save</button>
            </div>
        </div>
    </form>
</div>
