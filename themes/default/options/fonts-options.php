<?php 
// prevent direct access
if (!isset($options)) {
die('You Can not Access Directly');	
}
$google_api_key = "AIzaSyAjBXFgI_btxg_s9vI_h87r4tz42JCGwz0";
$fonts_list = google_fonts($google_api_key);

?>
<div class="page-container">
<div class="page-header page-heading">
    <h4>Fonts Options</h4>
</div>
<form role="form" method="POST" action="">
  <div class="form">
  <?php if (isset($message)) {echo normalize_input($message);} ?>
      <div class="form-group">
          <div class="row">
              <div class="col-md-12">
                  <label for="heading_font">Heading Font</label>
                  <select name="heading_font" id="heading_font" class="form-control">
                      <?php
                      foreach($fonts_list['items'] AS $font) {
                          ?>
                          <option value="<?php echo str_replace(' ','+', $font['family']); ?>" <?php if (isset($options['heading_font']) AND $options['heading_font'] == str_replace(' ','+', $font['family'])) {echo 'SELECTED';} ?>><?php echo $font['family']; ?></option>
                          <?php
                      }
                      ?>
                  </select>
              </div>
          </div>
          <p class="help">Preview</p>
          <p class="example-header"></p>

      </div>
      <div class="form-group">
          <div class="row">
              <div class="col-md-12">
                  <label for="paragraph_font">Paragraph Font</label>
                  <select name="paragraph_font" id="paragraph_font" class="form-control">
                      <?php
                      foreach($fonts_list['items'] AS $font) {
                          ?>
                          <option value="<?php echo str_replace(' ','+', $font['family']); ?>" <?php if (isset($options['paragraph_font']) AND $options['paragraph_font'] == str_replace(' ','+', $font['family'])) {echo 'SELECTED';} ?>><?php echo $font['family']; ?></option>
                          <?php
                      }
                      ?>
                  </select>
              </div>
          </div>
          <p class="help">Preview</p>
          <p class="example-paragraph"></p>
      </div>

      <div class="form-actions">
          <button type="submit" name="save" class="btn btn-dark">Save</button>
      </div>
  </div>
</form>
</div>

