<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Services</h1>
        <div class="actions">
            <a href="<?php echo help_links('services'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
        </div>
    </div>
<?php
$_SESSION['admin_session_id'] = session_id();
if (!empty($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
switch ($case) {

case 'add';
if (isset($_POST['submit'])) {
    $message = $services->add_service($_POST);
}
    $mains = $categories->main_categories('category_order ASC');
?>
<div class="page-container">
	<div class="page-header">
        <h4>Add Service</h4>
        <div class="actions">
            <a href="services.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
 <div class="form">
     <?php if (isset($message)) {echo normalize_input($message);} ?>
		<form role="form" method="POST" action="" enctype="multipart/form-data">
		  <div class="form-group">
			<label for="title">Title <span class="required">*</span></label>
			<input type="text" class="form-control" name="title" id="title" />
		  </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <p class="help">If you leave it empty the slug will be generated from title</p>
                <input type="text" class="form-control" name="slug" id="slug" />
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control">
                    <option value="0">Uncategorized</option>
                    <?php foreach($mains AS $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo $category['category']; ?></option>
                    <?php endforeach;?>
                </select>
            </div>
		  <div class="form-group">
			<label for="content">Details <span class="required">*</span></label>
			<textarea class="form-control wysiwyg" name="content" id="content" rows="25"></textarea>
		  </div>
            <div class="form-group">
                <label for="price">Price <span class="required">*</span></label>
                <input type="text" class="form-control" name="price" id="price" />
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="digital_download" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="digital_download" id="digital_download" value="1" /> <span class="checkbox-label">Digital Download Service</span>
                </label>
            </div>
            <div class="digital-download-div">
                <div class="form-group">
                    <label>File</label>
                    <div class="custom-file">
                        <input type="file" class="form-control" name="digital_download_file" id="digital_download_file" />
                        <label class="custom-file-label" for="digital_download_file">Choose file</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="delivery">Delivery (Days)</label>
                <p class="help">How many days you need to complete and deliver this service.</p>
                <input type="number" class="form-control" name="delivery" id="delivery" />
            </div>
            <div class="images-inputs form-group">
                <label for="filer_input2">Service Gallery</label>
                <p class="help">First image will be assigned as cover image for the service.</p>
                <input type="file" name="files[]" id="image_input">
            </div>
            <div class="form-group">
                <label for="requirements">Service Requirements</label>
                <p class="help">This text will appear to customers after purchasing the service,<br /><strong>for example</strong> instagram account details, or website FTP, or any extra details that help you to complete the service.</p>
                <textarea class="form-control wysiwyg" name="requirements" id="requirements" rows="20"></textarea>
            </div>
            <div class="form-group">
                <label for="seo_keywords">SEO Keywords</label>
                <p class="help">If you leave it empty the keywords will be generated from title</p>
                <input type="text" data-role="tagsinput" class="form-control" name="seo_keywords" id="seo_keywords" />
            </div>
            <div class="form-group">
                <label for="seo_description">SEO Description</label>
                <p class="help">If you leave it empty the description will be generated from details</p>
                <textarea class="form-control" name="seo_description" id="seo_description" rows="4"></textarea>
            </div>
            <div class="form-group">
            <label>
                <input type="hidden" name="active" value="0" />
                <input data-toggle="toggle" data-size="mini" type="checkbox" name="active" id="active" value="1" /> <span class="checkbox-label">Publish the service</span>
            </label>
            </div>
            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-dark">Save</button>
            </div>
		</form>
 </div>
</div>
<?php
break;
case 'edit';
$id = abs(intval(make_safe(xss_clean($_GET['id']))));
if (isset($_POST['submit'])) {
$message = $services->edit_service($_POST,$id);
}
    $service = $services->service($id);
    $mains = $categories->main_categories('category_order ASC');
?>
<div class="page-container">
	<div class="page-header">
        <h4>Edit Service</h4>
        <div class="actions">
            <a href="services.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>

    <div class="form">
        <?php if (isset($message)) {echo normalize_input($message);} ?>
		<form role="form" method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title <span class="required">*</span></label>
                <input type="text" class="form-control" name="title" id="title" value="<?php echo make_safe($service['title']); ?>" />
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <p class="help">If you leave it empty the slug will be generated from title</p>
                <input type="text" class="form-control" name="slug" id="slug" value="<?php echo make_safe($service['slug']); ?>" />
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control">
                    <option value="0" <?php if($service['category_id'] == 0): ?>SELECTED<?php endif; ?>>Uncategorized</option>
                    <?php foreach($mains AS $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if($service['category_id'] == $category['id']): ?>SELECTED<?php endif; ?>><?php echo $category['category']; ?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Details <span class="required">*</span></label>
                <textarea class="form-control wysiwyg" name="content" id="content" rows="25" ><?php echo htmlspecialchars_decode($service['content'], ENT_QUOTES); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price <span class="required">*</span></label>
                <input type="text" class="form-control" name="price" id="price" value="<?php echo make_safe($service['price']); ?>" />
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="digital_download" value="0" />
                    <input data-toggle="toggle" data-size="mini" type="checkbox" name="digital_download" id="digital_download" value="1" <?php if(isset($service['digital_download']) && $service['digital_download'] == 1): echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Digital Download Service</span>
                </label>
            </div>
            <div class="digital-download-div">
                <div class="form-group">
                    <label>File</label>
                    <div class="custom-file">
                        <input type="file" class="form-control" name="digital_download_file" id="digital_download_file" />
                        <label class="custom-file-label" for="digital_download_file">Choose file</label>
                    </div>
                    <?php if (!empty($service['digital_download_file'])) { ?>
                        <p class="help"><span class="icon icon-s-remove"></span> Current File : <?php echo normalize_input($service['digital_download_file']); ?></p>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <label for="delivery">Delivery (Days)</label>
                <p class="help">How many days you need to complete and deliver this service.</p>
                <input type="number" class="form-control" name="delivery" id="delivery" value="<?php echo make_safe($service['delivery']); ?>" />
            </div>
            <div class="images-inputs form-group">
                <label for="filer_input2">Service Gallery</label>
                <p class="help">First image will be assigned as cover image for the service.</p>
                <input type="file" name="files[]" id="image_input">
                <div class="service-images">
                    <?php
                    $images = $services->service_images($id);
                    if ($images != 0) {
                        ?>
                        <div class="row">
                            <?php
                            foreach ($images AS $image) {
                                ?>
                                <div class="col-lg-3 col-md-4 col-sm-6" id="image-<?php echo normalize_input($image['id']); ?>">
                                    <div class="service-image">
                                        <img src="../upload/services/<?php echo make_safe($id).'/'.make_safe($image['filename']); ?>" class="img-fluid" />
                                        <a href="javascript:void();" class="delete-service-image" data-image-id="<?php echo make_safe($image['id']); ?>">
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label for="requirements">Service Requirements</label>
                <p class="help">This text will appear to customers after purchasing the service,<br /><strong>for example</strong> instagram account details, or website FTP, or any extra details that help you to complete the service.</p>
                <textarea class="form-control wysiwyg" name="requirements" id="requirements" rows="20"><?php echo htmlspecialchars_decode($service['requirements'], ENT_QUOTES); ?></textarea>
            </div>
            <div class="form-group">
                <label for="seo_keywords">SEO Keywords</label>
                <p class="help">If you leave it empty the keywords will be generated from title</p>
                <input type="text" data-role="tagsinput" class="form-control" name="seo_keywords" id="seo_keywords" value="<?php echo make_safe($service['seo_keywords']); ?>" />
            </div>
            <div class="form-group">
                <label for="seo_description">SEO Description</label>
                <p class="help">If you leave it empty the description will be generated from details</p>
                <textarea class="form-control" name="seo_description" id="seo_description" rows="4"><?php echo make_safe($service['seo_description']); ?></textarea>
            </div>
            <div class="form-group">
            <label>
                <input type="hidden" name="active" value="0" />
                <input data-toggle="toggle" data-size="mini" type="checkbox" name="active" id="active" value="1" <?php if ($service['active'] == 1) : echo 'CHECKED'; endif; ?> /> <span class="checkbox-label">Publish the service</span>
            </label>
            </div>
            <div class="form-actions">
                <input type="hidden" name="current_digital_download_file" value="<?php echo normalize_input($service['digital_download_file']); ?>" />
		        <button type="submit" name="submit" class="btn btn-dark">Save</button>
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
        <h4>All Services</h4>
        <div class="actions">
            <a href="services.php?case=add" class="btn btn-success btn-sm float-right"><i class="ri-add-line"></i></a>
        </div>
    </div>
    <div class="form">
    <?php
    if (isset($message)) {echo normalize_input($message);}
    $page = 1;
    $size = 10;
    if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
    $sqls = "SELECT * FROM ss_services WHERE deleted='0' ORDER BY id DESC";
    $query = $mysqli->query($sqls);
    $total_records = $query->num_rows;
    if ($total_records == 0) {
        echo notification('warning','There Are No Services yet.');
    } else {
        $pagination = new Pagination();
        $pagination->setLink("?page=%s");
        $pagination->setPage($page);
        $pagination->setSize($size);
        $pagination->setAlign('justify-content-start');
        $pagination->setTotalRecords($total_records);
        $get = "SELECT * FROM ss_services WHERE deleted='0' ORDER BY id DESC ".$pagination->getLimitSql();
        $q = $mysqli->query($get);
        ?>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th width="35"></th>
                    <th>Service</th>
                    <th class="hidden-xs">Category</th>
                    <th class="hidden-xs">Price</th>
                    <th class="hidden-xs">Delivery</th>
                    <th width="200"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = $q->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <?php if ($row['active'] == 0) { ?>
                                <span class="badge badge-secondary">Stopped</span>
                            <?php } else { ?>
                                <span class="badge badge-success">Active</span>
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars_decode($row['title'],ENT_QUOTES); ?></td>
                        <td class="hidden-xs"><?php echo get_category($row['category_id']); ?></td>
                        <td class="hidden-xs" width="120"><?php echo make_safe($row['price']); ?></td>
                        <td class="hidden-xs" width="120"><?php echo make_safe($row['delivery']); ?> Days</td>
                        <td align="right">
                            <a class="action-edit" href="services.php?case=edit&id=<?php echo make_safe($row['id']); ?>" data-toggle="tooltip" data-placement="top" title="Edit">Edit</a>
                            <a class="action-remove" href="javascript:deleteService(<?php echo make_safe($row['id']); ?>);" data-toggle="tooltip" data-placement="top" title="Delete">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <div class="news-actions">
                <div class="row">
                    <div class="col-12"><?php echo normalize_input($pagination->create_links()); ?></div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php }
include('footer.php');
?>