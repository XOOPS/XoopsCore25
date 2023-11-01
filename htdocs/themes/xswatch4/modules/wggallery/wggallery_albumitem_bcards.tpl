<{assign var='cols' value='col-12'}>
<{if isset($number_cols_album)}>
    <{if $number_cols_album == 6}><{assign var='cols' value='col-12 col-md-2'}>
    <{elseif $number_cols_album == 4}><{assign var='cols' value='col-12 col-md-3'}>
    <{elseif $number_cols_album == 3}><{assign var='cols' value='col-12 col-md-4'}>
    <{elseif $number_cols_album == 2}><{assign var='cols' value='col-12 col-md-6'}>
    <{/if}>
<{/if}>

	<div class="card <{$cols}>">
        <{if $album.image}>
            <{if $album.nb_images}>
                <{if isset($gallery)}>
                    <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$gallery_target}>' >
                <{else}>
                    <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                <{/if}>
            <{/if}>
            <img class="card-img-top" src="<{$album.image}>" alt="<{$album.name}>" title="<{$album.name}>">
            <{if $album.nb_images}>
            </a>
            <{/if}>
        <{/if}>
		<div class="card-body text-center">
            <{if isset($showTitle)}><h5><{$album.name}></h5><{/if}>
            <{if isset($showDesc)}><p><{$album.desc}></p><{/if}>
            <p class="center">
                <{if isset($gallery)}>
                    <a class='btn btn-primary wg-color1' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$gallery_target}>' ><{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}></a>
                <{else}>
                    <a class='btn btn-primary wg-color1' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'><{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}></a>
                <{/if}>
            </p>
        </div>
	</div>

<{if $album.linebreak}>
	<div class='clear'>&nbsp;</div>
<{/if}>
