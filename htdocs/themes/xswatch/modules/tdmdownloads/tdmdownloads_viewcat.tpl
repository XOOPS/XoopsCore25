<div class="tdmdownloads">
    <div class="breadcrumb"><{$category_path}></div>

    <{if $cat_description != ""}>
        <blockquote>
            <small><{$cat_description}></small>
        </blockquote>
    <{/if}>

    <{foreach item=category from=$subcategories}>
    <a title="<{$category.title}>" href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>"><{$category.title}></a>
    <a title="<{$category.title}>" href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>"><{$category.totaldownloads}></a>

    <{if $category.image != ""}>
        <a title="<{$category.title}>" href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>">
            <img class="<{$img_float}>" src="<{$category.image}>" alt="<{$category.title}>">
        </a>
    <{/if}>

    <{$category.description_main}>

    <{if $category.subcategories != ""}>
    <{$smarty.const._MD_TDMDOWNLOADS_INDEX_SCAT}>
    <ul><{$category.subcategories}>
        <{/if}>
        <{/foreach}>

        <a title="<{$smarty.const._MD_TDMDOWNLOADS_RSS}>" href="<{$xoops_url}>/modules/tdmdownloads/rss.php?cid=<{$category_id}>">
            <img src="images/rss.gif" alt="<{$smarty.const._MD_TDMDOWNLOADS_RSS}>">
        </a>

        <div class="tdm-downloads-info row">
            <{if $bl_affichage==1}>
                <div class="col-md-12"><h2><{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLNAME}>:</h1></div>
                <div class="col-sm-4 col-md-4">
                <{if $bl_date != ""}>
                    <h3 class="tdm-title"><span class="glyphicon glyphicon-calendar"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLDATE}></h3>
                    <ul class="list-unstyled">
                        <{foreach item=bl_date from=$bl_date}>
                            <li>
                                <a title="<{$bl_date.title}>"
                                   href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$bl_date.cid}>&amp;lid=<{$bl_date.id}>"><{$bl_date.title}></a>
                                (<{$bl_date.date}>)
                            </li>
                        <{/foreach}>
                    </ul>
                    </div>
                <{/if}>
                <{if $bl_pop != ""}>
                    <div class="col-sm-4 col-md-4">
                        <h3 class="tdm-title"><span class="glyphicon glyphicon-star"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLPOP}></h3>
                        <ul class="list-unstyled">
                            <{foreach item=bl_pop from=$bl_pop}>
                                <li>
                                    <a title="<{$bl_pop.title}>"
                                       href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$bl_pop.cid}>&amp;lid=<{$bl_pop.id}>"><{$bl_pop.title}></a>
                                    (<{$bl_pop.hits}>)
                                </li>
                            <{/foreach}>
                        </ul>
                    </div>
                <{/if}>
                <{if $bl_rating != ""}>
                    <div class="col-sm-4 col-md-4">
                        <h3 class="tdm-title"><span class="glyphicon glyphicon-thumbs-up"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLRATING}></h3>
                        <ul class="list-unstyled">
                            <{foreach item=bl_rating from=$bl_rating}>
                                <li>
                                    <a title="<{$bl_rating.title}>"
                                       href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$bl_rating.cid}>&amp;lid=<{$bl_rating.id}>"><{$bl_rating.title}></a>
                                    (<{$bl_rating.rating}>)
                                </li>
                            <{/foreach}>
                        </ul>
                    </div>
                <{/if}>
            <{/if}>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <p class="text-muted text-right">
                    <small><em><{$lang_thereare}></em></small>
                </p>
            </div>
        </div><!-- .downloads-info -->

        <div class="row order-by">
            <{if $navigation == true}>
                <div class="col-md-12"><h3 class="tdm-title"><{$smarty.const._MD_TDMDOWNLOADS_CAT_SORTBY}></h3></div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <{$smarty.const._MD_TDMDOWNLOADS_CAT_TITLE}>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_TITLEATOZ}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=title&amp;order=ASC">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>

                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_TITLEZTOA}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=title&amp;order=DESC">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <{$smarty.const._MD_TDMDOWNLOADS_CAT_DATE}>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_DATEOLD}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=date&amp;order=ASC">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_DATENEW}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=date&amp;order=DESC">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <{$smarty.const._MD_TDMDOWNLOADS_CAT_RATING}>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_RATINGLTOH}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=rating&amp;order=ASC">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_RATINGHTOL}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=rating&amp;order=DESC">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <{$smarty.const._MD_TDMDOWNLOADS_CAT_POPULARITY}>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_POPULARITYLTOM}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=hits&amp;order=ASC">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_POPULARITYMTOL}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=hits&amp;order=DESC">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12"><p class="text-center text-muted"><{$affichage_tri}></p></div>
            <{/if}>
        </div><!-- .tdm-order-by -->

        <{if $file != ""}>
            <h3 class="tdm-title"><{$smarty.const._MD_TDMDOWNLOADS_CAT_LIST}>:</h3>
            <{section name=i loop=$file}><{include file="db:tdmdownloads_download.tpl" down=$file[i]}><{/section}>
            <{if $pagenav != ''}><{$pagenav}><{/if}>
        <{/if}>
</div><!-- .tdmdownloads -->

<{include file="db:system_notification_select.tpl"}>
