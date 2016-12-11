/*
* Styleswitch stylesheet switcher built on jQuery
* Under an Attribution, Share Alike License
* By Kelvin Luck ( http://www.kelvinluck.com/ )
*
* * * * * *
* WARNING *
* * * * * *
* Note by tititou : this script differ by one line from
* the original in order to deal with the several css
* files included in the oxygen GUI file.
*
*     if (this.getAttribute('title') == 'Style sheet') this.disabled = false;
*
* See http://sourceforge.net/tracker/?func=detail&aid=2839949&group_id=41586&atid=430840
* for a description of the issue.
*
* So please be careful is this script is updated ;-)
*/

(function($)
{
    $(document).ready(function() {
        $('.styleswitch').click(function()
        {
            switchStylestyle(this.getAttribute("rel"));
            return false;
        });
        var c = readCookie('xoadmstyle');
        if (c) switchStylestyle(c);

        // Close error messages
        if($(".errorMsg").length > 0){
            setTimeout(function(){
                $(".errorMsg").slideUp(500, function(){
                    $(this).remove();
                });
            }, 7000);
        }
    });

    function switchStylestyle(styleName)
    {
        $('link[rel*=style][title]').each(function(i)
        {
            this.disabled = true;
            if (this.getAttribute('title') == 'Style sheet') this.disabled = false;
            if (this.getAttribute('title') == styleName) this.disabled = false;

            if(styleName == 'orange') {
                $("#xoops-logo").attr('src', tplUrl + '/images/logo-xoops-w.png');
            } else if(styleName == 'silver'){
                    $("#xoops-logo").attr('src', tplUrl + '/images/logo-xoops-nb.png');
            } else {
                $("#xoops-logo").attr('src', tplUrl + '/images/logo-xoops.png');
            }

        });
        createCookie('xoadmstyle', styleName, 365);
    }
})(jQuery);
// cookie functions http://www.quirksmode.org/js/cookies.html
function createCookie(name,value,days)
{
    if (days)
    {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name)
{
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++)
    {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function eraseCookie(name)
{
    createCookie(name,"",-1);
}
// /cookie functions
