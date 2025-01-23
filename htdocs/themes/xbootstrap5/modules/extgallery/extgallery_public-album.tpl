<div class="extGalleryAlbum">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li><a title="<{$extgalleryName}>" href="<{xoAppUrl 'modules/extgallery/'}>"><{$extgalleryName}></a></li>
                <{foreach item=node from=$catPath|default:null name=breadcrumb}>
                <li><a title="<{$node.cat_name}>" href="<{xoAppUrl 'modules/extgallery/'}>public-categories.php?id=<{$node.cat_id}>"><{$node.cat_name}></a></li>
                <{/foreach}>
                <li><{$cat.cat_name}></li>
            </ul>
        </div>

        <div class="col-md-12 aligncenter">
            <h3 class="gallerytitle"><{$extgallerySortbyOrderby}></h3>
        </div>

        <div class="col-md-12 aligncenter orderbyicons">
            <ul class="list-unstyled">
                <li>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_date&orderby=DESC" title="<{$smarty.const._MD_EXTGALLERY_SORTDATEDESC}>">
                        <span class="fa fa-collapse-up"></span>
                    </a>
                    <span><{$smarty.const._MD_EXTGALLERY_SORTDATE}></span>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_date&orderby=ASC" title="<{$smarty.const._MD_EXTGALLERY_SORTDATEASC}>">
                        <span class="fa fa-collapse-down"></span>
                    </a>
                </li>

                <li>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_title&orderby=ASC" title="<{$smarty.const._MD_EXTGALLERY_SORTNAMEASC}>">
                        <span class="fa fa-collapse-up"></span>
                    </a>
                    <span><{$smarty.const._MD_EXTGALLERY_SORTNAME}></span>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_title&orderby=DESC" title="<{$smarty.const._MD_EXTGALLERY_SORTNAMEDESC}>">
                        <span class="fa fa-collapse-down"></span>
                    </a>
                </li>

                <li>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_hits&orderby=DESC" title="<{$smarty.const._MD_EXTGALLERY_SORTHITSDESC}>">
                        <span class="fa fa-collapse-up"></span>
                    </a>
                    <span><{$smarty.const._MD_EXTGALLERY_SORTHITS}></span>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_hits&orderby=ASC" title="<{$smarty.const._MD_EXTGALLERY_SORTHITSASC}>">
                        <span class="fa fa-collapse-down"></span>
                    </a>
                </li>

                <li>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_rating&orderby=DESC" title="<{$smarty.const._MD_EXTGALLERY_SORTNOTEDESC}>">
                        <span class="fa fa-collapse-up"></span>
                    </a>
                    <span><{$smarty.const._MD_EXTGALLERY_SORTNOTE}></span>
                    <a href="<{xoAppUrl 'modules/extgallery/'}>public-album.php?id=<{$extgalleryID}>&start=<{$extgalleryStart}>&sortby=photo_rating&orderby=ASC" title="<{$smarty.const._MD_EXTGALLERY_SORTNOTEASC}>">
                        <span class="fa fa-collapse-down"></span>
                    </a>
                </li>
            </ul>
        </div><!-- .orderbyicons -->
    </div>


    <h3 class="gallerytitle"><{$cat.cat_name}></h3>

    <div id="xoopsgrid">

        <{section name=photo loop=$photos}>
            <div class="col-xs-6 col-sm-6 col-md-4 album-thumb">

                <{if $photos[photo].photo_id}>
                    <ul class="adminlinks list-unstyled">
                        <{if isset($xoops_isadmin)}>
                            <li><a title="edit" href="<{xoAppUrl 'modules/extgallery/'}>public-modify.php?op=edit&id=<{$photos[photo].photo_id}>"><span class="fa fa-pencil-square-o"></span></a></li>
                            <li><a title="delete" href="<{xoAppUrl 'modules/extgallery/'}>public-modify.php?op=delete&id=<{$photos[photo].photo_id}>"><span class="fa fa-trash"></span></a></li>
                        <{/if}>

                        <{if isset($enable_show_comments)}>
                            <li><{$photos[photo].photo_comment}> <{$lang.comments}></li>
                        <{/if}>
                        <{if isset($enable_photo_hits)}>
                            <li><{$photos[photo].photo_hits}> <{$lang.hits}></li>
                        <{/if}>
                        <{if isset($enable_date)}>
                            <li><span class="fa fa-calendar"></span> <{$photos[photo].photo_date}></li>
                        <{/if}>
                        <{if isset($enable_submitter_lnk)}>
                            <li><a title="<{$photos[photo].user.uname}>" href="<{$xoops_url}>/userinfo.php?uid=<{$photos[photo].user.uid}>"><{$photos[photo].user.uname}></a>
                            </li>
                        <{/if}>
                    </ul>
                    <{if $photos[photo].photo_serveur && $photos[photo].photo_name}>
                        <a href="<{xoAppUrl 'modules/extgallery/'}>public-photo.php?photoId=<{$photos[photo].photo_id}>" title="<{$photos[photo].photo_title}>">
                            <img src="<{$photos[photo].photo_serveur}>thumb_<{$photos[photo].photo_name}>" alt="<{$photos[photo].photo_title}>">
                        </a>
                    <{elseif $photos[photo].photo_name}>
                        <a href="<{xoAppUrl 'modules/extgallery/'}>public-photo.php?photoId=<{$photos[photo].photo_id}>" title="<{$photos[photo].photo_title}>">
                            <img src="<{$xoops_url}>/uploads/extgallery/public-photo/thumb/thumb_<{$photos[photo].photo_name}>" alt="<{$photos[photo].photo_title}>">
                        </a>
                    <{/if}>

                    <{if isset($enableRating)}>
                        <div class="photoRating"><img src="<{xoAppUrl 'modules/extgallery/'}>assets/images/rating_<{$photos[photo].photo_rating}>.gif" alt="<{$lang.rate_score}> : <{$photos[photo].photo_rating}>" title="<{$lang.rate_score}>"></div>
                    <{/if}>

                    <{foreach item=pluginLink from=$photos[photo].link|default:null}>
                    <a href="<{$pluginLink.link}><{$photos[photo].photo_id}>" title="<{$pluginLink.name}>"><{$pluginLink.name}></a>
                <{/foreach}>

                <{/if}>

            </div>
        <{/section}>
    </div>

</div><!-- .extGalleryAlbum -->

<div class="pageNav">
    <{$pageNav}>
</div>

<{if isset($show_rss)}>
    <div id="rss">
        <a href="<{xoAppUrl 'modules/extgallery/public-rss.php?id='}><{$extgalleryID}>" title="<{$smarty.const._MD_EXTGALLERY_ALBUMRSS}>">
            <img src="<{xoAppUrl 'modules/extgallery/assets/images/feedblue.png'}>" alt="<{$smarty.const._MD_EXTGALLERY_ALBUMRSS}>">
        </a>
        <a href="<{xoAppUrl 'modules/extgallery/public-rss.php'}>" title="<{$smarty.const._MD_EXTGALLERY_RSS}>">
            <img src="<{xoAppUrl 'modules/extgallery/assets/images/feed.png'}>" alt="<{$smarty.const._MD_EXTGALLERY_RSS}>">
        </a>
    </div>
<{/if}>

<{include file='db:system_notification_select.tpl'}>
