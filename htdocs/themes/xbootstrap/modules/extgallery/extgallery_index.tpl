<div class="row">
    <div class="col-md-12"><a title="<{$extgalleryName}>" href="<{xoAppUrl modules/extgallery/}>"><h4><{$extgalleryName}></h4></a></div>
    <div class="col-xs-4 col-sm-4 col-md-4"><{$lang.categoriesAlbums}></div>
    <!-- <div class="col-xs-4 col-sm-4 col-md-4"><{$lang.nbAlbums}></div> -->
    <!-- <div class="col-xs-4 col-sm-4 col-md-4"><{$lang.nbPhotos}></div> -->
</div>

<div id="xoopsgrid" class="row">
    <{foreach item=child from=$cats}>
    <div class="col-xs-6 col-sm-4 col-md-4 xoops-gallery-thumb">
        <{if $disp_cat_img == 1 }>
            <{if $child.cat_imgurl != "" }>
                <{if $child.cat_isalbum}>
                    <a title="<{$child.cat_name}>" href="<{xoAppUrl modules/extgallery/}>public-<{$display_type}>.php?id=<{$child.cat_id}>">
                        <img src="<{$child.cat_imgurl}>" alt="<{$child.cat_name}>">
                    </a>
                <{else}>
                    <a title="<{$child.cat_name}>" href="<{xoAppUrl modules/extgallery/}>public-categories.php?id=<{$child.cat_id}>">
                        <img src="<{$child.cat_imgurl}>" alt="<{$child.cat_name}>">
                    </a>
                <{/if}>
            <{else}>
                <{if $child.photo}>

                    <{if $child.cat_isalbum}>
                        <a title="<{$child.cat_name}>" href="<{xoAppUrl modules/extgallery/}>public-<{$display_type}>.php?id=<{$child.cat_id}>">
                            <img src="<{$xoops_url}>/uploads/extgallery/public-photo/thumb/thumb_<{$child.photo.photo_name}>" alt="<{$child.photo.photo_title}>">
                        </a>
                    <{else}>
                        <a title="<{$child.cat_name}>" href="pu<{$extgalleryName}>blic-categories.php?id=<{$child.cat_id}>">
                            <img src="<{$xoops_url}>/uploads/extgallery/public-photo/thumb/thumb_<{$child.photo.photo_name}>" alt="<{$child.photo.photo_title}>">
                        </a>
                    <{/if}>
                <{/if}>
            <{/if}>
        <{else}>

            <{if $child.photo}>
                <{if $child.cat_isalbum}>
                    <a title="<{$child.cat_name}>" href="<{xoAppUrl modules/extgallery/}>public-<{$display_type}>.php?id=<{$child.cat_id}>">
                        <img src="<{$xoops_url}>/uploads/extgallery/public-photo/thumb/thumb_<{$child.photo.photo_name}>" alt="<{$child.photo.photo_title}>">
                    </a>
                <{else}>
                    <a title="<{$child.cat_name}>" href="<{xoAppUrl modules/extgallery/}>public-categories.php?id=<{$child.cat_id}>">
                        <img src="<{$xoops_url}>/uploads/extgallery/public-photo/thumb/thumb_<{$child.photo.photo_name}>" alt="<{$child.photo.photo_title}>">
                    </a>
                <{/if}>
            <{/if}>
        <{/if}>

        <{if $child.cat_isalbum}>
            <a title="<{$child.cat_name}>" href="<{xoAppUrl modules/extgallery/}>public-<{$display_type}>.php?id=<{$child.cat_id}>"
               class="extgallery-album-name">
                <strong><{$child.cat_name}></strong>
            </a>
            <{$child.cat_desc}>

        <{else}>
            <a title="<{$child.cat_name}>" href="<{xoAppUrl modules/extgallery/}>public-categories.php?id=<{$child.cat_id}>">
                <{$child.cat_name}>
            </a>
            <{$child.cat_desc}>
        <{/if}>

        <{if $child.cat_nb_album != 0}>
            <{$child.cat_nb_album}>
        <{/if}>

        <strong class="number-photos"><{$child.cat_nb_photo}> <{$lang.nbPhotos}></strong>
    </div>
    <{/foreach}>
</div>

<{if $show_rss}>
    <div class="row">
        <div class="col-md-12">
            <a href="<{xoAppUrl modules/extgallery/public-rss.php}>" title="<{$smarty.const._MD_EXTGALLERY_RSS}>">
                <img src="<{xoAppUrl modules/extgallery/assets/images/feed.png}>" alt="<{$smarty.const._MD_EXTGALLERY_RSS}>">
            </a>
        </div>
    </div>
<{/if}>

<{include file='db:system_notification_select.tpl'}>
