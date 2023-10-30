<div class="tdmdownloads">
    <{if count($categories) > 0}>

    <div class="tdm-category row">
        <{foreach item=category from=$categories|default:null}>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 tdm-category-list">
                <a class="btn btn-primary btn-md btn-block" title="<{$category.title}>"
                   href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>">
                    <{$category.title}>
                </a>

                <a title="<{$category.title}>" href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>" class="tdm-category-image">
                    <img class="<{$img_float}>" src="<{$category.image}>" alt="<{$category.title}>">
                </a>

                <!-- Category Description -->
                <div class="aligncenter">
                    <{if !empty($category.description_main)}>
                        <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#tdmDesc-<{$category.id}>">+</button>
                    <{else}>
                        <button class="btn btn-xs disabled" data-toggle="modal">+</button>
                    <{/if}>
                </div>

                <div class="modal fade" id="tdmDesc-<{$category.id}>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header"><h4 class="modal-title aligncenter"><{$category.title}></h4></div>
                            <div class="modal-body">
                                <{$category.description_main}>
                            </div>
                            <div class="modal-footer">
                                <a title="<{$category.title}>" href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>"
                                   class="pull-left btn btn-success">
                                    There are <strong><{$category.totaldownloads}></strong> files in this category!
                                </a>
                                <button type="button" class="btn btn-default" data-dismiss="modal">&times;</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Category Description -->

                <{if !empty($category.subcategories)}>
                    <{$smarty.const._MD_TDMDOWNLOADS_INDEX_SCAT}>
                    <ul><{$category.subcategories}></ul>
                <{/if}>
            </div>
            <!-- .tdm-category-list -->
        <{/foreach}>
    </div><!-- .tdm-category -->

    <div class="aligncenter">
        <a title="<{$smarty.const._MD_TDMDOWNLOADS_RSS}>" href="<{$xoops_url}>/modules/tdmdownloads/rss.php?cid=0">
            <img src="assets/images/rss.gif" alt="<{$smarty.const._MD_TDMDOWNLOADS_RSS}>">
        </a>
    </div>

    <div class="tdm-downloads-info row">
        <{if isset($bl_affichage) && $bl_affichage == 1}>
            <div class="col-md-12"><h3><{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLNAME}>:</h3></div>
            <{if !empty($bl_date)}>
                <div class="col-sm-4 col-md-4">
                    <h3 class="tdm-title"><span class="glyphicon glyphicon-calendar"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLDATE}></h3>
                    <ul class="list-unstyled">
                        <{foreach item=bl_dateitem from=$bl_date|default:null}>
                            <li>
                                <a title="<{$bl_dateitem.title}>"
                                   href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$bl_dateitem.cid}>&amp;lid=<{$bl_dateitem.id}>"><{$bl_dateitem.title}></a>
                                (<{$bl_dateitem.date}>)
                            </li>
                        <{/foreach}>
                    </ul>
                </div>
            <{/if}>

            <{if !empty($bl_pop)}>
                <div class="col-sm-4 col-md-4">
                    <h3 class="tdm-title"><span class="glyphicon glyphicon-star"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLPOP}></h3>
                    <ul class="list-unstyled">
                        <{foreach item=bl_popitem from=$bl_pop|default:null}>
                            <li>
                                <a title="<{$bl_popitem.title}>" href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$bl_popitem.cid}>&amp;lid=<{$bl_popitem.id}>"><{$bl_popitem.title}></a>
                                (<{$bl_popitem.hits}>)
                            </li>
                        <{/foreach}>
                    </ul>
                </div>
            <{/if}>

            <{if !empty($bl_rating)}>
                <div class="col-sm-4 col-md-4">
                    <h3 class="tdm-title"><span class="glyphicon glyphicon-thumbs-up"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLRATING}></h3>
                    <ul class="list-unstyled">
                        <{foreach item=bl_ratingitem from=$bl_rating|default:null}>
                            <li>
                                <a title="<{$bl_ratingitem.title}>"
                                   href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$bl_ratingitem.cid}>&amp;lid=<{$bl_ratingitem.id}>"><{$bl_ratingitem.title}></a>
                                (<{$bl_ratingitem.rating}>)
                            </li>
                        <{/foreach}>
                    </ul>
                </div>
            <{/if}>

        <{/if}>

        <div class="clearfix"></div>
        <div class="col-md-12">
            <h4 class="aligncenter"><{$lang_thereare}></h4>
        </div>

        <{/if}>
    </div><!-- .downloads-info -->

    <{if isset($show_latest_files)}>
        <div class="row">
            <{if !empty($file)}>
                <div class="col-md-12">
                    <h1><{$smarty.const._MD_TDMDOWNLOADS_INDEX_LATESTLIST}>:</h1>
                </div>
                <{section name=i loop=$file}>
                    <{include file="db:tdmdownloads_download.tpl" down=$file[i]}>
                <{/section}>
            <{/if}>
        </div>
    <{/if}>

</div><!-- .tdmdownloads -->

<{include file="db:system_notification_select.tpl"}>
