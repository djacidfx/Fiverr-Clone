<?php
include('header.php');
?>
    <div class="big-title">
        <h1>System Updates</h1>
        <div class="actions">
            <a href="<?php echo help_links('updates'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
        </div>
    </div>
    <div class="page-container">
    <div class="page-header">
        <h4>Upload Update File</h4>
    </div>

    <div class="form">
        <div class="alert alert-warning">
            Make sure that you have a backup of your files and database, the updated files will override the existing files.
        </div>
        <form id="uploadImage" action="ajax.php" method="post">
            <input type="hidden" name="action" value="update" />
            <div class="form-group">
                <label>File</label>
                <div class="custom-file">
                    <input type="file" class="form-control" name="uploadFile" id="uploadFile" accept=".zip" />
                    <label class="custom-file-label" for="uploadFile">Choose file</label>
                </div>
            </div>

            <div class="progress">
                <div class="progress-bar progress-bar-success progress-bar-striped progress-bar-animated active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="form-group">
                <button type="submit" id="uploadSubmit" class="btn btn-dark">Upload</button>
            </div>
        </form>
    </div>
    </div>
<?php

include('footer.php');
?>