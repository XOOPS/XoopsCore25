<{include file='db:wggallery_header.tpl'}>

<{if isset($form)}>
	<{$form}>
<{/if}>

<{if isset($multiupload)}>
    <div class="clear">&nbsp;</div>
    <{include file="db:wggallery_trigger_uploads.tpl"}>
    <h2><{$img_albname}></h2>
    <div id="fine-uploader-manual-trigger"></div>
    <div><{$smarty.const._IMGMAXSIZE}> <{$img_maxsize}></div>
    <div><{$smarty.const._IMGMAXWIDTH}> <{$img_maxwidth}></div>
    <div><{$smarty.const._IMGMAXHEIGHT}> <{$img_maxheight}></div>
    <!-- Your code to create an instance of Fine Uploader and bind to the DOM/template
    ====================================================================== -->
    <script>
        var filesTotal = 0;
        var manualUploader = new qq.FineUploader({
            element: document.getElementById('fine-uploader-manual-trigger'),
            template: 'qq-template-manual-trigger',
            request: {
                endpoint: '<{$xoops_url}>/ajaxfineupload.php',
                params: {
                    "Authorization": "<{$jwt}>"
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
                minWidthImageError: "<{$smarty.const._MINWIDTHIMAGEERROR}>",
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
                acceptFiles: [<{$allowedmimetypes}>],
                allowedExtensions: [<{$allowedfileext}>],
                image: {
                    maxHeight: <{$img_maxheight}>,
                    maxWidth: <{$img_maxwidth}>
                },
                sizeLimit: <{$img_maxsize}>
            },
            autoUpload: false,
            callbacks: {
                onError: function(id, name, errorReason, xhrOrXdr) {
                    console.log(qq.format("Error uploading {}.  Reason: {}", name, errorReason));
                },
                onStatusChange: function(id, oldStatus, newStatus) {
                    document.getElementById("qq-uploader-status").classList.remove("qq-hide");
                    if ( newStatus == "submitting" ) {
                        filesTotal=id;
                    }
                },
                onSubmitted: function(id, name) {
                    if (id == filesTotal) {
                        document.getElementById('qq-uploader-status-text').innerHTML = '<{$smarty.const._CO_WGGALLERY_FU_SUBMITTED}>';
                    } else {
                        document.getElementById('qq-uploader-status-text').innerHTML = '<{$smarty.const._CO_WGGALLERY_FU_SUBMIT}>' + (id + 1);
                    }
                },
                onUpload: function(id, name) {
                   document.getElementById('qq-uploader-status-text').innerHTML = '<{$smarty.const._CO_WGGALLERY_FU_UPLOAD}>' + id;
                },
                onAllComplete: function(succeeded, failed) {
                    if ( failed.length > 0 ) {
                        document.getElementById('qq-uploader-status-text').innerHTML = '<{$smarty.const._CO_WGGALLERY_FU_FAILED}>';
                    } else {
                        document.getElementById('qq-uploader-status-text').innerHTML = '<{$smarty.const._CO_WGGALLERY_FU_SUCCEEDED}>';
                    }
                }
            },
            debug: <{$fineup_debug}>
        });

        qq(document.getElementById("trigger-upload")).attach("click", function() {
            manualUploader.uploadStoredFiles();
        });
    </script>
<{/if}>
<div class="clear">&nbsp;</div>
<div class='multiupload-footer'>
	<{if isset($albId)}>
		<div class='col-xs-12 col-sm-12 right'>
			<a class='btn btn-secondary wgg-btn' href='images.php?op=list&amp;ref=albums&amp;alb_id=<{$albId}>&amp;alb_pid=<{$albPid}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>photos.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}><{/if}></a>
            <a class='btn btn-secondary wgg-btn' href='albums.php?op=edit&amp;alb_id=<{$albId}>' title='<{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}>'>
				<span class="wgg-btn-icon"><img class='' src='<{$wggallery_icon_url_16}>edit.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}>'></span><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}><{/if}></a>
			<a class='btn btn-secondary wgg-btn' href='album_images.php?op=list&amp;alb_id=<{$albId}>' title='<{$smarty.const._CO_WGGALLERY_ALBUM_IH_IMAGE_EDIT}>'>
				<span class="wgg-btn-icon"><img class='' src='<{$wggallery_icon_url_16}>album_images.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_IH_IMAGE_EDIT}>'></span><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_ALBUM_IH_IMAGE_EDIT}><{/if}></a>
		</div>
	<{/if}>
</div>


<{include file='db:wggallery_footer.tpl'}>
