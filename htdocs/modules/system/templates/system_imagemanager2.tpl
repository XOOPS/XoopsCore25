<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>"/>
    <meta http-equiv="content-language" content="<{$xoops_langcode}>"/>
    <title><{$xoops_sitename}> <{$lang_imgmanager}></title>
    <{$image_form.javascript}>
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl xoops.css}>"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl modules/system/css/imagemanager.css}>"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl media/font-awesome/css/font-awesome.min.css}>"/>

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
    <link href="<{xoAppUrl Frameworks/fine-uploader/fine-uploader-new.css}>" rel="stylesheet">

    <!-- Fine Uploader JS file
    ====================================================================== -->
    <script src="<{$xoops_url}>/Frameworks/fine-uploader/fine-uploader.js"></script>

    <!-- Fine Uploader Thumbnails template w/ customization
    ====================================================================== -->
    <script type="text/template" id="qq-template-manual-trigger">
        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="buttons">
                <div class="qq-upload-button-selector qq-upload-button">
                    <div>Select files</div>
                </div>
                <button type="button" id="trigger-upload" class="btn btn-primary" title="Upload">
                    <span class="fa fa-upload"></span> Upload
                </button>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
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
                    <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                    <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                    <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Close</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">No</button>
                    <button type="button" class="qq-ok-button-selector">Yes</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Cancel</button>
                    <button type="button" class="qq-ok-button-selector">Ok</button>
                </div>
            </dialog>
        </div>
    </script>

    <style>
        #trigger-upload {
            color: white;
            background-color: #00ABC7;
            font-size: 14px;
            padding: 7px 20px;
            background-image: none;
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
            endpoint: '<{$xoops_url}>/Frameworks/fine-uploader/endpoint.php'
        },
        thumbnails: {
            placeholders: {
                waitingPath: '<{$xoops_url}>/Frameworks/fine-uploader/placeholders/waiting-generic.png',
                notAvailablePath: '<{$xoops_url}>/Frameworks/fine-uploader/placeholders/not_available-generic.png'
            }
        },
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'png', 'gif'],
            image: {
                maxHeight: <{$imgcat_maxheight}>,
                maxWidth: <{$imgcat_maxwidth}>
            },
            sizeLimit: <{$imgcat_maxsize}> // 50 kB = 50 * 1024 bytes
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
