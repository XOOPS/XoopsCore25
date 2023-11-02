<{include file='db:wggallery_header.tpl'}>
<{if isset($albums_list)}>
	<div class='col-sm-12 col-sm-8'>
	<div class='panel panel-<{$panel_type}>'>
		<div class='panel-heading'><{$smarty.const._CO_WGGALLERY_ALBUMS_TITLE}></div>
		<div class='panel-body'>
			<{foreach item=album from=$albums_list|default:null}>
				<div class='row wgg-album-list'>
					<div class='col-sm-4'>
						<img id='album_<{$album.id}>' class='img-fluid wgg-album-img' src='<{$album.image}>?<{$force}>' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_IMAGE}> <{$album.name}>'>
					</div>
					<div class='col-sm-4'>
						<p class='wgg-album-name'><{$album.name}></p>
						<p class='wgg-album-desc'><{$album.desc}></p>
					</div>
					<div class='col-sm-4'>
						<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>collection.png'
							alt='<{if $album.iscoll == 1}><{$smarty.const._CO_WGGALLERY_ALBUM_COLL}><{else}><{$smarty.const._CO_WGGALLERY_ALBUM}><{/if}>'>
							<{if $album.iscoll == 1}><{$smarty.const._CO_WGGALLERY_ALBUM_COLL}><{else}><{$smarty.const._CO_WGGALLERY_ALBUM}><{/if}>
						</p>
						<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>state<{$album.state}>.png' alt='<{$album.state_text}>'><{$album.state_text}></p>
                        <p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>photos.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGES_COUNT}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_COUNT}>'><span><{$album.nb_images}> <{$smarty.const._CO_WGGALLERY_ALBUM_NB_IMAGES}></span></p>
						<{if $album.nb_subalbums > 0}>
							<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>albums.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUMS_COUNT}>' title='<{$smarty.const._CO_WGGALLERY_ALBUMS_COUNT}>'><span><{$album.nb_subalbums}> <{$smarty.const._CO_WGGALLERY_ALBUM_NB_COLL}></span></p>
						<{/if}>
						<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>date.png' alt='<{$album.date}>'><{$album.date}></p>
						<p><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>submitter.png' alt='<{$album.submitter}>'><{$album.submitter}></p>
					</div>
					<div class='col-sm-12 center'>
						<{if $album.nb_images}>
							<{if isset($gallery)}>
								<a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$gallery_target}>' >
									<span class="wgg-btn-icon"><img class='' src='<{$wggallery_icon_url_16}>show.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>'></span>
									<{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}><{/if}>
								</a>
							<{/if}>
							<a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/images.php?op=list&amp;ref=albums&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
								<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>photos.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
								<{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}><{/if}>
							</a>
						<{else}>
							<a class='disabled btn btn-secondary wgg-btn'><img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>photos.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'><{$smarty.const._CO_WGGALLERY_ALBUM_NO_IMAGES}></a>
						<{/if}>
						<{if $album.edit}>
                            <{if $album.nb_images}>
                                <a class='btn btn-secondary wgg-btn' href='images.php?op=manage&amp;ref=albums&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGE_MANAGE}>'>
                                    <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>images.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGE_MANAGE}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGE_MANAGE}><{/if}></a>
                            <{/if}>
							<{if 0 == $album.iscoll}>
								<a class='btn btn-secondary wgg-btn' href='upload.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_UPLOAD}>'>
									<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>upload.png' alt='<{$smarty.const._CO_WGGALLERY_IMAGES_UPLOAD}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_IMAGES_UPLOAD}><{/if}></a>
                            <{/if}>
							<a class='btn btn-secondary wgg-btn' href='albums.php?op=edit&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}>'>
                                <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>edit.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_ALBUM_EDIT}><{/if}></a>
                            <a class='btn btn-secondary wgg-btn' href='album_images.php?op=imghandler&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_ALBUM_IH_IMAGE_EDIT}>'>
                                <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>album_images.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_IH_IMAGE_EDIT}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_ALBUM_IH_IMAGE_EDIT}><{/if}></a>
                            <a class='btn btn-secondary wgg-btn' href='albums.php?op=delete&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._DELETE}>'>
                                <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>delete.png' alt='<{$smarty.const._DELETE}>'><{if isset($displayButtonText)}><{$smarty.const._DELETE}><{/if}></a>
                        <{/if}>
                        <{if 0 == $album.iscoll && $album.download}>
                            <a class='btn btn-secondary wgg-btn' href='<{$wggallery_url}>/download.php?op=album&amp;alb_id=<{$album.id}>' title='<{$smarty.const._CO_WGGALLERY_DOWNLOAD_ALB}>'>
                                <img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>download.png' alt='<{$smarty.const._CO_WGGALLERY_DOWNLOAD_ALB}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_DOWNLOAD_ALB}><{/if}></a>
                        <{/if}>

						<{if $album.nb_subalbums}>
							<a class='btn btn-secondary wgg-btn' href='albums.php?op=list&amp;alb_pid=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}>'>
								<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>index.png' alt='<{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}><{/if}></a>
						<{/if}>
					</div>
				</div>
			<{/foreach}>
		</div>
	</div>
	<div class='clear'>&nbsp;</div>
	<{if isset($pagenav)}>
		<div class="col">
		<div class="generic-pagination xo-pagenav pull-right"><{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}></div>
		</div>
	<{/if}>
	</div>


	<div class='col-sm-12 col-sm-4'>
		<div class='panel panel-<{$panel_type}>'>
			<div class='panel-heading'><{$smarty.const._CO_WGGALLERY_ALBUMS_SORT}></div>
			<div class='panel-body'>
				<ol class="sortable ui-sortable mjs-nestedSortable-branch mjs-nestedSortable-expanded">
					<{$albumlist_sort}>
				</ol>
				<p class='center'>
					<a class='btn btn-secondary wgg-btn' href='albums.php' title='<{$smarty.const._CO_WGGALLERY_UPDATE}>'>
						<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>reset.png' alt='<{$smarty.const._CO_WGGALLERY_UPDATE}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_UPDATE}><{/if}></a>
					<{if isset($global_submit)}>
						<a class='btn btn-secondary wgg-btn' href='albums.php?op=new&alb_pid=<{$albpid}>' title='<{$smarty.const._CO_WGGALLERY_ALBUM_ADD}>'>
							<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>add.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_ADD}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_ALBUM_ADD}><{/if}></a>
					<{/if}>
				</p>
			</div>
		</div>
	</div>
