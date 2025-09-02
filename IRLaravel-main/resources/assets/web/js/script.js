$(document).ready(function () {
    var owlCarouselHome = $('.owl-carousel-home');
    
    owlCarouselHome.owlCarousel({
        lazyLoad:true,
        loop: true,
        margin: 40,
        nav: false,
        stagePadding: 40,
        dots: false,
        responsive: {
            0: {
                items: 2,
            },
            600: {
                items: 3,
            },
            1000: {
                items: 4,
            },
        },
    });

    owlCarouselHome.on('mousewheel', '.owl-stage', function (e) {
        if (e.originalEvent.deltaY > 0) {
            owlCarouselHome.trigger('next.owl');
        } else {
            owlCarouselHome.trigger('prev.owl');
        }
        e.preventDefault();
    });

    if( $(".owl-search-category").length > 0 ){
        var owlSearchCategoryScript = $(".owl-search-category").owlCarousel({
            loop: false,
            nav: true,
            navText : ["<i class='icon-chevron-left'></i>","<i class='icon-chevron-right'></i>"],
            dots: false,
            startPosition: $('.active-category').index($('.current')) > 0 ? $('.active-category').index($('.current')) : 0,
            autoWidth:true,
            slideBy: 4,
            onInitialize: function(){
                $(".header-search .form-category").removeClass('display-none');
            }
        });
    }

    if( $(".owl-search-type").length > 0 ){
        var owlSearchType = $(".owl-search-type").owlCarousel({
            loop: false,
            nav: true,
            navText : ["<i class='icon-chevron-left'></i>","<i class='icon-chevron-right'></i>"],
            dots: false,
            autoWidth:true,
            slideBy: 4,
            onInitialize: function () {
                $(".type-zaak .wrap-search").removeClass('display-none');
            }
        });
    }

    $(".hp-slider .wp-image").owlCarousel({
        lazyLoad:true,
        loop: true,
        nav: false,
        dots: false,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 1,
            },
            1000: {
                items: 1,
            },
        },
    });

    MainWeb.fn.initTimeslotCarousel.call(this);

    if ($(".validate-slider .item").length > 1) {
        $(".validate-slider").owlCarousel({
            loop: true,
            autoplay: true,
            autoplayTimeout:3000,
            nav: false,
            dots: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 1,
                },
                1000: {
                    items: 1,
                },
            },
            onInitialize: function(){
                $(".validate-slider").removeClass('display-none');
            }
        });
        
    }else{
        $(".validate-slider").removeClass('display-none');
    }

    $(".owl-carousel-step").owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: false,
        navText: [
            '<i class="icn-small-prev"></i>',
            '<i class="icn-small-next"></i>',
        ],
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 1,
            },
            1000: {
                items: 4,
            },
        },
    });

    $("body").on("input", ".username,.password, .password_confirmation", function () {
        if ($(this).val() != "") {
            $(this).next().addClass("active");
        } else {
            $(this).next().removeClass("active");
        }
        if ($(".username").val() != "" && $(".password").val() != "") {
            $(".btn-login").removeClass("btn-disable");
            $(".btn-login").prop("disabled", false);
        } else {
            $(".btn-login").addClass("btn-disable");
            $(".btn-login").prop("disabled", true);
        }
    });

    /*$("#form-login .btn-login").click(function () {
        var error_free = true;
        var form_data = $("#form-login").serializeArray();
        $("#form-login").addClass("invalid");
        return false;
    });*/

    // Validate Google address input
    $("#form-register .form-line, #form-register .form-line-modal").on("change", '.latitude, .longitude', function () {
        // Check location in Google Address
        var txtLocation = $(this);
        var locationContainer = txtLocation.closest('.use-maps');
        var locationError = locationContainer.find('.error');
        var txtLat = locationContainer.find(':input.latitude');
        var txtLng = locationContainer.find(':input.longitude');

        if ((txtLocation.val().trim() != '') && (txtLat.val().trim() == '' || txtLng.val().trim() == '')) {
            if (locationError.length == 0) {
                // Create new error element
                locationError = $('<span class="error">&nbsp;</span>');
                locationContainer.append(locationError);
            }

            locationError.text(Lang.get("register.validation.address.location")).show();
        } else {
            locationError.remove();
        }
    });

    $("#form-register .form-line, #form-register .form-line-modal").on("change", '.location', function () {
        // Check location in Google Address
        var txtLocation = $(this);
        var locationContainer = txtLocation.closest('.use-maps');
        var locationError = locationContainer.find('.error');

        if (locationError.length == 0) {
            // Create new error element
            locationError = $('<div class="error">&nbsp;</div>');
            locationContainer.append(locationError);
        }

        locationError.text(Lang.get("register.validation.address.location")).show();
        // $("#form-register .btn-modal-register, #form-register .btn-register").prop('disabled', true);
    });

    /*$("#form-register .form-line input").on("input", function () {
        var invalid = false;
        $("#form-register .form-line input").each(function (i, val) {
            if ($(this).val() == "" && !$(this).hasClass('address-street-number')) {
                invalid = true;
                return;
            }
        });

        // Agree Terms & Conditions
        var termAndCondition = $("#form-register .form-line #checkbox-1").prop('checked');

        if (!termAndCondition) {
            invalid = true;
        }

        if (invalid == true) {
            $("#form-register .btn-register").prop("disabled", true);
        } else {
            $("#form-register .btn-register").prop("disabled", false);
        }
    });*/

    /*$("#form-register .btn-register").click(function () {
        if (!$("#form-register").hasClass("invalid")) {
            $("#form-register").addClass("invalid");
            $("#form-register").find(".form-line").removeClass("invalid-input");
        } else {
            $("#form-register").removeClass("invalid");
            $("#form-register").find(".form-line").removeClass("invalid-input");
        }
        return false;
    });*/

    $(document).on('click', '.check-content-collapse', function(){
        $(this).parent().parent().children(":first-child").toggleClass("active");
        $(this)
            .find("i")
            .toggleClass("icon-chevron-down")
            .toggleClass("icon-chevron-up");
        return false;
    });

    $(".tab-click").click(function () {
        $(".tab-click").not($(this)).removeClass("active");
        $(this).addClass("active");
        $(".tab-content").removeClass("active");
        var id = $(this).data("id");
        $(".tab-content").each(function (e) {
            if (id == $(this).data("id")) {
                $(".tab-content").not($(this)).slideUp();
                $(this).slideDown();
                $(this).toggleClass("active");
            }
        });
        if ($(".page-match-detail").length > 0) {
            return false;
        }
    });

    $(".click-next-step").click(function () {
        var step = $(this).data("step");

        if (step != 5) {
            $(".wrap-step").removeClass("active");
            if (step == 4) {
                $(".bottom-content .form-row ").addClass("none");
            }
            step++;
            $(this).find(".current-step").text(step);
            $(this).data("step", step);
            $(".back-step").data("step", step);
            $(".wrap-step").each(function (e) {
                if (step == $(this).data("id")) {
                    $(".wrap-step").not($(this)).slideUp();
                    $(this).slideDown();
                    $(this).toggleClass("active");
                }
            });
            $(".wrap-sidebar-step li").removeClass("active").removeClass("checked");
            $(".wrap-sidebar-step li").each(function (e) {
                if (step > $(this).data("id")) {
                    $(this).addClass("checked");
                } else if (step == $(this).data("id")) {
                    $(this).addClass("active");
                }
            });
        }

        return false;
    });

    $(".back-step").click(function () {
        var step = $(this).data("step");

        if (step != 1) {
            $(".wrap-step").removeClass("active");
            if (step <= 4) {
                $(".bottom-content .form-row ").removeClass("none");
            }
            step--;
            //$(this).find('.current-step').text(step);
            $(this).data("step", step);
            $(".click-next-step").find(".current-step").text(step);
            $(".click-next-step").data("step", step);
            $(".wrap-step").each(function (e) {
                if (step == $(this).data("id")) {
                    $(".wrap-step").not($(this)).slideUp();
                    $(this).slideDown();
                    $(this).toggleClass("active");
                }
            });
            $(".wrap-sidebar-step li").removeClass("active").removeClass("checked");
            $(".wrap-sidebar-step li").each(function (e) {
                if (step > $(this).data("id")) {
                    $(this).addClass("checked");
                } else if (step == $(this).data("id")) {
                    $(this).addClass("active");
                }
            });
        }

        return false;
    });

    $(".add-event").click(function () {
        $(".event-form").slideDown();
        return false;
    });

    $(".edit-event").click(function () {
        $(".event-form").slideDown();
        return false;
    });

    $(".close-event").click(function () {
        $(".event-form").slideUp();
        return false;
    });

    $(".enable-content").click(function () {
        if ($(this).find("input:checked").length > 0) {
            $(".disable-is-switch-false").slideDown();
        } else {
            $(".disable-is-switch-false").slideUp();
        }
    });
    
    $(document).on('click', '.selection', function (e) {
        $(this).toggleClass("active");
    });
    
    $(document).on('click', '.order-sub-menu li a', function (e) {
        $('.actived').removeClass('actived');
        $('.selection').find("span").html($(this).text());
        $(this).addClass('actived');
    });


    $(window).trigger("resize");

    $(".go-back").on("click", function () {
        $(this)
            .parent("ul")
            .addClass("is-hidden")
            .parent(".has-children")
            .parent("ul")
            .removeClass("moves-out");
    });

    $(".remove-checked").on("click", function () {
        //$('.wrap-check-remove ')
        $(".wrap-check-remove input[type=radio]").prop("checked", false);
        return false;
    });

    if ($(".show-date-time").length > 0 || $(".show-date").length > 0) {
        $(".show-date-time").datetimepicker({
            formatTime: "H:i",
            formatDate: "d/m/Y",
            format: "d/m/Y H:i",
            dayOfWeekStart: 1,
            onSelectDate: function () {
                $(".xdsoft_datepicker").removeClass("active");
                $(".xdsoft_timepicker").addClass("enable");
            },
            onSelectTime: function () {
                $(".xdsoft_datepicker").addClass("active");
                $(".xdsoft_timepicker").removeClass("enable");
            },
        });

        // Show date picker
        var $now = new Date();
        var $year = $now.getFullYear();

        // Get all input with want to init date picker
        $(".show-date").map(function () {
            MainWeb.fn.initDateTimeForFutureElem.call(this, $(this));
        });
    }

    var state = 0;
    var maxcol = Math.ceil(
        $(".header-search .search-category").width() /
        ($(".header-search .search-category li").width() + 30)
    );
    var maxState = Math.ceil(
        $(".header-search .search-category li").length / maxcol
    );
    var winWidth = $(".header-search .search-category").width();
    $("#lefty").click(function () {
        // if (state == 0) {
        //     state = maxState;
        // } else {
        //     state--;
        // }
        // $(".header-search .search-category").animate(
        //     { scrollLeft: winWidth * 0.7 * state + "px" },
        //     800
        // );
        // return false;
        if( $(".header-search .search-category li").first().position().left < ($(".header-search .search-category").width() + $(".header-search .search-category").position().left) ){
            state--;
            $(".header-search .search-category").animate(
                { scrollLeft: winWidth * state + "px" },
                800
            );
        }
    });

    $("#righty").click(function () {
        
        // if (state == maxState) {
        //     state = 0;
        // } else {
        //     state++;
        // }

        if( $(".header-search .search-category li").last().position().left >= ($(".header-search .search-category").width() + $(".header-search .search-category").position().left) ){
            state++;
            $(".header-search .search-category").animate(
                { scrollLeft: winWidth * state + "px" },
                800
            );
        }

        
        return false;
    });

    $(".minute-content .plus").on("click", function () {
        //var num = $('.minute-content .wrap-input input').val() != "" ? $('.minute-content .wrap-input input').val(): 0 ;
        var num =
            $(this).parent().find("input").val() != ""
                ? $(this).parent().find("input").val()
                : 1;
        num++;
        $(this).parent().find("input").val(num);

        MainWeb.fn.calculationFinalPriceCart.call(this);

        MainWeb.fn.showHideFeeShip.call(this);

        updateQuantity(this);

        return false;
    });

    $(".minute-content .minus").on("click", function () {
        //var num = $('.minute-content .wrap-input input').val() != "" ? $('.minute-content .wrap-input input').val(): 0 ;
        var num =
            $(this).parent().find("input").val() != ""
                ? $(this).parent().find("input").val()
                : 1;
        num--;
        if (num < 1) {
            num = 1;
        }
        $(this).parent().find("input").val(num);

        MainWeb.fn.calculationFinalPriceCart.call(this);

        MainWeb.fn.showHideFeeShip.call(this);

        updateQuantity(this);

        return false;
    });

    $(".search-text").click(function () {
        if ($(this).parent().hasClass('show-input')) {
            $("#remove-input").trigger('click');
            return false;
        }
        
        $(".owl-search-category").addClass("disable");
        $("#remove-input").toggleClass('active');
        $(this).parent().toggleClass("show-input");
        return false;
    });

    // $(".header-search .search-action .wrap-action input")
    //     .focus(function () {
    //         $(".header-search .search-action .wrap-action .remove-input").addClass(
    //             "active"
    //         );
    //     })
    //     .focusout(function () {
    //         $(".search-category").removeClass("disable");
    //         $(".header-search .search-action .wrap-action").removeClass("show-input");
    //         $(".header-search .search-action .wrap-action .remove-input").removeClass(
    //             "active"
    //         );
    //     });

    if ($(".xdsoft_time_variant").length) {
    }

    // SonTT disabled it
    // Because we should use in blade to include config from Laravel PHP
    // googleMapLocations();

    $(".modelProfileUser .button-upload-avatar").click(function () {
        var form = $(this).closest('form');
        form.find('input[name=deleteAvatar]').removeAttr('disabled');
        $(".show-img img").attr('src', $('.show-img').data('image'));
    });

    $(".eye-color").click(function () {
        var target = $(this).data("target");
        $("#" + target).addClass("active");
        return false;
    });

    $(document).on('click', '.pop-up .wrap-popup-text .close, .pop-up .wrap-popup-order .close, .pop-up .wrap-popup-text .btn-bottom', function(){
        $('.pop-up').removeClass('active');
        $('.user-modal').addClass('hidden');
        return false;
    });
    
    $(document).on('click', '.user-modal .wrap-popup-text .close', function () {
        $(this).closest('.user-modal').addClass('hidden');
        return false;
    });

    $('.user-modal .bg').on('click',function(e){
        $(this).parent().addClass('hidden');
        e.preventDefault();
    });

    $('.pop-up .bg').on('click',function(e){
        $(this).parent().removeClass('active');
        e.preventDefault();
    });

    /*$(".pop-up>.row").click(function () {
        $(".pop-up").removeClass("active");
        return false;
    });*/

    $(document).on('click', '.wrap-sidebar-time .item .sub-item label', function(){
        if (!$(this).hasClass("disable")) {
            $(this).closest('.sub-item').find('input[name="settingTimeslot"]').trigger("click");
        }

        return false;
    });

    $(".wrap-table-header .tab-index h6").click(function () {
        $(".wrap-table-header .tab-index h6").removeClass('color');
        var id = $(this).data('tab');
        $(this).addClass('color');
        $('.wrap-table-time .content-tab').removeClass('active');
        $('.wrap-table-time .content-tab').each(function (index, value) {
            if( $(value).data('tab') == id ){
                $(this).addClass('active');
                return;
            }
        })
        return false;
    });

    $(window).scroll(function(){
        $(".wrap-meal .wrap-item-content .wrap-info").removeClass("active");
    });
    
    $('body').bind('touchmove', function(e) { 
        $(".wrap-meal .wrap-item-content .wrap-info").removeClass("active");
    });
    
    if (window.innerWidth < 768) {
        $('.wrap-meal .wrap-item-content h6>i').on('click',function(e){
            if( $(this).parent().parent().find('.wrap-info').length > 0 ){
                $(this).parent().parent().find('.wrap-info').toggleClass('active');
                y = e.clientY;
                $(this).parent().parent().find(".wrap-info").css({ top: y });
            }
        });
    }else{
        $(".item-meal .wrap-contentx .icn-information-o")
        .mouseover(function (e) {
            if (!$(this).parent().parent().find(".wrap-info").hasClass("active")) {
                $(this).parent().parent().find(".wrap-info").addClass("active");
                x = e.pageX - $(this).parent().offset().left + 55;
                y = e.pageY - $(this).parent().offset().top + 35;
                $(this).parent().parent().find(".wrap-info").css({ left: x, top: y });
            }
        })
        .mouseout(function (e) {
            $(this).parent().parent().find(".wrap-info").removeClass("active");
        });

    $(".item-meal .wrap-contentx .wrap-info")
        .mouseenter(function () {
            $(this).addClass("active");
        })
        .mouseleave(function () {
            $(this).removeClass("active");
        });
    }

    $(document).on('click', '.form-line .show-pass, .ip-has-icon .show-pass, .form-line-modal .show-pass, body .show-pass', function () {
        if($(this).hasClass('active')){
            if( $(this).prev().attr('type') == 'password' ){
                $(this).prev().attr('type', 'text');
            }else{
                $(this).prev().attr('type', 'password');
            }
            
            $(this).find('.svg-icon').toggleClass('hidden');
            return false;
        }
    });

    //Mote to main.js
    // $('.item-meal .favorite i').click(function () {
    //     $(this).toggleClass('icon-heart').toggleClass('icon-heart-o');
    // });

    $(window).trigger('resize');

    //Siderbar menu of mobile
    sidebarMenu();

    $(document).on('input', '.clearable', function(){
        $(this)[tog(this.value)]('x');
    }).on('mousemove', '.x', function( e ){
    $(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]('onX');   
        }).on('click', '.onX', function(){
    $(this).removeClass('x onX').val('').change();
    });

});
function tog(v){
    return v?'addClass':'removeClass';
}
function googleMapLocations() {
    
    if (
        window.hasOwnProperty("mapLocationsJson") &&
        document.getElementById("map") !== null
    ) {
        loadLocationsGoogleMapsScript();
    }
}

