<!doctype html>
<html lang="<{$xoops_langcode}>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>">
    <meta http-equiv="content-language" content="<{$xoops_langcode}>">
    <title><{$xoops_sitename}> <{$lang_imgmanager}></title>
    <{$image_form.javascript}>
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl xoops.css}>">
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl modules/system/css/imagemanager.css}>">
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl media/font-awesome/css/font-awesome.min.css}>">

    <{php}>
        $language = $GLOBALS['xoopsConfig']['language'];
        if(file_exists(XOOPS_ROOT_PATH.'/language/'.$language.'/style.css')){
        echo "
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"language/$language/style.css\"/>
        ";
        }
    <{/php}>
    <!-- fine-upload -->
    <!-- Fine Uploader New/Modern CSS file
    ====================================================================== -->
    <link href="<{xoAppUrl media/fine-uploader/fine-uploader-new.css}>" rel="stylesheet">
    <link href="<{xoAppUrl media/fine-uploader/ManuallyTriggerUploads.css}>" rel="stylesheet">

    <!-- Fine Uploader JS file
    ====================================================================== -->
    <script src="<{$xoops_url}>/media/fine-uploader/fine-uploader.js"></script>
    <{includeq file="db:system_trigger_uploads.tpl"}>
    <!-- fine-upload -->
</head>

<body onload="window.resizeTo(<{$xsize|default:800}>, <{$ysize|default:572}>);">
<table cellspacing="0" id="imagenav">
    <tr>
        <td id="addimage" class="txtleft"><a href="<{$xoops_url}>/imagemanager.php?target=<{$target}>&amp;cat_id=<{$show_cat}>"
                                             title="<{$lang_imgmanager}>"><{$lang_imgmanager}></a></td>
    </tr>
</table>
<h2><{$imgcat_name}></h2>
<div id="fine-uploader-manual-trigger"></div>
<div><{$smarty.const._IMGMAXSIZE}> <{$imgcat_maxsize}></div>
<div><{$smarty.const._IMGMAXWIDTH}> <{$imgcat_maxwidth}></div>
<div><{$smarty.const._IMGMAXHEIGHT}> <{$imgcat_maxheight}></div>

<div id="footer">
    <input value="<{$lang_close}>" type="button" onclick="window.close();"/>
</div>
<!-- Your code to create an instance of Fine Uploader and bind to the DOM/template
====================================================================== -->
<script>
    var manualUploader = new qq.FineUploader({
        element: document.getElementById('fine-uploader-manual-trigger'),
        template: 'qq-template-manual-trigger',
        request: {
            endpoint: '<{$xoops_url}>/ajaxfineupload.php',
            customHeaders: {
                "Authorization": "Basic <{$jwt}>"
            }
        },
        text: {
            formatProgress: "<{$smarty.const._FORMATPROGRESS}>",
            failUpload: "<{$smarty.const._FAILUPLOAD}>",
            waitingForResponse: "<{$smarty.const._WAITINGFORRESPONSE}>",
            paused: "<{$smarty.const._PAUSED}>"
        },
        messages: {
            typeError: "<{$smarty.const._TYPEERROR}>",
            sizeError: "<{$smarty.const._SIZEERROR}>",
            minSizeError: "<{$smarty.const._MINSIZEERROR}>",
            emptyError: "<{$smarty.const._EMPTYERROR}>",
            noFilesError: "<{$smarty.const._NOFILESERROR}>",
            tooManyItemsError: "<{$smarty.const._TOOMANYITEMSERROR}>",
            maxHeightImageError: "<{$smarty.const._MAXHEIGHTIMAGEERROR}>",
            maxWidthImageError: "<{$smarty.const._MAXWIDTHIMAGEERROR}>",
            minHeightImageError: "<{$smarty.const._MINHEIGHTIMAGEERROR}>",
            minWidthImageError: "<{$smarty.const.__MINWIDTHIMAGEERROR}>",
            retryFailTooManyItems: "<{$smarty.const._RETRYFAILTOOMANYITEMS}>",
            onLeave: "<{$smarty.const._ONLEAVE}>",
            unsupportedBrowserIos8Safari: "<{$smarty.const._UNSUPPORTEDBROWSERIOS8SAFARI}>"
        },
        thumbnails: {
            placeholders: {
                waitingPath: '<{$xoops_url}>/media/fine-uploader/placeholders/waiting-generic.png',
                notAvailablePath: '<{$xoops_url}>/media/fine-uploader/placeholders/not_available-generic.png'
            }
        },
        validation: {
            acceptFiles: ['image/jpeg', 'image/gif', 'image/png'],
            allowedExtensions: ['jpeg', 'jpg', 'png', 'gif'],
            image: {
                maxHeight: <{$imgcat_maxheight}>,
                maxWidth: <{$imgcat_maxwidth}>
            },
            sizeLimit: <{$imgcat_maxsize}>,
            itemLimit: <{$imgcat_itemlimit|default:2}>
        },
        autoUpload: false,
        callbacks: {
            onError: function(id, name, errorReason, xhrOrXdr) {
                console.log(qq.format("Error uploading {}.  Reason: {}", name, errorReason));
            }
        },
        debug: <{$fineup_debug}>
    });

    qq(document.getElementById("trigger-upload")).attach("click", function() {
        manualUploader.uploadStoredFiles();
    });
</script>

</body>
</html>
