/*
 * Tooltip script
 * powered by jQuery (http://www.jquery.com)
 *
 * written by Alen Grakalic (http://cssglobe.com)
 *
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 *
 */

/**
 * This section originates from https://github.com/janl/mustache.js under The MIT License
 *
 * Copyright (c) 2009 Chris Wanstrath (Ruby)
 * Copyright (c) 2010-2014 Jan Lehnardt (JavaScript)
 * Copyright (c) 2010-2015 The mustache.js community
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
var entityMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
    '/': '&#x2F;',
    '`': '&#x60;',
    '=': '&#x3D;'
};

function escapeHtml (string) {
    return String(string).replace(/[&<>"'`=\/]/g, function (s) {
        return entityMap[s];
    });
}
/* End of mustache.js code */

this.tooltip = function(){
    /* CONFIG */
    yOffset = 20;

    /* END CONFIG */
    $(".tooltip").hover(function(e){
        this.t = this.title;
        this.title = "";

        //Removing alt atribute for IE
        $("a.tooltip img").each(function() { $(this).attr("title", ""); $(this).attr("alt", ""); });

        $("body").append("<p id='tooltip'>"+ escapeHtml(this.t) +"</p>");

        $("#tooltip")
            .css("top",(e.pageY + yOffset) + "px")
            .css("left",(e.pageX - ($('#tooltip').width() / 2)) + "px")
            .fadeIn("fast");
    }, function(){
        this.title = this.t;
        $("#tooltip").remove();
    });

    //$("a.tooltip img").hover(function(e){
       //$(this).attr("title", "");
       //$(this).attr("alt", "");
    //});

    $("a.tooltip").mousemove(function(e){

       xOffset = - ($('#tooltip').width() / 2);
       scrollBarWidth = 15; //padding from right side allways
       windowWidth = $(window).width() - scrollBarWidth;

       if (e.pageX + xOffset <= 0 ) {
            xOffset = - e.pageX;
        }
        if (e.pageX + xOffset + $('#tooltip').width() >= windowWidth) {
            xOffset = windowWidth - e.pageX - $('#tooltip').width();
        }

        $("#tooltip")
            .css("top",(e.pageY + yOffset) + "px")
            .css("left",(e.pageX + xOffset) + "px");
    });
};

// starting the script on page load
$(document).ready(function(){
    tooltip();
});