function loadLocationsGoogleMapsScript() {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src =
        "https://maps.googleapis.com/maps/api/js?key=AIzaSyBNMkmsT3usqqf0gxqPNy0yAcrzM_KRBPo&language=en&v=3&" +
        "callback=initializeLocationsGoogleMaps";
    document.body.appendChild(script);
}

function initializeLocationsGoogleMaps() {
    
    var googleMap = new google.maps.Map(document.getElementById("map"), {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableAutoPan: false,
        navigationControl: true,
        mapTypeControl: false,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
        },
        disableDefaultUI: true,
        gestureHandling: "cooperative"
    });
    var bounds = new google.maps.LatLngBounds();
    var geocoder = new google.maps.Geocoder();
    var infowindow = new google.maps.InfoWindow();
    var pinIcon = new google.maps.MarkerImage();

    for (i = 0; i < window.mapLocationsJson.length; i++) {
        var location = window.mapLocationsJson[i];
        var latlng = new google.maps.LatLng(location.latitude, location.longitude);
        //pinIcon.url = location.marker;
        var mapMarker = new google.maps.Marker({
            position: latlng,
            map: googleMap,
            //icon: pinIcon
        });
        bounds.extend(mapMarker.position);
        googleMap.fitBounds(bounds);
        googleMap.panToBounds(bounds);
        google.maps.event.addListener(
            mapMarker,
            "click",
            (function (mapMarker, location) {
                return function () {
                    infowindow.setContent(location.description);
                    infowindow.open(googleMap, mapMarker);
                };
            })(mapMarker, location)
        );
    }

    var listener = google.maps.event.addListener(googleMap, "idle", function () {
        if (googleMap.getZoom() > 14) googleMap.setZoom(14);
        google.maps.event.removeListener(listener);
    });
}

