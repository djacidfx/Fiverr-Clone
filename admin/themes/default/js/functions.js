(function($) { 
    "use strict";


    $(document).ready(function() {
        $('.scrollbar').perfectScrollbar();
        $('#smtp_div').hide();
        $('#mail_div').hide();
        $('#sendgrid_div').hide();
        $('.paypal-wrapper').hide();
        $('.stripe-wrapper').hide();
        $('.offline-wrapper').hide();
        $('.razorpay-wrapper').hide();
        $('.paypal-live').hide();
        $('.paypal-sandbox').hide();
        $('.stripe-live').hide();
        $('.stripe-test').hide();
        $('.razorpay-live').hide();
        $('.razorpay-test').hide();
        $('.digital-download-div').hide();
        $('#facebook-login').hide();
        $('#twitter-login').hide();
        $('#envato-login').hide();


        $(".ajax-options-form button").on('click',function(e) {
            e.preventDefault();
            $(this).append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=save_options',
                data: $('.ajax-options-form').serialize(),
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    if (obj.code == 0) {
                        $(".ajax-options-form button span").remove();
                        $(".ajax-form-response").html(obj.message);
                    } else {
                        setTimeout(function() {
                            $(".ajax-options-form button span").remove();
                            notice(obj.message);
                        }, 2000);
                    }

                }
            });
            return false;
        });


        $('#mobileMenu').on('click', function(e){
            e.preventDefault();
            $('body').toggleClass('sidebar-show');
        });

        // two level menu
        $('.nav-sidebar .nav-item').on('click', '.nav-link', function(e){
            if($(this).hasClass('with-sub')) {
                e.preventDefault();
                $(this).parent().toggleClass('show');
                $(this).parent().siblings().removeClass('show');
            } else {
                $(this).parent().addClass('active').siblings().removeClass('active');
                $(this).parent().siblings().find('.sub-link').removeClass('active');
            }

            var ss = $(this).closest('.nav-sidebar').siblings('.nav-sidebar');
            var sg = $(this).closest('.nav-group').siblings('.nav-group');

            ss.find('.active').removeClass('active');
            ss.find('.show').removeClass('show');

            sg.find('.active').removeClass('active');
            sg.find('.show').removeClass('show');

            $('.scrollbar').perfectScrollbar('update');
        });



        $('.nav-group-label').on('click', function(){
            $(this).closest('.nav-group').toggleClass('show');
            $(this).closest('.nav-group').siblings().removeClass('show');

            $('.scrollbar').perfectScrollbar('update');
        });

        var heading_font = $("select[name='heading_font'] option:selected").val();
        $.get("ajax.php?case=change_heading_font", { "heading_font": heading_font},
            function(data) {
                $("p.example-header").html(data);
            });
        $("select[name='heading_font'],select[name='heading_font_size'],select[name='heading_font_weight']").change(function() {
            var heading_font = $("select[name='heading_font'] option:selected").val();
            $.get("ajax.php?case=change_heading_font", { "heading_font": heading_font },
                function(data) {
                    $("p.example-header").html(data);
                });
        });

        var body_font = $("select[name='paragraph_font'] option:selected").val();
        $.get("ajax.php?case=change_paragraph_font", { "body_font": body_font},
            function(data) {
                $("p.example-paragraph").html(data);
            });
        $("select[name='paragraph_font'],select[name='paragraph_font_size'],select[name='paragraph_font_weight']").change(function() {
            var body_font = $("select[name='paragraph_font'] option:selected").val();
            $.get("ajax.php?case=change_paragraph_font", { "body_font": body_font },
                function(data) {
                    $("p.example-paragraph").html(data);
                });
        });
    

        var IsSMTP = $('input[value="smtp"]:checked').length;
        if (IsSMTP > 0)
            $('#smtp_div').show();
        else
            $('#smtp_div').hide();

        var IsMail = $('input[value="mail"]:checked').length;
        if (IsMail > 0)
            $('#mail_div').show();
        else
            $('#mail_div').hide();

        var IsSendGrid = $('input[value="sendgrid"]:checked').length;
        if (IsSendGrid > 0)
            $('#sendgrid_div').show();
        else
            $('#sendgrid_div').hide();


        var IsPayPal = $('input[name="allow_paypal"]:checked').length;
        if (IsPayPal > 0) {
            $('.paypal-wrapper').show();
        } else {
            $('.paypal-wrapper').hide();
        }

        var IsStripe = $('input[name="allow_stripe"]:checked').length;
        if (IsStripe > 0) {
            $('.stripe-wrapper').show();
        } else {
            $('.stripe-wrapper').hide();
        }

        var IsManual = $('input[name="allow_offline"]:checked').length;
        if (IsManual > 0) {
            $('.offline-wrapper').show();
        } else {
            $('.offline-wrapper').hide();
        }

        var IsRazorPay = $('input[name="allow_razorpay"]:checked').length;
        if (IsRazorPay > 0) {
            $('.razorpay-wrapper').show();
        } else {
            $('.razorpay-wrapper').hide();
        }


        var IsPayPalSandBox = $('input[name="paypal_sandbox"]:checked').length;
        if (IsPayPalSandBox > 0) {
            $('.paypal-sandbox').show();
            $('.paypal-live').hide();
        } else {
            $('.paypal-live').show();
            $('.paypal-sandbox').hide();
        }

        var IsStripeTest = $('input[name="stripe_test_mode"]:checked').length;
        if (IsStripeTest > 0) {
            $('.stripe-test').show();
            $('.stripe-live').hide();
        } else {
            $('.stripe-live').show();
            $('.stripe-test').hide();
        }

        var IsRazorPayTest = $('input[name="razorpay_test_mode"]:checked').length;
        if (IsRazorPayTest > 0) {
            $('.razorpay-test').show();
            $('.razorpay-live').hide();
        } else {
            $('.razorpay-live').show();
            $('.razorpay-test').hide();
        }

        var IsDigitalDownload = $('input[name="digital_download"]:checked').length;
        if (IsDigitalDownload > 0) {
            $('.digital-download-div').show();
        } else {
            $('.digital-download-div').hide();
        }


        var allowFacebookLogin = $('input[name="enable_facebook_login"]:checked').length;
        if (allowFacebookLogin > 0) {
            $('#facebook-login').show();
        } else {
            $('#facebook-login').hide();
        }

        var allowTwitterLogin = $('input[name="enable_twitter_login"]:checked').length;
        if (allowTwitterLogin > 0) {
            $('#twitter-login').show();
        } else {
            $('#twitter-login').hide();
        }

        var allowEnvatoLogin = $('input[name="enable_envato_login"]:checked').length;
        if (allowEnvatoLogin > 0) {
            $('#envato-login').show();
        } else {
            $('#envato-login').hide();
        }

        $('#allow_paypal').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.paypal-wrapper').show();
            } else {
                $('.paypal-wrapper').hide();
            }
        });

        $('#allow_stripe').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.stripe-wrapper').show();
            } else {
                $('.stripe-wrapper').hide();
            }
        });

        $('#allow_offline').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.offline-wrapper').show();
            } else {
                $('.offline-wrapper').hide();
            }
        });

        $('#allow_razorpay').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.razorpay-wrapper').show();
            } else {
                $('.razorpay-wrapper').hide();
            }
        });

        $('#paypal_sandbox').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.paypal-sandbox').show();
                $('.paypal-live').hide();
            } else {
                $('.paypal-live').show();
                $('.paypal-sandbox').hide();
            }
        });

        $('#stripe_test_mode').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.stripe-test').show();
                $('.stripe-live').hide();
            } else {
                $('.stripe-live').show();
                $('.stripe-test').hide();
            }
        });

        $('#razorpay_test_mode').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.razorpay-test').show();
                $('.razorpay-live').hide();
            } else {
                $('.razorpay-live').show();
                $('.razorpay-test').hide();
            }
        });


        $('#digital_download').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('.digital-download-div').show();
            } else {
                $('.digital-download-div').hide();
            }
        });

        $('#enable_facebook_login').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('#facebook-login').show();
            } else {
                $('#facebook-login').hide();
            }
        });

        $('#enable_twitter_login').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('#twitter-login').show();
            } else {
                $('#twitter-login').hide();
            }
        });

        $('#enable_envato_login').on('change',function() {
            if ($(this).prop("checked") == true) {
                $('#envato-login').show();
            } else {
                $('#envato-login').hide();
            }
        });

        $(function() {
            $(".sort-categories ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
                    var order = $(this).sortable("serialize") + '&action=sort_categories';
                    $.post("ajax.php", order, function(theResponse){});
                }
            });
        });

        $("#restore-default").on('click', function(e) {
            e.preventDefault();
            var template_id = $(this).data('template-id');
            var data = 'template_id='+template_id;

            Swal({
                title: 'Restore Defaults',
                text: "all your changes will be overrode",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Restore'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        type: "POST",
                        url: 'ajax.php?case=restore_default_template',
                        data: data,
                        cache: false,
                        success: function (result) {
                            document.location.href = 'email_templates.php?case=edit&id=' + template_id;
                        }
                    });
                    return false;
                }
            });
        });

        $("#message-submit").on('click', function(e) {
            e.preventDefault();
            $("#message-submit").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=submit_message',
                data: $('#message-form').serialize(),
                cache: false,
                success: function(result) {
                    if (result == 0) {
                        $("div#ajax-result-message").html(result);
                    } else {
                        setTimeout(
                            function()
                            {
                                $("#message-submit span").remove();
                                document.getElementById("message-form").reset();
                                $('.upload-files').trigger("filer.reset");
                                $('#message-submit').prop('disabled',true);
                                $(".messages").append(result);
                            }, 2000);
                    }
                }
            });
            return false;
        });

        $("#reply-submit").on('click',function(e) {
            e.preventDefault();
            $("#reply-submit").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=submit_reply',
                data: $('#reply-form').serialize(),
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    if (obj.code == 0) {
                        $("div#ajax-result-reply").html(obj.message);
                    } else {
                        setTimeout(
                            function()
                            {
                                $("#reply-submit span").remove();
                                $("div#ajax-result-reply").html(obj.message);
                                document.getElementById("reply-form").reset();
                            }, 2000);
                    }

                }
            });
            return false;
        });

        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover({html: true});

        $('.toggle-content-nav').on('click',function() {
            $('.setting-nav').removeClass('visible');
            $('.content-nav').toggleClass('visible');
        });
        $('.toggle-setting-nav').on('click',function() {
            $('.content-nav').removeClass('visible');
            $('.setting-nav').toggleClass('visible');
        });
        $('.close-nav').on('click',function() {
            $('.content-nav').removeClass('visible');
            $('.setting-nav').removeClass('visible');
        });
    
            $(".sort-pages ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
                    var order = $(this).sortable("serialize") + '&action=sort_pages';
                    $.post("ajax.php", order, function(theResponse){

                    });
                }
            });

            $(".sort-slides ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
                    var order = $(this).sortable("serialize") + '&action=sort_slides';
                    $.post("ajax.php", order, function(theResponse){

                    });
                }
            });

        $(".delete-service-image").on('click',function() {

            var id = $(this).data("image-id");
            var dataString = 'id='+ id +'&action=delete_service_image';
            Swal({
                title: 'Delete',
                text: "Proceed To Delete Image ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "ajax.php",
                        data: dataString,
                        dataType: "html",
                        cache: false,
                        success: function()
                        {
                            $('#image-'+id).fadeOut('slow');
                        }
                    });
                }
            });
        });
        $('.progress').hide();
        $('#uploadImage').submit(function(event){
            if($('#uploadFile').val())
            {
                event.preventDefault();
                $('#uploadSubmit').html("<i class='sp sp-circle'></i>");
                $(this).ajaxSubmit({
                    beforeSubmit:function(){
                        $('.progress').show();
                    },
                    uploadProgress: function(event, position, total, percentageComplete)
                    {
                        $('.progress-bar').animate({
                            width: percentageComplete + '%'
                        }, {
                            duration: 1000
                        });

                    },
                    success:function(result){
                        $('#uploadSubmit').html('Upload');
                        console.log(result);
                    },
                    resetForm: true
                });
            }
            return false;
        });

    });
})(jQuery); 

