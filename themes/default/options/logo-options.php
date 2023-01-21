<?php 
// prevent direct access
if (!isset($options)) {
die('You Can not Access Directly');	
}
?>
<div class="page-container">
		<div class="page-header page-heading">
            <h4>Logo& FavIcon Options</h4>
        </div>
  <form role="form" method="POST" action="" enctype="multipart/form-data">
  <div class="form">
  <?php if (isset($message)) {echo normalize_input($message);} ?>
      <div class="form-group">
          <label for="logo">Site Logo  <span class="help">PNG, JPEG Image.</span></label>
          <div class="custom-file">
              <input type="file" class="custom-file-input" name="logo" id="logo">
              <label class="custom-file-label" for="thumbnail">Choose file</label>
          </div>
          <p class="help">
          <a href="javascript:void();" data-toggle="popover" data-placement="top" title="Current Logo" data-content="<img src='../upload/<?php echo make_safe($options['logo']); ?>' class='img-responsive' />">Current Logo</a>
          </p>
      </div>
      <div class="form-group">
          <label for="favicon">Site FavIcon <span class="help">16x16 PNG Image</span></label>
          <div class="custom-file">
              <input type="file" class="custom-file-input" name="favicon" id="favicon">
              <label class="custom-file-label" for="thumbnail">Choose file</label>
          </div>
          <p class="help">
              <a href="javascript:void();" data-toggle="popover" data-placement="top" title="Current Favicon" data-content="<img src='../upload/<?php echo make_safe($options['favicon']); ?>' class='img-responsive' />">Current FavIcon</a>
          </p>
      </div>
      <input type="hidden" name="old_logo" value="<?php echo make_safe($options['logo']); ?>" />
      <input type="hidden" name="old_favicon" value="<?php echo make_safe($options['favicon']); ?>" />
      <div class="form-actions">
          <button type="submit" name="save" class="btn btn-dark">Save</button>
      </div>
  </div>
  </form>
</div>

		  