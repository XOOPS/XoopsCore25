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

    <!-- Fine Uploader JS file
    ====================================================================== -->
    <script src="<{$xoops_url}>/media/fine-uploader/fine-uploader.js"></script>

    <!-- Fine Uploader Thumbnails template w/ customization
    ====================================================================== -->
    <script type="text/template" id="qq-template-manual-trigger">
        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="<{$smarty.const._DROPFILESHERE}>">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="buttons">
                <div class="qq-upload-button-selector qq-upload-button">
                    <div><{$smarty.const._SELECTFILES}></div>
                </div>
                <button type="button" id="trigger-upload" class="btn btn-primary" title="Upload">
                    <span class="fa fa-upload"></span> <{$smarty.const._UPLOAD}>
                </button>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span><{$smarty.const._PROCESSINGDROPPEDFILES}></span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <div class="qq-progress-bar-container-selector">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                    <span class="qq-upload-file-selector qq-upload-file"></span>
                    <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel"><{$smarty.const._CANCEL}></button>
                    <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry"><{$smarty.const._RETRY}></button>
                    <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete"><{$smarty.const._DELETE}></button>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector"><{$smarty.const._CLOSE}></button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector"><{$smarty.const._NO}></button>
                    <button type="button" class="qq-ok-button-selector"><{$smarty.const._YES}></button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector"><{$smarty.const._CANCEL}></button>
                    <button type="button" class="qq-ok-button-selector"><{$smarty.const._OK}></button>
                </div>
            </dialog>
        </div>
    </script>

    <style>
        #trigger-upload {
            color: white;
            background-color: #00ABC7;
            font-size: 12px;
            padding: 9px 20px;
            background-image: none;
            border: 0px;
            border-radius: 2px;
        }

        #fine-uploader-manual-trigger .qq-upload-button {
            margin-right: 15px;
        }

        #fine-uploader-manual-trigger .buttons {
            width: 36%;
        }

        #fine-uploader-manual-trigger .qq-uploader .qq-total-progress-bar-container {
            width: 60%;
        }
    </style>
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
            sizeLimit: <{$imgcat_maxsize}>
        },
        autoUpload: false,
        debug: true
    });

    qq(document.getElementById("trigger-upload")).attach("click", function() {
        manualUploader.uploadStoredFiles();
    });
</script>

</body>
</html>
