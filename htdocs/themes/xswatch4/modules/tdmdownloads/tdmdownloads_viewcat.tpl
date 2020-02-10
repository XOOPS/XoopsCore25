<div class="tdmdownloads">
    <div class="breadcrumb"><{$category_path|replace:'<img src="assets/images/deco/arrow.gif" alt="arrow">':'&nbsp;/&nbsp;'}></div>

    <div>
        <{if false!==$category.title|default:false}>
        <h3><{$category.title}></h3>
        <{/if}>
        <p>
            <{$cat_description}>
        </p>
    </div>

    <div class="row">
    <{if $category.image|default:'' != ""}>
        <div class="col-6">
        <a title="<{$category.title}>" href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>">
            <img class="img-fluid" src="<{$category.image}>" alt="<{$category.title}>">
        </a>
        </div>
    <{/if}>

    <div class="col-6">
    <{if !empty($subcategories)}>
    <h5><{$smarty.const._MD_TDMDOWNLOADS_INDEX_SCAT}></h5>
    <div class="list-group">
        <{foreach item=subcategory from=$subcategories}>
        <a href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$subcategory.id}>"
           class="list-group-item list-group-item-action"><{$subcategory.title}> <span class="badge badge-secondary badge-pill"><{$subcategory.totaldownloads}></span>
        </a>
        <{/foreach}>
    </div>
    <{/if}>

        <br><a class="btn btn-warning" title="<{$smarty.const._MD_TDMDOWNLOADS_RSS}>" href="<{$xoops_url}>/modules/tdmdownloads/rss.php?cid=<{$category_id}>">
            <span class="fa fa-fw fa-rss"></span>
        </a>
    </div>
    </div>
        <div class="tdm-downloads-info row">
            <{if $bl_affichage==1}>
                <div class="col-md-12"><h2><{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLNAME}>:</h1></div>
                <div class="col-sm-4 col-md-4">
                <{if $bl_date != ""}>
                    <h3 class="tdm-title"><span class="fa fa-calendar"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLDATE}></h3>
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
                        <h3 class="tdm-title"><span class="fa fa-star"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLPOP}></h3>
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
                        <h3 class="tdm-title"><span class="fa fa-thumbs-o-up"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLRATING}></h3>
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
                        <span class="fa fa-chevron-up"></span>
                    </a>

                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_TITLEZTOA}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=title&amp;order=DESC">
                        <span class="fa fa-chevron-down"></span>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <{$smarty.const._MD_TDMDOWNLOADS_CAT_DATE}>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_DATEOLD}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=date&amp;order=ASC">
                        <span class="fa fa-chevron-up"></span>
                    </a>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_DATENEW}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=date&amp;order=DESC">
                        <span class="fa fa-chevron-down"></span>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <{$smarty.const._MD_TDMDOWNLOADS_CAT_RATING}>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_RATINGLTOH}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=rating&amp;order=ASC">
                        <span class="fa fa-chevron-up"></span>
                    </a>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_RATINGHTOL}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=rating&amp;order=DESC">
                        <span class="fa fa-chevron-down"></span>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <{$smarty.const._MD_TDMDOWNLOADS_CAT_POPULARITY}>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_POPULARITYLTOM}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=hits&amp;order=ASC">
                        <span class="fa fa-chevron-up"></span>
                    </a>
                    <a title="<{$smarty.const._MD_TDMDOWNLOADS_CAT_POPULARITYMTOL}>"
                       href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category_id}>&amp;sort=hits&amp;order=DESC">
                        <span class="fa fa-chevron-down"></span>
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12"><p class="text-center text-muted"><{$affichage_tri}></p></div>
            <{/if}>
        </div><!-- .tdm-order-by -->

        <{if $file != ""}>
            <h3 class="tdm-title"><{$smarty.const._MD_TDMDOWNLOADS_CAT_LIST}>:</h3>
            <div class="row">
            <{section name=i loop=$file}><{include file="db:tdmdownloads_download.tpl" down=$file[i]}><{/section}>
            </div>
            <{if $pagenav != ''}>
                <div class="generic-pagination col text-right mt-2">
                <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
                </div>
            <{/if}>
        <{/if}>
</div><!-- .tdmdownloads -->

<{include file="db:system_notification_select.tpl"}>
