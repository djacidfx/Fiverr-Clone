(function($) { 
    "use strict"; 
        $(document).ready(function() {

        $('#image_input').filer({
            limit: 10,
            maxSize: 2,
            extensions: ['jpeg','png','jpg','gif', 'webp'],
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
        });   	       
        $("#filer_input2").filer({
            limit: 1,
            maxSize: 1,
            extensions: ['jpeg','png','jpg','webp'],
            changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop Image here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Images</a></div></div>',
            showThumbs: true,
            theme: "dragdropbox",
            templates: {
                box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
                item: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li>{{fi-progressBar}}</li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
                itemAppend: '<li class="jFiler-item">\
                                <div class="jFiler-item-container">\
                                    <div class="jFiler-item-inner">\
                                        <div class="jFiler-item-thumb">\
                                            <div class="jFiler-item-status"></div>\
                                            <div class="jFiler-item-info">\
                                                <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                                <span class="jFiler-item-others">{{fi-size2}}</span>\
                                            </div>\
                                            {{fi-image}}\
                                        </div>\
                                        <div class="jFiler-item-assets jFiler-row">\
                                            <ul class="list-inline pull-left">\
                                                <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                            </ul>\
                                            <ul class="list-inline pull-right">\
                                                <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                            </ul>\
                                        </div>\
                                    </div>\
                                </div>\
                            </li>',
                progressBar: '<div class="bar"></div>',
                itemAppendToEnd: false,
                removeConfirmation: true,
                _selectors: {
                    list: '.jFiler-items-list',
                    item: '.jFiler-item',
                    progressBar: '.bar',
                    remove: '.jFiler-item-trash-action'
                }
            },
            dragDrop: {
                dragEnter: null,
                dragLeave: null,
                drop: null,
            },
            uploadFile: {
                url: "./ajax.php?case=upload_image",
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
            files: null,
            addMore: false,
            clipBoardPaste: true,
            excludeName: null,
            beforeRender: null,
            afterRender: null,
            beforeShow: null,
            beforeSelect: null,
            onSelect: null,
            afterShow: null,
            onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
                var file = file.name;
                $.post('./ajax.php?case=remove_image', {file: file});
            },
            onEmpty: null,
            options: null,
            captions: {
                button: "Choose Image",
                feedback: "Choose image To Upload",
                feedback2: "image was chosen",
                drop: "Drop image here to Upload",
                removeConfirmation: "Are you sure you want to remove this image ?",
                errors: {
                    filesLimit: "Only {{fi-limit}} image are allowed to be uploaded.",
                    filesType: "Only Images are allowed to be uploaded.",
                    filesSize: "{{fi-name}} is too large! Please upload image up to {{fi-maxSize}} MB.",
                    filesSizeAll: "Images you've choosed are too large! Please upload images up to {{fi-maxSize}} MB."
                }
            }
        });
        
        $('.upload-files').filer({
            limit: 10,
            maxSize: 10,
            extensions: ['jpeg','png','jpg','gif','pdf','zip','doc','docx','webp'],
            showThumbs: true,
            uploadFile: {
                url: "ajax.php?case=upload_attachments",
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
                $.post('ajax.php?case=remove_attachment', {file: file});
            },
            captions: {
                button: "Upload",
                feedback: "Choose Files To Upload",
                feedback2: "file was chosen"
            }
        });

    });
})(jQuery); 
