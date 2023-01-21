(function($) { 
    "use strict"; 
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        adminBarSpace();
        $(window).resize(function () {
            adminBarSpace();
        });
        function adminBarSpace() {
            var header = $('.header').outerHeight();
            $('body').css('padding-top', header+'px');
        }

        $('.service-images').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false,
            arrows: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        dots:true,
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        arrows: false,
                        dots:true,
                    }
                }
            ]
        });


        var width = $('.g-recaptcha').parent().width();
        if (width < 302) {
            var scale = width / 302;
            $('.g-recaptcha').css('transform', 'scale(' + scale + ')');
            $('.g-recaptcha').css('-webkit-transform', 'scale(' + scale + ')');
            $('.g-recaptcha').css('transform-origin', '0 0');
            $('.g-recaptcha').css('-webkit-transform-origin', '0 0');
        }

        var autoplaySpeed = 7000;
        $('.slider').slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            dots:false,
            fade:true,
            cssEase:'linear',
            autoplay: true,
            autoplaySpeed: autoplaySpeed,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        dots:true,
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        arrows: false,
                        dots:true,
                    }
                }
            ]
        });

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            $('.rrssb-whatsapp').show();
            $('.rrssb-linkedin').show();
        } else {
            $('.rrssb-whatsapp').hide();
            $('.rrssb-linkedin').hide();
        }



        check_header_position();
        $(window).scroll(function () {
            check_header_position();
        });

    
        $("#signup_btn").on('click',function(e) {
            e.preventDefault();
            $("#signup_btn").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=register',
                data: $('#signup-form').serialize(),
                cache: false,
                success: function(result) {
                    setTimeout(
                        function()
                        {
                            $("#signup_btn span").remove();
                            $("div#signup-message").html(result);
                            grecaptcha.reset();
                        }, 2000);
                }
            });
            return false;
        });
    



        $("#change-password-btn").on('click',function(e) {
            e.preventDefault();
            $("#change-password-btn").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=change_password',
                data: $('#change-password-form').serialize(),
                cache: false,
                success: function(result) {
                    setTimeout(
                        function()
                        {
                            $("#change-password-btn span").remove();
                            $("div#change-password-message").html(result);
                            document.getElementById("change-password-form").reset();
                        }, 2000);
                }
            });
            return false;
        });



  
        $("#forget-password-btn").on('click',function(e) {
            e.preventDefault();
            $("#forget-password-btn").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=forget_password',
                data: $('#forget-password-form').serialize(),
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    setTimeout(
                        function()
                        {
                            $("#forget-password-btn span").remove();
                            $("div#forget-password-message").html(obj.message);
                            if (obj.code === 1) {
                                document.getElementById("forget-password-form").reset();
                                grecaptcha.reset();
                            }
                        }, 2000);
                }
            });
            return false;
        });


        $("#reset-password-btn").on('click',function(e) {
            e.preventDefault();
            $("#reset-password-btn").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=reset_password',
                data: $('#reset-password-form').serialize(),
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    setTimeout(
                        function()
                        {
                            $("#reset-password-btn span").remove();
                            $("div#reset-password-message").html(obj.message);
                            if (obj.code === 1) {
                                document.getElementById("reset-password-form").reset();
                            }
                        }, 2000);
                }
            });
            return false;
        });



        $("#contact-form-btn").on('click',function(e) {
            e.preventDefault();
            $("#contact-form-btn").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=contact',
                data: $('#contact-form').serialize(),
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    setTimeout(
                        function()
                        {
                            $("#contact-form-btn span").remove();
                            $("div#contact-form-message").html(obj.message);
                            if (obj.code === 1) {
                                document.getElementById("contact-form").reset();
                                grecaptcha.reset();
                            }
                        }, 2000);
                }
            });
            return false;
        });



        $("#service-inquiries-btn").on('click',function(e) {
            e.preventDefault();
            $("#service-inquiries-btn").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=service_inquiries',
                data: $('#service-inquiries-form').serialize(),
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    setTimeout(
                        function()
                        {
                            $("#service-inquiries-btn span").remove();
                            $("div#service-inquiries-message").html(obj.message);
                            if (obj.code === 1) {
                                document.getElementById("service-inquiries-form").reset();
                                grecaptcha.reset();
                            }
                        }, 2000);
                }
            });
            return false;
        });


    
        $("#login-btn").on('click',function() {
            var username = $("input#login-username").val();
            var password = $("input#login-password").val();
            var remember = $("input#remember").val();
            var currentpage = $("input[name='currentpage']").val();

            var dataString = 'username='+ username + '&password=' + password +'&remember='+remember+'&currentpage='+currentpage;
            $("#login-btn").append(' <span class="sp-circle"></span>');
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=login',
                data: dataString,
                success: function(result) {
                    if (result == 1) {
                        setTimeout(
                            function()
                            {
                                document.location.href = currentpage;
                            }, 2000);
                    } else {
                        $("#login-btn span").remove();
                        $("div#login-message").html(result);
                    }
                }
            });
            return false;
        });
    
        $("#logout-btn").on('click',function() {
            $.ajax({
                type: "POST",
                url: 'ajax.php?case=logout',
                data: 'logout=1',
                success: function(result) {
                    if (result == 1) {
                        document.location.href = './';
                    } else {
                        alert('Error Happened');
                    }
                }
            });
        });


        $(".offline-payment-btn").on('click',function(e) {
            e.preventDefault();
            $(this).append(' <span class="sp-circle"></span>');
            var service_id = $(this).data('service-id');
            var customer_id = $(this).data('customer-id');
            var dataString = 'service_id='+ service_id + '&customer_id=' + customer_id;

            $.ajax({
                type: "POST",
                url: 'ajax.php?case=offline_payment',
                data: dataString,
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    if(obj.code == 1) {
                        setTimeout(
                            function()
                            {
                                $(".offline-payment-btn").remove();
                                document.location.href = './dashboard/orders/order/'+obj.order_id;
                            }, 2000);
                    } else {
                        $(".offline-payment-btn").remove();
                        document.location.href = './';
                    }

                }
            });
            return false;
        });

        $("#message-submit").on('click',function(e) {
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
    });
})(jQuery); 



function check_header_position() {
    var headerOffset = $('.header').offset().top;
    if (headerOffset > 100) {
        $('.header').addClass('shadow-header');
    } else {
        $('.header').removeClass('shadow-header');
    }
}

function checkLength(i) {
    if (i.textLength > 0) {
        jQuery('#message-submit').prop('disabled',false);
    } else {
        jQuery('#message-submit').prop('disabled',true);
    }
}