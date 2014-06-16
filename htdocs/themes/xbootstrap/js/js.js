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

jQuery(document).ready(function($) {
    $('.carousel').carousel({
        interval:   5000,
        pause:      "hover",
        wrap:       true
  })
});