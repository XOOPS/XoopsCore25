// JavaScript Document

/* Scroll Top */

jQuery(function ($) {
    $.scrollUp({
        scrollName: 'scrollUp', // Element ID
        topDistance: '300', // Distance from top before showing element (px)
        topSpeed: 300, // Speed back to top (ms)
        animation: 'fade', // Fade, slide, none
        animationInSpeed: 200, // Animation in speed (ms)
        animationOutSpeed: 200, // Animation out speed (ms)
        scrollText: 'Scroll to top', // Text for element
        activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
//      activeOverlay:'#00FFFF', // Set CSS color to display scrollUp active point, e.g '#00FFFF'
        scrollImg: true            // Set true to use image
    });
});

/* Bootstrap Carousel */
jQuery(document).ready(function($) {
    $('.carousel').carousel({
        interval:   5000,
        pause:      "hover",
        wrap:       true
  })
});

/* Masonry Grid */
jQuery(document).ready(function($){
    var $container = $('#xoopsgrid').masonry();
    $container.imagesLoaded( function() {
      $container.masonry();
    });
});

/* Newbb */
jQuery(document).ready(function($){
    /* Bootstrap Style: Horizontal Form */
    $(".xoopsform").find('form').addClass("form-inline");
    $(".xoopsform").find('select').addClass("form-control");
    $(".xoopsform").find('input[type="submit"]').addClass("btn btn-primary");
    $(".newbb-links").find('span').removeClass('forum_icon forum_button');
    $('.newbb-thread-attachment').find('br').remove();
    $('.newbb-thread-attachment').find('hr').remove();
});

