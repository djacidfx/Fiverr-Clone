$(document).ready(function() {
    $('.upload-files').filer({
        limit: 10,
        maxSize: 10,
        extensions: ['jpeg','png','jpg','gif','pdf','zip','doc','docx'],
        showThumbs: true,
        uploadFile: {
            url: "./ajax.php?case=upload_images",
            data: null,
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){

                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
                });
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: null
        },
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            var file = file.name;
            $.post('./ajax.php?case=remove_image', {file: file});
        },
        captions: {
            button: "<i class='icon icon-cloud-upload'></i>",
            feedback: "Choose Files To Upload",
            feedback2: "file was chosen"
        }
    });

});

