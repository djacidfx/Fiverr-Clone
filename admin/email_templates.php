<?php
include('header.php');
?>
    <div class="big-title">
        <h1>E-Mail Templates</h1>
        <div class="actions">
            <a href="<?php echo help_links('email_templates'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
        </div>
    </div>
<?php
if (!empty($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
switch ($case) {

case 'edit';
$id = (int) $_GET['id'];
if (isset($_POST['submit'])) {
    $message = $templates->edit_template($_POST,$id);
}
$template = $templates->template($id);
?>
<div class="page-container">
	<div class="page-header">
        <h4><?php echo htmlspecialchars_decode($template['template_name'],ENT_QUOTES); ?></h4>
        <div class="actions">
            <a href="email_templates.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
	<?php if (isset($message)) {echo normalize_input($message);} ?>
 <div class="form">
		<form role="form" method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="template_code">Template Code <span class="required">*</span></label>
                <textarea class="form-control wysiwyg" name="template_code" id="template_code" rows="25"><?php echo html_entity_decode(htmlspecialchars_decode($template['template_code'], ENT_QUOTES)); ?></textarea>
            </div>
            <div class="form-actions">
            <button type="submit" name="submit" class="btn btn-dark">Save</button>
            <button id="restore-default" data-template-id="<?php echo $template['id']; ?>" class="btn btn-outline-dark float-right">Restore Defaults</button>
            </div>
		</form>
 </div>
</div>
<?php
break;
default;
?>
<div class="page-container">
    <div class="page-header">
        <h4>Templates</h4>
    </div>
    <div class="form">
    <?php
    if (isset($message)) {echo normalize_input($message);}
    $page = 1;
    $size = 20;
    if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
    $sqls = "SELECT * FROM ss_email_templates ORDER BY id ASC";
    $query = $mysqli->query($sqls);
    $total_records = $query->num_rows;
    if ($total_records == 0) {
        echo notification('warning','There Are No Templates.');
    } else {
        ?>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>Template</th>
                    <th width="200"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = $query->fetch_assoc()) {
                    ?>
                    <tr>
                        <td class="email-template"><h5><?php echo htmlspecialchars_decode($row['template_name'],ENT_QUOTES); ?></h5>
                            <p><?php echo htmlspecialchars_decode($row['template_desc'],ENT_QUOTES); ?></p></td>
                        <td align="right">
                            <a class="action-edit" href="email_templates.php?case=edit&id=<?php echo make_safe($row['id']); ?>" data-toggle="tooltip" data-placement="top" title="Edit">Edit</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>
<?php
} 
include('footer.php');
?>