$(window).on("resize", function () {
    if (window.innerWidth > 767) {
        if ($(".grid-meal").length) {
            var h = $(".grid-meal .item-meal .wrap-item-content").innerHeight();
            var highestBox = 0;
            $(".grid-meal .item-meal .wrap-item-content").each(function () {
                if ($(this).height() > highestBox) {
                    highestBox = $(this).height();
                }
            });
            //var padd = h - highestBox;
            $(".grid-meal .item-meal .wrap-item-content").height(highestBox);
        }
    }

    if (window.innerWidth > 1024) {
        if ($(".equal-height-event").length) {
            var highestBox = 0;
            $(".equal-height-event .event").each(function () {
                if (
                    $(this).find(".wrap-image").height() >
                    $(this).find(".wrap-content").innerHeight()
                ) {
                    highestBox = $(this).find(".wrap-image").height();
                    $(this).find(".wrap-content").innerHeight(highestBox);
                } else {
                    highestBox = $(this).find(".wrap-content").innerHeight();
                    $(this).find(".wrap-image").height(highestBox);
                }
            });
        }
    }
});

if ($('.web-portal .main-content').length) {
    var stickyOffset = $('.web-portal .main-content').offset().top;
    
    $(window).scroll(function(){
        var sticky = $('.page-search'),
          scroll = $(window).scrollTop();
    
        if (scroll >= stickyOffset) {
            sticky.addClass('sticky');
        } else {
            sticky.removeClass('sticky');
        }
    });
}

