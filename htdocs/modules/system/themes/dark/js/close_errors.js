/*
 * Close errors shown on control panel entry after some time
 */

(function($)
{
    $(document).ready(function() {
        // Close error messages
        if($(".errorMsg").length > 0){
            setTimeout(function(){
                $(".errorMsg").slideUp(500, function(){
                    $(this).remove();
                    $('#xo-content br').not($('#xoopsorgnews br')).remove();
                });
            }, 7000);
        }
    });
})(jQuery);