<{/if}>


<{if isset($form)}>
	<{$form}>

	<!-- Modal -->
    <div class="modal fade" id="myModalImagePicker" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog wgg-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><{$smarty.const._CO_WGGALLERY_IMAGES_TITLE}></h4>
                </div>
                <div class="modal-body">
                    <{foreach item=image from=$images|default:null}>
                        <{if $image.alb_name}><h4 class="modal-title"><{$image.alb_name}></h4><{/if}>
                        <input class="img <{if $image.selected}>wgg-modal-selected<{/if}>" type="image" src="<{$image.medium}>" alt="<{$image.title}>"
                               style="padding:3px;max-height:150px;max-width:200px" value="<{$image.name}>">
                    <{/foreach}>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        $('#myModalImagePicker').on('shown.bs.modal', function () {
            $('#alb_imgid').focus()
        })
        $(".img").click(function () {
            $('#alb_imgid').val($(this).attr('value'));
            var elements = document.getElementsByClassName('wgg-modal-selected');
            while(elements.length > 0){
                elements[0].classList.remove('wgg-modal-selected');
            }
            $(this).addClass("wgg-modal-selected");
            $('#alb_imgid').change();

            $('#myModalImagePicker').modal('hide');
            return false;
        })
    </script>

<{/if}>
<{if isset($error)}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<div class='clear'>&nbsp;</div>
<div class='center'>
	<{if isset($global_submit)}>
		<a class='btn btn-secondary wgg-btn' href='albums.php?op=new' title='<{$smarty.const._CO_WGGALLERY_ALBUM_ADD}>'>
			<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>add.png' alt='<{$smarty.const._CO_WGGALLERY_ALBUM_ADD}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_ALBUM_ADD}><{/if}></a>
	<{/if}>
	<{if isset($goback)}>
	<a class='btn btn-secondary wgg-btn' href='albums.php?op=list' title='<{$smarty.const._CO_WGGALLERY_BACK}>'>
		<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>back.png' alt='<{$smarty.const._CO_WGGALLERY_BACK}>'><{if isset($displayButtonText)}><{$smarty.const._CO_WGGALLERY_BACK}><{/if}></a>
	<{/if}>
</div>

<script>
	$('.disclose').attr('title','<{$smarty.const._CO_WGGALLERY_ALBUM_SORT_SHOWHIDE}>');
</script>

<{include file='db:wggallery_footer.tpl'}>
