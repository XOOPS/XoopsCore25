<div class="row extGalleryAlbum">
    <div class="col-md-12">
        <ul class="breadcrumb">
            <li><a title="<{$extgalleryName}>" href="<{xoAppUrl modules/extgallery/}>"><{$extgalleryName}></a></li>

            <{foreachq item=node from=$catPath name=breadcrumb}>
            <li>
                <a href="<{xoAppUrl modules/extgallery/}>public-<{if $node.cat_isalbum}><{$display_type}><{else}>categories<{/if}>.php?id=<{$node.cat_id}>"
                   title="<{$node.cat_name}>"><{$node.cat_name}></a></li>
            <{/foreach}>
            <li><{$photo.photo_title}></li>
        </ul>
    </div>

    <div class="col-md-12 aligncenter gallery-single-photo">
        <!-- Start Admin link -->
        <div class="row photo-nav-controls">
            <div class="col-xs-6 col-sm-6 col-md-6">
                <{if $xoops_isadmin}>
                    <div class="pull-left">
                        <a title="edit" class="btn btn-success btn-xs"
                           href="<{xoAppUrl modules/extgallery/}>public-modify.php?op=edit&id=<{$photo.photo_id}>"><span
                                    class="glyphicon glyphicon-edit"></span></a>
                        <a title="delete" class="btn btn-danger btn-xs"
                           href="<{xoAppUrl modules/extgallery/}>public-modify.php?op=delete&id=<{$photo.photo_id}>"><span
                                    class="glyphicon glyphicon-trash"></span></a>
                    </div>
                <{/if}>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-6">
                <ul class="pull-right list-unstyled">
                    <{if $prevId != 0}>
                        <li class="nav-btn"><a href="<{xoAppUrl modules/extgallery/}>public-photo.php?photoId=<{$prevId}>"><span
                                        class="glyphicon glyphicon-circle-arrow-left"></span></a><{else}>
                        </li>
                    <{/if}>
                    <li class="small"><{$currentPhoto}></li>
                    <li class="small"><{$lang.of}></li>
                    <li class="small"><{$totalPhoto}></li>
                    <{if $nextId != 0}>
                        <li class="nav-btn"><a href="<{xoAppUrl modules/extgallery/}>public-photo.php?photoId=<{$nextId}>"><span
                                        class="glyphicon glyphicon-circle-arrow-right"></span></a><{else}>
                        </li>
                    <{/if}>
                </ul>
            </div>
        </div><!-- .row || .photo-nav-controls -->

        <{if $photo.photo_serveur}>
            <img src="<{$photo.photo_serveur}><{$photo.photo_name}>" alt="<{$photo.photo_desc}>" class="img-thumbnail">
        <{else}>
            <img src="<{$xoops_url}>/uploads/extgallery/public-photo/medium/<{$photo.photo_name}>" alt="<{$photo.photo_title}>" class="img-thumbnail">
        <{/if}>
    </div><!-- .gallery-single-photo -->

    <div class="row gallery-image-details">
        <{if $disp_ph_title == 1 }>
            <!-- Start Photo Title -->
            <div class="col-md-12">
                <h3 class="gallerytitle"><{$photo.photo_title}></h3>
            </div>
        <{/if}>

        <!-- Start Photo desc -->
        <div class="col-md-12">
            <{$photo.photo_desc}>
        </div>

        <{if $enableExtra}>
            <!-- Start extra field -->
            <div class="col-md-12">
                <{$photo.photo_extra}>
            </div>
        <{/if}>

        <{foreachq item=pluginLink from=$pluginPhotoAlbumLink}>
        <a href="<{$pluginLink.link}><{$photo.photo_id}>" title="<{$pluginLink.name}>"><{$pluginLink.name}></a>
        <{/foreach}>
        <{foreachq item=pluginLink from=$photo.link}>
        <a href="<{$pluginLink.link}><{$photo.photo_id}>" title="<{$pluginLink.name}>"><{$pluginLink.name}></a>
        <{/foreach}>

        <!-- Start XOOPS Tag -->
        <{if $tags}>
            <div class="col-md-12">
                <{include file="db:tag_bar.tpl"}>
            </div>
        <{/if}>

        <!-- Start social network and bookmarks -->
        <div class="col-md-12 aligncenter">
            <div class='shareaholic-canvas' data-app='share_buttons' data-app-id=''></div>
        </div>
    </div><!-- .gallery-image-details -->

    <!-- Start Rating part -->
    <{if $canRate}>
        <div class="col-md-12">
            <h3 class="gallerytitle"><{$lang.voteFor}></h3>

            <div class="aligncenter">
                <a title="<{$lang.voteFor}> : 1" href="<{xoAppUrl modules/extgallery/}>public-rating.php?id=<{$photo.photo_id}>&amp;rate=1"><img src="assets/images/rating_1.gif" alt="<{$lang.voteFor}> : 1"></a>
                <a title="<{$lang.voteFor}> : 2" href="<{xoAppUrl modules/extgallery/}>public-rating.php?id=<{$photo.photo_id}>&amp;rate=2"><img src="assets/images/rating_2.gif" alt="<{$lang.voteFor}> : 2"></a>
                <a title="<{$lang.voteFor}> : 3" href="<{xoAppUrl modules/extgallery/}>public-rating.php?id=<{$photo.photo_id}>&amp;rate=3"><img src="assets/images/rating_3.gif" alt="<{$lang.voteFor}> : 3"></a>
                <a title="<{$lang.voteFor}> : 4" href="<{xoAppUrl modules/extgallery/}>public-rating.php?id=<{$photo.photo_id}>&amp;rate=4"><img src="assets/images/rating_4.gif" alt="<{$lang.voteFor}> : 4"></a>
                <a title="<{$lang.voteFor}> : 5" href="<{xoAppUrl modules/extgallery/}>public-rating.php?id=<{$photo.photo_id}>&amp;rate=5"><img src="assets/images/rating_5.gif" alt="<{$lang.voteFor}> : 5"></a>
            </div>
        </div>
    <{/if}>

    <!-- Start Photo Information -->
    <{if $enable_info }>
        <div class="text-center">
            <a href="#gallery-info" data-toggle="collapse" class="big-info-icon-link" title="Info"><span class="glyphicon glyphicon-info-sign"></span></a>
        </div>
        <div class="panel-collapse collapse" id="gallery-info">

            <h3 class="gallerytitle aligncenter"><{$lang.photoInfo}></h3>
            <ul class="list-unstyled photo-info">
                <{if $enable_submitter_lnk}>
                    <li><{$lang.submitter}> : <a title="<{$photo.user.uname}>" href="<{$xoops_url}>/userinfo.php?uid=<{$photo.user.uid}>"><{$photo.user.uname}></a>
                        <a title="<{$lang.allPhotoBy}> <{$photo.user.uname}>"
                           href="<{xoAppUrl modules/extgallery/}>public-useralbum.php?id=<{$photo.user.uid}>">
                            <{$lang.allPhotoBy}> <{$photo.user.uname}>
                        </a>
                    </li>
                <{/if}>

                <{if $enable_photo_hits}>
                    <li><{$lang.view}> : <{$photo.photo_hits}> <{$lang.hits}></li>
                <{/if}>

                <{if $enable_resolution}>
                    <li><{$lang.resolution}> : <{$photo.photo_res_x}> x <{$photo.photo_res_y}> <{$lang.pixels}> | <{$lang.fileSize}> : <{$photo.photo_size}>
                        Kb
                    </li>
                <{/if}>

                <{if $enable_date}>
                    <li><{$lang.added}> : <{$photo.photo_date}></li>
                <{/if}>

                <{if $canRate}>
                    <li><{$lang.score}> : <img src="assets/images/rating_<{$rating}>.gif" alt="rating"> | <{$photo.photo_nbrating}> <{$lang.votes}></li>
                <{/if}>

                <{if $canDownload && $enable_download}>
                    <li><a title="<{$lang.downloadOrig}>" href="<{xoAppUrl modules/extgallery/}>public-download.php?id=<{$photo.photo_id}>"><{$lang.downloadOrig}><img
                                    src="assets/images/download.gif" alt="<{$lang.downloadOrig}>"></a> | <{$photo.photo_download}> <{$lang.donwloads}>
                    </li>
                <{/if}>

                <{if $canSendEcard && $enable_ecards}>
                    <li><a title="<{$lang.sendEcard}>" href="<{xoAppUrl modules/extgallery/}>public-sendecard.php?id=<{$photo.photo_id}>"><{$lang.sendEcard}>
                            <img src="assets/images/ecard.gif" alt="<{$lang.sendEcard}>"></a> | <{$photo.photo_ecard}> <{$lang.sends}>
                    </li>
                <{/if}>
            </ul>
        </div>
    <{/if}>

    <{if $show_rss}>
        <div id="rss">
            <a href="<{xoAppUrl modules/extgallery/public-rss.php}>" title="<{$smarty.const._MD_EXTGALLERY_RSS}>">
                <img src="<{xoAppUrl modules/extgallery/assets/images/feed.png}>" alt="<{$smarty.const._MD_EXTGALLERY_RSS}>"/>
            </a>
        </div>
    <{/if}>

</div><!-- .row || .extGalleryAlbum -->

<{$commentsnav}>

<{$lang_notice}>

<{if $comment_mode == "flat"}>
    <{include file="db:system_comments_flat.tpl"}>
<{elseif $comment_mode == "thread"}>
    <{include file="db:system_comments_thread.tpl"}>
<{elseif $comment_mode == "nest"}>
    <{include file="db:system_comments_nest.tpl"}>
<{/if}>

<{include file='db:system_notification_select.tpl'}>