function ConfirmLogOut() {
    new jBox('Confirm', {
        confirmButton: 'Logout ?',
        cancelButton: 'Stay'
    });

}

function deleteService(id) {
    var dataString = 'id='+ id +'&action=delete_service';
    Swal({
        title: 'Delete',
        text: "Proceed To Delete Service ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: dataString,
                dataType: "html",
                cache: false,
                success: function()
                {
                    document.location.href = 'services.php';
                }
            });
        }
    });
}

function deleteCustomer(id) {
    var dataString = 'id='+ id +'&action=delete_customer';
    Swal({
        title: 'Delete Customer',
        text: "Deleting customer will lead to deleting everything related to this customer, like orders, messages .. etc.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: dataString,
                dataType: "html",
                cache: false,
                success: function()
                {
                    document.location.href = 'customers.php';
                }
            });
        }
    });
}

function ShowDiv(DivID) {
    if (DivID == 'smtp_div') {
        $('#smtp_div').show();
        $('#mail_div').hide();
        $('#sendgrid_div').hide();
    }  else if (DivID == 'mail_div')  {
        $('#mail_div').show();
        $('#smtp_div').hide();
        $('#sendgrid_div').hide();
    } else {
        $('#mail_div').hide();
        $('#smtp_div').hide();
        $('#sendgrid_div').show();
    }
}

