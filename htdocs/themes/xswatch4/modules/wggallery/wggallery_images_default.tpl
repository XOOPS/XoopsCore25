<{include file='db:wggallery_header.tpl'}>

<div class='card panel-<{$panel_type}> mb-3'>
	<{if isset($showlist)}>
        <div class='card-header wgg-imgindex-header'><{$smarty.const._CO_WGGALLERY_IMAGES_TITLE}> <{$alb_name}></div>
        <div class='card-body'>
            <{if isset($images)}>
                <{foreach item=image from=$images|default:null}>
                    <div id='imglist_<{$image.id}>' class='row wgg-img-panel wgg-image-list'>
                        <div class='wgg-img-panel-row col-sm-8'>
                            <{if $image.medium}>
                                <div class='center'><img id='image_<{$image.id}>' class='img-fluid wgg-img' src='<{$image.medium}>#<{$random}>' alt='<{$image.title}>'></div>
                            <{/if}>
                        </div>
                        <div class='wgg-img-panel-row col-sm-4'>
                            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>photos.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_TITLE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_TITLE}>'><{$image.title}>
                            <{if $image.desc}><{$image.desc}><{/if}>
                            </p>
                            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>size.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>'><{$image.size}> kb</p>
                            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>dimension.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>'><{$image.resx}>px / <{$image.resy}>px</p>
                            <{if isset($img_allowdownload)}>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>download.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_DOWNLOADS}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_DOWNLOADS}>'><{$image.downloads}></p>
                            <{/if}>
							<{if isset($show_rating)}>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>rate.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>'><{$image.ratinglikes}> (<{$image.votes}> <{$smarty.const._CO_WGGALLERY_IMAGE_VOTES}>)</p>
							<{/if}>
							<{if isset($permAlbumEdit)}>
								<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>state<{$image.state}>.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_STATE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_STATE}>'><{$image.state_text}></p>
                            <{/if}>
							<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>date.png' alt='<{$smarty.const._CO_WGGALLERY_DATE}>' title='<{$smarty.const._CO_WGGALLERY_DATE}>'><{$image.date}></p>
                            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>views.png' alt='<{$smarty.const._CO_WGGALLERY_VIEWS}>' title='<{$smarty.const._CO_WGGALLERY_VIEWS}>'><{$image.views}></p>
                            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>submitter.png' alt='<{$smarty.const._CO_WGGALLERY_SUBMITTER}>' title='<{$smarty.const._CO_WGGALLERY_SUBMITTER}>'><{$image.submitter}></p>
                            <{if $use_categories && $image.cats_list}>
								<p class='wgg-cats'><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>categories.png' alt='<{$smarty.const._CO_WGGALLERY_CATS}>' title='<{$smarty.const._CO_WGGALLERY_CATS}>'><{$image.cats_list}></p>
                            <{/if}>
                            <{if $use_tags && $image.tags}>
								<p class='wgg-tags'><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>tags.png' alt='<{$smarty.const._CO_WGGALLERY_TAGS}>' title='<{$smarty.const._CO_WGGALLERY_TAGS}>'><{$image.tags}></p>
                            <{/if}>
                            <{if $image.com_show}>
								<p class='wgg-comcount'><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>comments.png' alt='<{$smarty.const._CO_WGGALLERY_COMMENTS}>' title='<{$smarty.const._CO_WGGALLERY_COMMENTS}>'><{$image.com_count_text}></p>
                            <{/if}>
                            <{if isset($rating) && $rating > 0}>
                                <{if $rating_5stars || $rating_10stars || $rating_10num}>
                                    <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>rate.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>'><{$image.rating.shorttext}></p>
                                <{/if}>
                                <{if isset($rating_likes)}>
                                    <p>
                                        <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>like.png' alt='<{$smarty.const._MA_WGGALLERY_RATING_LIKE}>' title='<{$smarty.const._MA_WGGALLERY_RATING_LIKE}>'>(<{$image.rating.likes}>)
                                        <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>dislike.png' alt='<{$smarty.const._MA_WGGALLERY_RATING_DISLIKE}>' title='<{$smarty.const._MA_WGGALLERY_RATING_DISLIKE}>'> (<{$image.rating.dislikes}>)
                                    </p>
                                <{/if}>
                            <{/if}>
                            <{if isset($show_exif)}>
								<p class='wgg-comcount'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>exif.png' alt='<{$smarty.const._CO_WGGALLERY_EXIF}>' title='<{$smarty.const._CO_WGGALLERY_EXIF}>'>
                                    <{if $image.exif}><img src="<{$wggallery_icon_url_16}>on.png" alt="_YES"><{else}><img src="<{$wggallery_icon_url_16}>0.png" alt="_NO"><{/if}>
                                </p>
                            <{/if}>
                        </div>
                        <div class='wgg-img-panel-row col-sm-12 center'>
                            <{if isset($showModal)}>
                                <a href='' id='btnModal<{$image.id}>' class='btn btn-secondary wgg-btn' data-toggle='modal' data-target='#myModalImagePicker<{$image.id}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>show.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SHOW}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGE_SHOW}><{/if}></a>
                            <{else}>
                                <a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/images.php?op=show&amp;redir=list&amp;img_id=<{$image.id}>&amp;alb_id=<{$image.albid}>&amp;start=<{$start}>&amp;limit=<{$limit}>&amp;img_submitter=<{$img_submitter}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_SHOW}>' target='<{$image_target}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>show.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SHOW}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGE_SHOW}><{/if}></a>
                            <{/if}>
                            <{if isset($permAlbumEdit)}>
                                <a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/images.php?op=edit&amp;img_id=<{$image.id}>' title='<{$smarty.const._EDIT}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>edit.png' alt='<{$smarty.const._EDIT}>'><{if isset($displayButtonText)}><{$smarty.const._EDIT}><{/if}></a>
                                <a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/images.php?op=delete&amp;img_id=<{$image.id}>&amp;alb_id=<{$image.albid}>&amp;alb_pid=<{$alb_pid}>' title='<{$smarty.const._DELETE}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>delete.png' alt='<{$smarty.const._DELETE}>'><{if isset($displayButtonText)}><{$smarty.const._DELETE}><{/if}></a>
                                <a class='btn btn-secondary wgg-btn' href='images.php?op=rotate&amp;dir=left&amp;img_id=<{$image.id}>&amp;alb_id=<{$alb_id}>&amp;start=<{$start}>&amp;limit=<{$limit}>&amp;img_submitter=<{$img_submitter}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_ROTATE_LEFT}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>rotate_left.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_ROTATE_LEFT}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGE_ROTATE_LEFT}><{/if}></a>
                                <a class='btn btn-secondary wgg-btn' href='images.php?op=rotate&amp;dir=right&amp;img_id=<{$image.id}>&amp;alb_id=<{$alb_id}>&amp;start=<{$start}>&amp;limit=<{$limit}>&amp;img_submitter=<{$img_submitter}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_ROTATE_RIGHT}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>rotate_right.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_ROTATE_RIGHT}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGE_ROTATE_RIGHT}><{/if}></a>
                            <{/if}>
                            <{if isset($img_allowdownload)}>
                                <a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/download.php?op=default&amp;img_id=<{$image.id}>' title='<{$smarty.const._CO_WGGALLERY_DOWNLOAD}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>download.png' alt='<{$smarty.const._CO_WGGALLERY_DOWNLOAD}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_DOWNLOAD}><{/if}></a>
                            <{/if}>
                        </div>
                    </div>
                <{/foreach}>
            <{elseif $showlist}>
                <div class=''>
                    <div class='errorMsg'><strong><{$smarty.const._CO_WGGALLERY_THEREARENT_IMAGES}></strong></div>
                </div>
            <{/if}>
            <div class='clear'>&nbsp;</div>
            <div class='wgg-goback'>
                <a class='btn btn-secondary wgg-btn' href='<{if isset($ref)}><{$ref}><{else}>index<{/if}>.php?op=list&amp;alb_id=<{$alb_id}>&amp;alb_pid=<{$alb_pid}>#album_<{$alb_id}>' title='<{$smarty.const._CO_WGGALLERY_BACK}>'>
                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>back.png' alt='<{$smarty.const._CO_WGGALLERY_BACK}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_BACK}><{/if}></a>
                <{if isset($permAlbumEdit)}>
                    <a class='btn btn-secondary wgg-btn' href='albums.php?op=edit&amp;alb_id=<{$alb_id}>' title='<{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}>'>
                        <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>edit.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}><{/if}></a>
                    <a class='btn btn-secondary wgg-btn' href='images.php?op=manage&amp;alb_id=<{$alb_id}>&amp;redir=list' title='<{$smarty.const._CO_WGGALLERY_IMAGE_MANAGE}>'>
                        <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>images.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_MANAGE}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGE_MANAGE}><{/if}></a>
                    <a class='btn btn-secondary wgg-btn' href='upload.php?op=list&amp;alb_id=<{$alb_id}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_UPLOAD}>'>
                        <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>upload.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGES_UPLOAD}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGES_UPLOAD}><{/if}></a>
                <{/if}>
            </div>
        </div>
        <div class='clear'>&nbsp;</div>
            <{if isset($pagenav)}>
                <div class="col">
                <div class="generic-pagination xo-pagenav pull-right mb-2"><{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}></div>
                </div>
            <{/if}>
        </div>
    <{/if}>
    <{if isset($showimage)}>
        <div class='wgg-img-panel-row col-12 col-sm-12 col-md-12 col-lg-12 center'><img class='img-fluid wgg-img' src='<{$file}>' alt='<{$image.title}>'></div>
        <div class='wgg-img-panel-row col-12 col-sm-6 col-md-6 col-lg6'>
            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>photos.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_TITLE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_TITLE}>'><{$image.title}></p>
            <{if $image.desc}>
                <p class='justify'><{$image.desc}></p>
            <{/if}>
        </div>
        <div class='wgg-img-panel-row col-12 col-sm-6 col-md-6 col-lg6'>
            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>size.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>'><{$image.size}> kb</p>
            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>dimension.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>'><{$image.resx}>px / <{$image.resy}>px</p>
            <{if isset($img_allowdownload)}>
                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>download.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_DOWNLOADS}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_DOWNLOADS}>'><{$image.downloads}></p>
            <{/if}>
			<{if isset($show_rating)}>
				<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>rate.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>'><{$image.ratinglikes}> (<{$image.votes}> <{$smarty.const._CO_WGGALLERY_IMAGE_VOTES}>)</p>
			<{/if}>
			<{if isset($permAlbumEdit)}>
				<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>state<{$image.state}>.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_STATE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_STATE}>'><{$image.state_text}></p>
            <{/if}>
			<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>date.png' alt='<{$smarty.const._CO_WGGALLERY_DATE}>' title='<{$smarty.const._CO_WGGALLERY_DATE}>'><{$image.date}></p>
            <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>submitter.png' alt='<{$smarty.const._CO_WGGALLERY_SUBMITTER}>' title='<{$smarty.const._CO_WGGALLERY_SUBMITTER}>'><{$image.submitter}></p>
            <p class='wgg-img-exif'><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>exif.png' alt='<{$smarty.const._CO_WGGALLERY_EXIF}>' title='<{$smarty.const._CO_WGGALLERY_EXIF}>'><{$image.exif}></p>
            <{if isset($rating)}>
                <{include file="db:wggallery_rating_img.tpl"}>
            <{/if}>
        </div>
        <div class='clear'>&nbsp;</div>
		<div class='wgg-img-panel-row col-sm-12 center'>
			<{if isset($showBack)}>
                <a class='btn btn-secondary wgg-btn' href='images.php?op=<{if isset($redir_op)}><{$redir_op}><{else}>list<{/if}>&amp;alb_id=<{$alb_id}>&amp;alb_pid=<{$alb_pid}>&amp;start=<{$start}>&amp;limit=<{$limit}>&amp;img_submitter=<{$img_submitter}>#image_<{$image.id}>' title='<{$smarty.const._CO_WGGALLERY_BACK}>'>
                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>back.png' alt='<{$smarty.const._CO_WGGALLERY_BACK}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_BACK}><{/if}></a>
            <{/if}>
			<{if isset($permAlbumEdit)}>
				<a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/images.php?op=edit&amp;img_id=<{$image.id}>' title='<{$smarty.const._EDIT}>'>
					<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>edit.png' alt='<{$smarty.const._EDIT}>'><{if isset($displayButtonText)}><{$smarty.const._EDIT}><{/if}></a>
				<a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/images.php?op=delete&amp;img_id=<{$image.id}>&amp;alb_id=<{$image.albid}>&amp;alb_pid=<{$alb_pid}>' title='<{$smarty.const._DELETE}>'>
					<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>delete.png' alt='<{$smarty.const._DELETE}>'><{if isset($displayButtonText)}><{$smarty.const._DELETE}><{/if}></a>
			<{/if}>
			<{if isset($img_allowdownload)}>
				<a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/download.php?op=default&amp;img_id=<{$image.id}>' title='<{$smarty.const._CO_WGGALLERY_DOWNLOAD}>'>
					<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>download.png' alt='<{$smarty.const._CO_WGGALLERY_DOWNLOAD}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_DOWNLOAD}><{/if}></a>
			<{/if}>
		</div>