function sidebarMenu () {
    var slide_wrp 		= ".m-side-menu-wrapper"; //Menu Wrapper
    var open_button 	= ".m-menu-open"; //Menu Open Button
    var close_button 	= ".menu-close"; //Menu Close Button
    var overlay 		= ".menu-overlay"; //Overlay

    $(slide_wrp).hide().css( {"left": -$(slide_wrp).outerWidth()+'px'}).delay(50).queue(function(){$(slide_wrp).show()}); 

    $(open_button).click(function(e){
        e.preventDefault();
        $(slide_wrp).css( {"left": "0px"});
        setTimeout(function(){
            $(slide_wrp).addClass('active');
        },50);
        $(overlay).css({"opacity":"1", "width":"100%"});
    });

    $(close_button).click(function(e){
        e.preventDefault();
        $(slide_wrp).css( {"left": -$(slide_wrp).outerWidth()+'px'});
        setTimeout(function(){
            $(slide_wrp).removeClass('active');
        },50);
        $(overlay).css({"opacity":"0", "width":"0"});
    });

    $('.menu-overlay').on('click', function(e) {
            $(slide_wrp).css( {"left": -$(slide_wrp).outerWidth()+'px'}).removeClass('active');
            $(overlay).css({"opacity":"0", "width":"0"});
    });
    //Show menu login
    $('.btn-login .nav-item').on('click', function() {
        $('.btn-login .sub-menu').toggleClass("show-m-menu");
        $('.m-side-logo #m-notification-list').removeClass('show-m-menu');
        $('.m-side-logo .sub-menu-time').removeClass('show-m-menu');
    });
    //Show menu time
    $('.m-side-logo .menu-time .icn-time').on('click', function() {
        $('.m-side-logo #m-notification-list').removeClass('show-m-menu');
        $('.btn-login .sub-menu').removeClass("show-m-menu");
        $('.m-side-logo .sub-menu-time').toggleClass("show-m-menu");
    });
    //Show menu email
    $('.m-side-logo .menu-email').on('click', function() {
        $('.m-side-logo .sub-menu-time').removeClass('show-m-menu');
        $('.btn-login .sub-menu').removeClass("show-m-menu");
        $('.m-side-logo #m-notification-list').toggleClass("show-m-menu");
    });

    $(document).on('click', function (e) {
        var container = $('#menu-time, #menu-email, .pop-up');
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            $('.m-side-logo .sub-menu-time').removeClass('show-m-menu');
            $('.m-side-logo #m-notification-list').removeClass('show-m-menu');
        }
    });

    // Setup jQuery
    $.ajaxSetup({
        headers: {
            'Content-Language': $('html').attr('lang')
        }
    });
}

function updateQuantity (e) {
    var route = $(e).data('route'),
        cartId = $('input[name="cart_id"]').val(),
        cartItemId = $(e).data('cart-item-id'),
        totalNumber = $('input[name="cartItem['+cartItemId+'][total_number]"]').val(),
        currentRequest = null;
        
    currentRequest = $.ajax({
        type: 'POST',
        url: route,
        datatype: "json",
        data: {
            cartId: cartId,
            cartItemId: cartItemId,
            totalNumber: totalNumber
        },
        success: function (response) {
            return true;
        },
        error: function (response) {
            console.log(response);
        }
    });
}