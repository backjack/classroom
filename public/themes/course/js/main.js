/*********************************************************************

    Template Name: Zapp - App Landing HTML Template   
    Template URI: https://themeforest.net/user/nilartstudio
    Description: This is html5 multipurpose app landing template.
    Author: nilArtStudio
    Author URI: http://nscreativestudio.com
    Version: 1.0

    Note: Style css

*********************************************************************/

/*************************************************************************
                            INDEX
*************************************************************************/
/*
    
    00. Sticky Menu
    01. Fixed Footer
    02. Wow js
    03. scrollUp jquery active
    04. screenshot slider
    05. testimonial slider
    06. Accordion
    07. Scroll to section
    08. Youtube video trigger 
    09. Mean menu
    10. Menu trigger 
    11. Youtube background 
    12. Button click animation 
    13. client Carousel


*/
/*********************************************************************/





(function($) {
    'use strict';

    /*-------------------------------------------
    	00. Sticky Header
    --------------------------------------------- */
    var stickyMenu = $('.header-area').clone().appendTo('#sticky-header');
    $(window).on('scroll', function() {
        var w = $(window).width();
        if (w > 991) {
            if ($(this).scrollTop() > 850) {
                $('#sticky-header').slideDown(300);
            } else {
                $('#sticky-header').slideUp(500);
            }
        }
    });
    
    /*-------------------------------------------
    	01. Fixed Footer
    --------------------------------------------- */
/*     var footerHeight = $("#footer").height();
    var footerWidth = $(window).width();
    if(footerWidth < 767){
        $('.footer--fixed #page-section').css({
            marginBottom: 0
        });
    }
    else {
        $('.footer--fixed #page-section').css({
            marginBottom: footerHeight
        });
    }
     */
    /*-------------------------------------------
    	02. Wow js
    --------------------------------------------- */
	new WOW().init();

    /*-------------------------------------------
    	03. scrollUp jquery active
    --------------------------------------------- */
	$.scrollUp({
	    scrollText: '<i class="fa fa-angle-up"></i>',
	    easingType: 'linear',
	    scrollSpeed: 900,
	    animation: 'fade'
	});
    
    /*-------------------------------------------
        04. screenshot slider 
    --------------------------------------------- */
    $('.screenshot').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        infinite: true,
        centerMode: true,
        centerPadding: '0px',
        autoplay: false,
        arrows: true,
        dots: false,
        autoplaySpeed: 5000,
        prevArrow:'<span class="slick-arrow left"><i class="fa fa-angle-left"></i></span>',
        nextArrow:'<span class="slick-arrow right"><i class="fa fa-angle-right"></i></span>',
        responsive: [
            {
            breakpoint: 991,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });


    /*-------------------------------------------
    	05. testimonial slider 
    --------------------------------------------- */
    $('.testimonial').owlCarousel({
        center: true,
        items: 1,
        smartSpeed: 800,
        autoplay: true,
        autoplayTimeout: 3000,
        loop: true,
        margin: 0,
        nav: false,
        dots: false,
        animateOut: 'slideOutDown',
        animateIn: 'bounceIn',
        thumbs: true,
        thumbsPrerendered: true
    });  
     

    /*-------------------------------------------
        06. Accordion 
    --------------------------------------------- */
    $('.accordion').accordion();

    /*-------------------------------------------
        07. Scroll to section 
    --------------------------------------------- */
    $('.mainmenu').onePageNav({
        currentClass: 'current',
        changeHash: false,
        scrollSpeed: 750,
        scrollThreshold: 0.5,
        filter: '',
        easing: 'swing'
    });

    // Another scroll to section menu
    $('#fullnav').onePageNav({
        currentClass: 'current',
        changeHash: false,
        scrollSpeed: 750,
        scrollThreshold: 0.5,
        filter: '',
        easing: 'swing'
    });



    /*-------------------------------------------
        08. Youtube video trigger 
    --------------------------------------------- */
    $('.video__trigger').yu2fvl();



/*-------------------------------------------
    09. Mean menu 
--------------------------------------------- */
    $('header nav').meanmenu({
        meanMenuClose: 'X',
        meanMenuCloseSize: '18px',
        meanScreenWidth: '991',
        meanExpandableChildren: true,
        meanMenuContainer: '.mobile-menu'
    });


/*-------------------------------------------
    10. Menu trigger 
--------------------------------------------- */
    $('button.menu__trigger').on('click', function(event){
        event.preventDefault();
        $('.f-menu').slideDown(500);
    });
    $('button.f-menu__close').on('click', function(event){
        event.preventDefault();
        $('.f-menu').fadeOut(500);
    });
    $('.f-menu__inner .menu li a').on('click', function(event){
        $('.f-menu').fadeOut(300);
    });



/*-------------------------------------------
    11. Youtube background
--------------------------------------------- */
    $('.youtube-bg').YTPlayer();


/*-------------------------------------------
    12. Button click animation
--------------------------------------------- */
    var ink, d, x, y;
    $('.button').on('click', function(e){
    if($(this).find('.ink').length === 0){
        $(this).prepend('<span class="ink"></span>');
    }
         
    ink = $(this).find('.ink');
    ink.removeClass('button-animate');
     
    if(!ink.height() && !ink.width()){
        d = Math.max($(this).outerWidth(), $(this).outerHeight());
        ink.css({height: d, width: d});
    }
     
    x = e.pageX - $(this).offset().left - ink.width()/2;
    y = e.pageY - $(this).offset().top - ink.height()/2;
     
    ink.css({top: y+'px', left: x+'px'}).addClass('button-animate');


    return true;
    });

/* ---------------------------------------------
    13. client Carousel
 --------------------------------------------- */
    var item = 4;
    $('#client-carousel').owlCarousel({
        center: false,
        items: item,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        singleItem: false,
        loop: true,
        margin: 30,
        nav: false,
        dots: true,
        responsive: {
            280: {
                items: 1
            },
            400: {
                items: 2
            },
            500: {
                items: 2
            },
            768: {
                items: 2
            },
            800: {
                items: 3
            },
            1000: {
                items: 4
            },
            1200: {
                items: 4
            },
            1400: {
                items: 4
            }
        }
    });  

})(jQuery);