</div>
        <{$commentsnav}>
        <div class="center"><{$lang_notice}></div>
<{*        <{if isset($comment_mode)}>*}>
<{*            <{if $comment_mode == "flat"}>*}>
<{*                <{include file="db:system_comments_flat.tpl"}>*}>
<{*            <{elseif $comment_mode == "thread"}>*}>
<{*                <{include file="db:system_comments_thread.tpl"}>*}>
<{*            <{elseif $comment_mode == "nest"}>*}>
<{*                <{include file="db:system_comments_nest.tpl"}>*}>
<{*            <{/if}>*}>
<{*        <{/if}>*}>


    <{/if}>
<!-- </div> -->

<{if isset($showModal)}>
    <!-- Create Modals -->
    <{foreach item=image from=$images|default:null}>
<!-- Modal 4 -->
    <div class="modal"  id='myModalImagePicker<{$image.id}>' tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><{$image.title}></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <{if isset($showModalInfo)}>
                            <div class="col-12 col-md-6 col-lg-6">
                            <{else}>
                            <div class="col-12 col-md-12 col-lg-12">
                            <{/if}>
                            <{if $image.alb_name}><h4 class='modal-title'><{$image.alb_name}></h4><{/if}>
                            <img class='img-fluid wgg-img' src='<{$image.img_modal}>' alt='<{$image.title}>'>
                            <{if isset($showModalInfo)}>
                             </div>
                             <div class="col-12 col-md-6 col-lg-6">
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>size.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>'><{$image.size}> kb</p>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>dimension.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_SIZE}>'><{$image.resx}>px / <{$image.resy}>px</p>
                                <{if isset($img_allowdownload)}>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>download.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_DOWNLOADS}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_DOWNLOADS}>'><{$image.downloads}></p>
                                <{/if}>
                                <{if isset($show_rating)}>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>rate.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_RATINGLIKES}>'><{$image.ratinglikes}> (<{$image.votes}> <{$smarty.const._CO_WGGALLERY_IMAGE_VOTES}>)</p>
                                <{/if}>
                                <{if isset($permAlbumEdit)}>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>state<{$image.state}>.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_STATE}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_STATE}>'><{$image.state_text}></p>
                                <{/if}>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>date.png' alt='<{$smarty.const._CO_WGGALLERY_DATE}>' title='<{$smarty.const._CO_WGGALLERY_DATE}>'><{$image.date}></p>
                                <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>submitter.png' alt='<{$smarty.const._CO_WGGALLERY_SUBMITTER}>' title='<{$smarty.const._CO_WGGALLERY_SUBMITTER}>'><{$image.submitter}></p>
                                <p class='wgg-img-exif'><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>exif.png' alt='<{$smarty.const._CO_WGGALLERY_EXIF}>' title='<{$smarty.const._CO_WGGALLERY_EXIF}>'><{$image.exif}></p>
                                <{if isset($rating)}>
                                    <{include file="db:wggallery_rating_img.tpl"}>
                                <{/if}>
                             </div>
                             <{else}>
                             </div>
                             <{/if}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<{/foreach }>
<{/if}>

<{if isset($form)}>
	<{$form}>
</div>
<{/if}>
<{if isset($error)}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{include file='db:wggallery_footer.tpl'}>
