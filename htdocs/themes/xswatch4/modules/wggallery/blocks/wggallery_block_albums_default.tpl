<i id='gallery'></i>
<{if isset($balbums_list)}>
    <{foreach item=album from=$balbums_list|default:null name=album}>
        <{if $album.newrow}><div class="row wgg-row-block"><{/if}>
        <{if isset($bnbAlbumsRow) && $bnbAlbumsRow == 2}><div class='col-xs-12 col-sm-6'>
        <{elseif $bnbAlbumsRow == 3}><div class='col-xs-12 col-sm-4'>
        <{elseif $bnbAlbumsRow == 4}><div class='col-xs-12 col-sm-3'>
        <{elseif $bnbAlbumsRow == 6}><div class='col-xs-12 col-sm-2'>
        <{else}><div class='col-xs-12 col-sm-12'>
        <{/if}>
            <{if isset($ba_template) && $ba_template == 'hovereffectideas'}>
                <{if $album.newrow}><div class="grid"><{/if}>
                <figure class="effect-<{$hovereffect}> figure<{$number_cols_album}><{$inblock}>">
                    <img class='img-fluid' src='<{$album.image}>' alt='<{$album.name}>'>
                    <figcaption>
                        <div class="text_figure<{$number_cols_album}><{$inblock}>">
                            <{if $ba_showTitle || $ba_showDesc}>
                                <{if isset($ba_gallery)}>
                                    <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>'>
                                <{else}>
                                    <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$alb_pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                                <{/if}>
                                <{if isset($ba_showTitle)}>
                                    <{if $album.name_limited}>
                                        <p class='wgg-block-atitle'><{$album.name_limited}><p>
                                    <{else}>
                                        <p class='wgg-block-atitle'><{$album.name}><p>
                                    <{/if}>
                                <{/if}>
                                <{if isset($ba_showDesc)}>
                                    <{if $album.desc_limited}>
                                        <p class='wgg-block-adesc'><{$album.desc_limited}><p>
                                    <{else}>
                                        <p class='wgg-block-adesc'><{$album.desc}><p>
                                    <{/if}>
                                <{/if}>
                                </a>
                            <{/if}>
                        </div>
                        <{if $album.nb_images}>
                            <{if isset($ba_gallery)}>
                                <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>'></a>
                            <{else}>
                                <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'></a>
                            <{/if}>
                        <{/if}>
                    </figcaption>
                </figure>
            <{elseif $ba_template == 'bcards'}>
                <div class="card">
                    <{if $album.image}>
                        <{if $album.nb_images}>
                            <{if isset($ba_gallery)}>
                                <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>' >
                            <{else}>
                                <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                            <{/if}>
                        <{/if}>
                        <img class="card-img-top img-fluid" src="<{$album.image}>" alt="<{$album.name}>" title="<{$album.name}>">
                        <{if $album.nb_images}></a><{/if}>
                    <{/if}>
                    <div class="card-body">
                        <{if $ba_showTitle || $ba_showDesc}>
                            <{if isset($ba_gallery)}>
                                <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>'>
                            <{else}>
                                <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$alb_pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                            <{/if}>
                            <{if isset($ba_showTitle)}>
                                <{if $album.name_limited}>
                                    <p class='wgg-block-atitle center'><{$album.name_limited}><p>
                                <{else}>
                                    <p class='wgg-block-atitle center'><{$album.name}><p>
                                <{/if}>
                            <{/if}>
                            <{if isset($ba_showDesc)}>
                                <{if $album.desc_limited}>
                                    <p class='wgg-block-adesc center'><{$album.desc_limited}><p>
                                <{else}>
                                    <p class='wgg-block-adesc center'><{$album.desc}><p>
                                <{/if}>
                            <{/if}>
                            </a>
                        <{/if}>
                        <p class="center">
                            <{if isset($ba_gallery)}>
                                <a class='btn btn-primary wg-color1' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>' ><{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}></a>
                            <{else}>
                                <a class='btn btn-primary wg-color1' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'><{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}></a>
                            <{/if}>
                        </p>
                    </div>
                </div>
            <{elseif $ba_template == 'simple'}>
                <div class='center'>
                    <{if $album.nb_images}>
                        <{if isset($gallery)}>
                            <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>' >
                        <{else}>
                            <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$album.pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                        <{/if}>
                    <{/if}>

                    <div class="simpleContainer">
                        <{if $album.image}><img class="img-fluid center" src="<{$album.image}>" alt="<{$album.name}>" title="<{$album.name}>"><{/if}>
                        <div class="simpleContent">
                            <{if $ba_showTitle || $ba_showDesc}>
                                <{if isset($ba_gallery)}>
                                    <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>'>
                                <{else}>
                                    <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$alb_pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                                <{/if}>
                                <{if isset($ba_showTitle)}>
                                    <{if $album.name_limited}>
                                        <p class='wgg-block-atitle'><{$album.name_limited}><p>
                                    <{else}>
                                        <p class='wgg-block-atitle'><{$album.name}><p>
                                    <{/if}>
                                <{/if}>
                                <{if isset($ba_showDesc)}>
                                    <{if $album.desc_limited}>
                                        <p class='wgg-block-adesc'><{$album.desc_limited}><p>
                                    <{else}>
                                        <p class='wgg-block-adesc'><{$album.desc}><p>
                                    <{/if}>
                                <{/if}>
                                </a>
                            <{/if}>
                        </div>
                    </div>
                    <{if $album.nb_images}></a><{/if}>
                </div>
            <{else}>
                <{if $album.image}>
                    <div class='center'>
                        <{if $album.nb_images}>
                            <{if isset($ba_gallery)}>
                                <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>'>
                                    <img class='img-fluid wgg-album-img center' src='<{$album.image}>' alt='<{$album.name}>'></a>
                            <{else}>
                                <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$alb_pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                                    <img class='img-fluid wgg-album-img center' src='<{$album.image}>' alt='<{$album.name}>'></a>
                            <{/if}>
                        <{else}>
                            <img class='img-fluid wgg-album-img center' src='<{$album.image}>' alt='<{$album.name}>'>
                        <{/if}>
                        <{if $ba_showTitle || $ba_showDesc}>
                            <{if isset($ba_gallery)}>
                                <a class='' href='<{$wggallery_url}>/gallery.php?op=show&amp;alb_id=<{$album.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_ALBUMSHOW}>' target='<{$ba_gallery_target}>'>
                            <{else}>
                                <a class='' href='<{$wggallery_url}>/images.php?op=list&amp;alb_id=<{$album.id}>&amp;alb_pid=<{$alb_pid}>' title='<{$smarty.const._CO_WGGALLERY_IMAGES_INDEX}>'>
                            <{/if}>
                            <{if isset($ba_showTitle)}>
                                <{if $album.name_limited}>
                                    <p class='wgg-block-atitle'><{$album.name_limited}><p>
                                <{else}>
                                    <p class='wgg-block-atitle'><{$album.name}><p>
                                <{/if}>
                            <{/if}>
                            <{if isset($ba_showDesc)}>
                                <{if $album.desc_limited}>
                                    <p class='wgg-block-adesc'><{$album.desc_limited}><p>
                                <{else}>
                                    <p class='wgg-block-adesc'><{$album.desc}><p>
                                <{/if}>
                            <{/if}>
                            </a>
                        <{/if}>
                    </div>
                <{/if}>
            <{/if}>
        </div>
        <{if $album.linebreak}>
            <div class='clear'>&nbsp;</div>
        <{/if}>
        <{if $album.linebreak}></div><{/if}>
    <{/foreach}>
    <div class="clear"></div>
    <{if isset($show_more_albums)}>
        <div class="wgg-b-album-more center">
            <a class='btn btn-primary wg-color1' href='<{$wggallery_url}>/index.php' title='<{$smarty.const._CO_WGGALLERY_ALBUMS_SHOW}>'><{$smarty.const._CO_WGGALLERY_ALBUMS_SHOW}></a>
        </div>
    <{/if}>
<{/if}>