function ShowComments(DivID) {
    if (DivID == 'disqus_div') {
        $('#disqus_div').show();
        $('#facebook_div').hide();
    }  else  {
        $('#disqus_div').hide();
        $('#facebook_div').show();
    }
}


function ShowNlDiv(DivID) {
    if (DivID == 'feedburner') {
        $('#feedburner').show();
        $('#custom_code').hide();
    }  else  {
        $('#feedburner').hide();
        $('#custom_code').show();
    }
}


function changePage(newLoc)
{
    nextPage = newLoc.options[newLoc.selectedIndex].value

    if (nextPage != "")
    {
        document.location.href = nextPage
    }
}


function checkLength(i) {
    if (i.textLength > 0) {
        jQuery('#message-submit').prop('disabled',false);
    } else {
        jQuery('#message-submit').prop('disabled',true);
    }
}

function ConfirmLogOut() {


    Swal({
        title: 'Logout',
        text: "do you want to end your session?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout!'
    }).then((result) => {
        if (result.value) {
            document.location.href = 'logout.php';
        }
    });


}

function startOrder(id) {
    var dataString = 'id='+ id +'&action=start_order';
    Swal({
        title: 'Start Order',
        text: "The buyer will be notified that order started",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Start!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: dataString,
                dataType: "html",
                cache: false,
                success: function()
                {
                    document.location.href = 'orders.php?case=details&id='+id;
                }
            });
        }
    });
}

