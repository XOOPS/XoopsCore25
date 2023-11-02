<{if isset($images_list)}>
    <div id="wgBlockImagesCarouselSlides" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <{assign var=active value=' active'}>
            <{foreach item=image from=$images_list|default:null}>
            <div class="carousel-item<{$active}>">
                <a href="<{$wggallery_url}>/images.php?op=show&amp;img_id=<{$image.id}>&amp;alb_id=<{$image.albid}>&alb_pid=<{$image.albpid}>" title="<{$smarty.const._CO_WGGALLERY_IMAGE_SHOW}>" target="<{$image_target}>">
                <img class="img-fluid wgg-album-img center" src="<{$image.medium}>" alt="<{$image.title}>">
                </a>
                <div class="carousel-caption">
                    <{if isset($bi_showTitle)}>
                        <{if $image.title_limited}>
                            <p class="wgg-block-ititle slidetext-trans center"><{$image.title_limited}><p>
                        <{else}>
                            <p class="wgg-block-ititle slidetext-trans center"><{$image.title}><p>
                        <{/if}>
                    <{/if}>
                </div>
            </div>
            <{assign var=active value=''}>
            <{/foreach}>
            <a class="carousel-control-prev" href="#wgBlockImagesCarouselSlides" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only"<{$smarty.const.THEME_CONTROL_PREVIOUS}>/span>
            </a>
            <a class="carousel-control-next" href="#wgBlockImagesCarouselSlides" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only"<{$smarty.const.THEME_CONTROL_NEXT}>/span>
            </a>
        </div>
    </div>
    <{if isset($show_more_images)}>
    <div class="wgg-b-album-more center">
        <a class="btn wgfxg-more-btn" href="<{$wggallery_url}>/index.php" title="<{$smarty.const._CO_WGGALLERY_ALBUMS_SHOW}>"><{$smarty.const._CO_WGGALLERY_ALBUMS_SHOW}></a>
    </div>
    <{/if}>
<{/if}>
