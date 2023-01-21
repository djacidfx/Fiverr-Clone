<?php
include('header.php');
?>
    <div class="big-title">
        <h1>Slider</h1>
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
	<div class="page-header">
        <div class="row">
            <div class="col-9"><h1>Add slide</h1></div>
            <div class="col-3">
				<a href="slider.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>
	<?php if (isset($message)) {echo normalize_input($message);} ?>
 <div class="form">
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
		  <button type="submit" name="submit" class="btn btn-primary">Save</button>
		</form>
 </div>
<?php
break;
case 'edit';
$id = abs(intval(make_safe(xss_clean($_GET['id']))));
if (isset($_POST['submit'])) {
$message = $slider->edit_slide($_POST,$id);
}
$slide = $slider->slide($id);
?>
	<div class="page-header">
        <div class="row">
            <div class="col-9"><h1>Edit slide</h1></div>
            <div class="col-3">
				<a href="slider.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>
	<?php if (isset($message)) {echo normalize_input($message);} ?>
 <div class="form">
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
                 <p class="help">Current Image : <a href="javascript:void();" data-toggle="popover" data-placement="top" title="Current Image" data-content="<img src='../upload/slider/<?php echo normalize_input($slide['slide_image']); ?>' class='img-responsive' />"><?php echo normalize_input($slide['slide_image']); ?></a></p>
             <?php } ?>
         </div>
         <div class="form-group">
             <label for="slide_title">Slide Title</label>
             <input type="text" class="form-control" name="slide_title" id="slide_title" value="<?php echo make_safe($slide['slide_title']); ?>" />
         </div>
         <div class="form-group">
             <label for="slide_text">Slide Text</label>
             <textarea class="form-control" name="slide_text" id="slide_text" rows="4"><?php echo make_safe($slide['slide_text']); ?></textarea>
         </div>
         <input type="hidden" name="slide_image" value="<?php echo make_safe($slide['slide_image']); ?>" />
         <button type="submit" name="submit" class="btn btn-primary">Save</button>
     </form>
 </div>
<?php
break;
default;
?>
    <div class="page-header">
        <div class="row">
            <div class="col-9"><h1>All Slides</h1></div>
            <div class="col-3">
                <a href="slider.php?case=add" class="btn btn-success btn-sm float-right"><i class="ri-add-line"></i></a>
            </div>
        </div>
    </div>
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
        <?php
    }
} 
include('footer.php');
?>