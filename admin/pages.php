<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Pages</h1>
        <div class="actions">
            <a href="<?php echo help_links('pages'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
        </div>
    </div>
<?php
if (isset($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
switch ($case) {
case 'add';
if (isset($_POST['submit'])) {
    $message = $pages->add_page($_POST);
}
?>
<div class="page-container">
	<div class="page-header">
        <h4>Add Page</h4>
        <div class="actions">
            <a href="pages.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
	<?php if (isset($message)) {echo normalize_input($message);} ?>
 <div class="form">
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
			<label for="content">Content <span class="required">*</span></label>
			<textarea class="form-control wysiwyg" name="content" id="content" rows="35" ></textarea>
		  </div>
		  <div class="form-group">
			<label for="seo_keywords">SEO Keywords</label>
			<input type="text" data-role="tagsinput" class="form-control tags" name="seo_keywords" id="seo_keywords" />
		  </div>
		  <div class="form-group">
			<label for="seo_description">SEO Description</label>
			<textarea class="form-control" name="seo_description" id="seo_description" rows="3"></textarea>
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
$message = $pages->edit_page($_POST,$id);
}
$page = $pages->page($id);
?>
<div class="page-container">
	<div class="page-header">
       <h4>Edit Page</h4>
        <div class="actions">
            <a href="pages.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
	<?php if (isset($message)) {echo normalize_input($message);} ?>
 <div class="form">
		<form role="form" method="POST" action="" enctype="multipart/form-data">
		  <div class="form-group">
			<label for="title">Title <span class="required">*</span></label>
			<input type="text" class="form-control" name="title" id="title" value="<?php echo make_safe($page['title']); ?>" />
		  </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <p class="help">If you leave it empty the slug will be generated from title</p>
                <input type="text" class="form-control" name="slug" id="slug" value="<?php echo make_safe($page['slug']); ?>" />
            </div>
		  <div class="form-group">
			<label for="content">Content <span class="required">*</span></label>
			<textarea class="wysiwyg form-control" name="content" id="content" rows="35" ><?php echo htmlspecialchars_decode($page['content'],ENT_QUOTES); ?></textarea>
		  </div>
		  <div class="form-group">
			<label for="seo_keywords">SEO Keywords</label>
			<input type="text" data-role="tagsinput" class="form-control tags" name="seo_keywords" id="seo_keywords" value="<?php echo make_safe($page['seo_keywords']); ?>" />
		  </div>
		  <div class="form-group">
			<label for="seo_description">SEO Description</label>
			<textarea class="form-control" name="seo_description" id="seo_description" rows="3"><?php echo make_safe($page['seo_description']); ?></textarea>
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
?>
<div class="page-container">
    <div class="page-header">
       <h4>All Pages</h4>
        <div class="actions">
            <a href="pages.php?case=add" class="btn btn-success btn-sm float-right"><i class="ri-add-line"></i></a>
        </div>
    </div>
<?php
$allpages = $pages->all_pages('page_order ASC');
if ($allpages == 0) {
echo notification('warning','You didn\'t add any Page. <a href="?case=add" class="alert-link">Add new Page</a>.');	
} else {
?>
<div class="categories-header">
    <div class="row">
        <div class="col-9">Title</div>
        <div class="col-3"></div>
    </div>
</div>
<div class="sort-pages">
<ul>
<?php
foreach ($allpages AS $page) {
?>
<li id="records_<?php echo make_safe($page['id']); ?>" class="category-li" title="Drag To Re-Order">
    <div class="row">
        <div class="col-9"><?php echo make_safe($page['title']); ?></div>
        <div class="col-3 text-right">
            <a href="pages.php?case=edit&id=<?php echo make_safe($page['id']); ?>" class="action-edit">Edit</a>
            <a href="javascript:deletePage(<?php echo make_safe($page['id']); ?>);" class="action-remove">Delete</a>
        </div>
    </div>
</li>
<?php	
}	
?>
</ul>
</div>
    </div>
<?php
}
} 
include('footer.php');
?>