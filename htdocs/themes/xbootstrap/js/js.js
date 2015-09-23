// JavaScript Document
if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement("style")
    msViewportStyle.appendChild(
        document.createTextNode(
            "@-ms-viewport{width:auto!important}"
        )
    )
    document.getElementsByTagName("head")[0].appendChild(msViewportStyle)
}

jQuery(document).ready(function ($) {
    $('.carousel').carousel({
        interval: 5000,
        pause: "hover",
        wrap: true
    })
});

jQuery(document).ready(function ($) {
    var $container = $('#xoopsgrid').masonry();
    $container.imagesLoaded(function () {
        $container.masonry();
    });
});

/* Newbb */
jQuery(document).ready(function ($) {
    /* Bootstrap Style: Horizontal Form */
    $(".xoopsform").find('form').addClass("form-inline");
    $(".xoopsform").find('select').addClass("form-control");
    $(".xoopsform").find('input[type="submit"]').addClass("btn btn-primary");
    $(".newbb-links").find('span').removeClass('forum_icon forum_button');
});

jQuery(document).ready(function ($) {
    $('.newbb-thread-attachment').find('br').remove();
    $('.newbb-thread-attachment').find('hr').remove();
})
