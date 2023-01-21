<?php include('header.php'); ?>
<div class="big-title">
    <h1>Categories</h1>
    <div class="actions">
        <a href="<?php echo help_links('categories'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
    </div>
</div>
<?php
if (!empty($_GET['case'])) {
$case = make_safe($_GET['case']);	
} else {
$case = '';	
}
switch ($case) {
case 'add';
if (isset($_POST['submit'])) {
    $message = $categories->add_category($_POST);
}
?>
<div class="page-container">
    <div class="page-header">
        <h4>Add New Category</h4>
        <div class="actions">
            <a href="categories.php" class="btn btn-dark btn-sm"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
    <div class="form">
        <?php if (isset($message)) {echo $message;} ?>
        <form role="form" method="POST" action="" enctype="multipart/form-data">
          <div class="form-group">
            <label for="category">Category <span class="required">*</span></label>
            <input type="text" class="form-control" name="category" id="category" />
          </div>
          <div class="form-group">
            <label for="seo_keywords">SEO Keywords</label>
            <input type="text" class="form-control tags" name="seo_keywords" id="seo_keywords" />
          </div>
          <div class="form-group">
            <label for="seo_description">SEO Description</label>
            <textarea class="form-control" name="seo_description" id="seo_description" rows="3" ></textarea>
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
$id = (int) abs(make_safe(xss_clean($_GET['id'])));
if (isset($_POST['submit'])) {
    $message = $categories->edit_category($_POST,$id);
}
$category = $categories->category($id);
?>
<div class="page-container">
    <div class="page-header">
        <h4>Edit Category</h4>
        <div class="actions">
            <a href="categories.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
    <div class="form">
        <?php if (isset($message)) {echo $message;} ?>
		<form role="form" method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category">Category <span class="required">*</span></label>
                <input type="text" class="form-control" name="category" id="category" value="<?php echo $category['category']; ?>" />
            </div>
            <div class="form-group">
                <label for="seo_keywords">SEO Keywords</label>
                <input type="text" class="form-control tags" name="seo_keywords" id="seo_keywords" value="<?php echo $category['seo_keywords']; ?>" />
            </div>
            <div class="form-group">
                <label for="seo_description">SEO Description</label>
                <textarea class="form-control" name="seo_description" id="seo_description" rows="3" ><?php echo $category['seo_description']; ?></textarea>
            </div>
            <div class="form-actions">
                <input type="hidden" name="old_thumbnail" value="<?php echo $category['image']; ?>" />
                <button type="submit" name="submit" class="btn btn-dark">Save</button>
            </div>
		</form>
    </div>
</div>
<?php
break;
case 'delete';
        $id = (int) abs(make_safe(xss_clean($_GET['id'])));
        $type = make_safe(xss_clean($_GET['type']));
        if (isset($_POST['move'])) {
            $result = $categories->move_category($_POST,$id);
            $message = $result['message'];
            $done = $result['done'];
        }
        if (isset($_POST['delete'])) {
            $result = $categories->delete_category($id);
            $message = $result['message'];
            $done = $result['done'];
        }
        $tcategory = $categories->category($id);
        ?>
    <div class="page-container">
        <div class="page-header">
            <h4>Delete Category</h4>
            <div class="actions">
                <a href="categories.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
        <div class="form">
            <?php if (isset($message)) {echo $message;} ?>
            <form role="form" method="POST" action="">
                <?php if (!isset($done) AND $categories->get_category_services($id) > 0) { ?>
                    <div class="alert alert-warning">The Category <b><?php echo $tcategory['category']; ?></b> Contains <b><?php echo $categories->get_category_services($id); ?></b> Service(s). Do You Want To Move Them to Another Category ?</div>
                    <div class="form-group">
                        <label for="category_id">Choose a Category to Move The Services(s) To.</label>
                        <select class="form-control" name="category_id" id="category_id">
                            <option value="0">Uncategorized</option>
                            <?php
                            $main = $categories->categories_query('WHERE id!="'.$id.'"','category_order ASC');
                            foreach ($main AS $category) {
                                ?>
                                <option value="<?php echo $category['id']; ?>"> - <?php echo $category['category']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-warning">Are you sure that you want to delete this category <strong><?php echo $tcategory['category']; ?></strong></div>
                <?php }  ?>
                <div class="form-actions">
                <?php if (isset($done)) { ?>
                    <a href="categories.php" class="btn btn-dark">Back To Categories</a>
                <?php } else { ?>
                    <?php if($categories->get_category_services($id) > 0): ?>
                        <button type="submit" name="move" class="btn btn-dark cancel-btn">Move Then Delete</button>
                        <button type="submit" name="delete" class="btn btn-danger cancel-btn">Delete</button>
                    <?php else: ?>
                        <button type="submit" name="delete" class="btn btn-danger cancel-btn">Delete</button>
                    <?php endif; ?>
                <?php } ?>
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
    <h4>All Categories</h4>
    <div class="actions">
        <a href="categories.php?case=add" class="btn btn-success btn-sm"><i class="ri-add-line"></i></a>
    </div>
</div>
    <div class="form">
<?php
$mains = $categories->main_categories('category_order ASC');
if ($mains == 0) :
echo notification('warning','You didn\'t add any category. <a href="?case=add">Add new category</a>.');	
else :
?>

<div class="categories-header">
    <div class="row">
        <div class="col-3">Category</div>
        <div class="col-9"></div>
    </div>
</div>
<div class="sort-categories">
<ul>
<?php foreach ($mains AS $category): ?>
    <li id="records_<?php echo $category['id']; ?>" class="category-li" title="Drag To Re-Order">
        <div class="row">
            <div class="col-3"><?php echo $category['category']; ?></div>
            <div class="col-9 text-right">
                <a href="categories.php?case=edit&id=<?php echo $category['id']; ?>" class="action-edit">Edit</a>
                <a href="categories.php?case=delete&id=<?php echo $category['id']; ?>&type=main" class="action-remove">Delete</a>
            </div>
        </div>
    </li>

<?php
endforeach;
?>
</ul>
</div>
    </div>
    </div>
<?php
endif;
}
include('footer.php');
?>