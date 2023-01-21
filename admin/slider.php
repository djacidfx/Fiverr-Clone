<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Slider</h1>
        <div class="actions">
            <a href="<?php echo help_links('slider'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
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
    $message = $slider->add_slide($_POST);
}
?>
<div class="page-container">
	<div class="page-header">
        <h4>Add slide</h4>
        <div class="actions">
            <a href="slider.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
 <div class="form">
 <?php if (isset($message)) {echo normalize_input($message);} ?>
		<form role="form" method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Slide Name <span class="required">*</span></label>
                <input type="text" class="form-control" name="title" id="title" />
            </div>
            <div class="form-group">
                <label>Image <span class="required">*</span></label>
                <div class="custom-file">
                    <input type="file" class="form-control" name="image" id="image" />
                    <label class="custom-file-label" for="image">Choose file</label>
                </div>
            </div>
		  <div class="form-group">
			<label for="slide_title">Slide Title</label>
			<input type="text" class="form-control" name="slide_title" id="slide_title" />
		  </div>
		  <div class="form-group">
			<label for="slide_text">Slide Text</label>
			<textarea class="form-control" name="slide_text" id="slide_text" rows="4"></textarea>
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
$message = $slider->edit_slide($_POST,$id);
}
$slide = $slider->slide($id);
?>
<div class="page-container">
	<div class="page-header">
        <h4>Edit slide</h4>
        <div class="actions">
            <a href="slider.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>

 <div class="form">
 <?php if (isset($message)) {echo normalize_input($message);} ?>
     <form role="form" method="POST" action="" enctype="multipart/form-data">
         <div class="form-group">
             <label for="title">Slide Name <span class="required">*</span></label>
             <input type="text" class="form-control" name="title" id="title" value="<?php echo make_safe($slide['title']); ?>" />
         </div>
         <div class="form-group">
             <label>Image <span class="required">*</span></label>
             <div class="custom-file">
                 <input type="file" class="form-control" name="image" id="image" />
                 <label class="custom-file-label" for="image">Choose file</label>
             </div>
             <?php if (!empty($slide['slide_image'])) { ?>
                 <p class="help">Current Image : <a href="javascript:void();" data-toggle="modal" data-target="#current_slide_image" title="Current Image"><?php echo normalize_input($slide['slide_image']); ?></a></p>
             <?php } ?>
             <div class="modal fade" id="current_slide_image" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                 <div class="modal-dialog" role="document">
                     <div class="modal-content">
                         <div class="modal-header">
                             <h5 class="modal-title" id="exampleModalLabel">Current Image</h5>
                             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                 <span aria-hidden="true">&times;</span>
                             </button>
                         </div>
                         <div class="modal-body">
                             <img src="../upload/slider/<?php echo normalize_input($slide['slide_image']); ?>" class="img-fluid" />
                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="form-group">
             <label for="slide_title">Slide Title</label>
             <input type="text" class="form-control" name="slide_title" id="slide_title" value="<?php echo make_safe($slide['slide_title']); ?>" />
         </div>
         <div class="form-group">
             <label for="slide_text">Slide Text</label>
             <textarea class="form-control" name="slide_text" id="slide_text" rows="4"><?php echo make_safe($slide['slide_text']); ?></textarea>
         </div>
         <div class="form-actions">
            <input type="hidden" name="slide_image" value="<?php echo make_safe($slide['slide_image']); ?>" />
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
        <h4>All Slides</h4>
        <div class="actions">
            <a href="slider.php?case=add" class="btn btn-success btn-sm float-right"><i class="ri-add-line"></i></a>
        </div>
    </div>
    <div class="form">
    <?php
    if (isset($message)) {echo normalize_input($message);}
    $slides = $slider->slides();
    if ($slides == 0) {
        echo notification('warning','There Are No Active slides.');
    } else {
        ?>
        <div class="categories-header">
            <div class="row">
                <div class="col-3">Slide</div>
                <div class="col-9"></div>
            </div>
        </div>
        <div class="sort-slides">
        <ul>
                <?php
                foreach ($slides AS $row) {
                    ?>
                    <li id="records_<?php echo make_safe($row['id']); ?>" class="category-li" title="Drag To Re-Order">
                        <div class="row">
                            <div class="col-3"><?php echo make_safe($row['title']); ?></div>
                            <div class="col-9 text-right">
                                <a class="action-edit" href="slider.php?case=edit&id=<?php echo make_safe($row['id']); ?>" data-toggle="tooltip" data-placement="top" title="Edit">Edit</a>
                                <a class="action-remove" href="javascript:deleteSlide(<?php echo make_safe($row['id']); ?>);" data-toggle="tooltip" data-placement="top" title="Delete">Delete</a>
                            </div>
                        </div>
                    </li>
                    <?php
                }
                ?>
        </ul>
        </div>
        <?php } ?>
    </div>
</div>
<?php }
include('footer.php');
?>