function cancelOrder(id) {
    var dataString = 'id='+ id +'&action=cancel_order';
    Swal({
        title: 'Delete Order',
        text: "The order will be deleted and you will need to refund the buyer",
        type: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Delete Order'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: dataString,
                dataType: "html",
                cache: false,
                success: function()
                {
                    document.location.href = 'orders.php?case=details&id='+id;
                }
            });
        }
    });
}

function completeOrder(id) {
    var dataString = 'id='+ id +'&action=complete_order';
    Swal({
        title: 'Complete Order',
        text: "The order will be assigned as completed, buyer will be notified.",
        type: 'success',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Complete Order'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: dataString,
                dataType: "html",
                cache: false,
                success: function()
                {
                    document.location.href = 'orders.php?case=details&id='+id;
                }
            });
        }
    });
}

function deletePage(id) {
    var dataString = 'id='+ id +'&action=delete_page';
    Swal({
        title: 'Delete Page',
        text: "are you sure you want to delete this page ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: dataString,
                dataType: "html",
                cache: false,
                success: function()
                {
                    document.location.href = 'pages.php';
                }
            });
        }
    });
}

function deleteSlide(id) {
    var dataString = 'id='+ id +'&action=delete_slide';
    Swal({
        title: 'Delete Slide',
        text: "are you sure you want to delete this slide ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: dataString,
                dataType: "html",
                cache: false,
                success: function()
                {
                    document.location.href = 'slider.php';
                }
            });
        }
    });
}


function notice(text) {
    new jBox('Notice', {
        content: text,
        color: 'black',
        autoClose: 5000
    });
}