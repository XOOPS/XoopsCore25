<div class="tdmdownloads">
    <div class="breadcrumb"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/"><{$xoops_pagetitle}></a></div>
    <{if count($categories) gt 0}>
    <div class="tdm-category row">
        <{foreach item=category from=$categories}>
        <div class="card col-8 col-sm-6 col-md-4 col-xl-3">
            <h5 class="card-title"><{$category.title}></h5>
            <{if !empty($category.image)}>
            <img class="card-img-top img-fluid" src="<{$category.image}>" alt="<{$category.title}>">
            <{/if}>
            <div class="card-body">
                <p class="card-text text-muted"><span class="fa fa-file-o"></span> <{$category.totaldownloads}></p>
                <p class="card-text"><{$category.description_main|default:''|truncateHtml:20:'...'}>
                    <a class="stretched-link"title="<{$category.title}>" href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$category.id}>"><span class="fa fa-forward"></span></a>
                </p>
            </div>
        </div>
        <{/foreach}>
    </div><!-- .tdm-category -->
    <div>
        <a class="btn btn-warning" title="<{$smarty.const._MD_TDMDOWNLOADS_RSS}>" href="<{$xoops_url}>/modules/tdmdownloads/rss.php?cid=<{$category_id}>">
            <span class="fa fa-fw fa-rss"></span>
        </a>
    </div>

    <div class="tdm-downloads-info row">
        <{if $bl_affichage==1}>
            <div class="col-md-12"><h3><{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLNAME}>:</h3></div>
            <{if $bl_date != ""}>
                <div class="col-sm-4 col-md-4">
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
                                <a title="<{$bl_pop.title}>" href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$bl_pop.cid}>&amp;lid=<{$bl_pop.id}>"><{$bl_pop.title}></a>
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
            <h4 class="aligncenter"><{$lang_thereare}></h4>
        </div>

        <{/if}>
    </div><!-- .downloads-info -->

    <{if $show_latest_files}>
            <{if $file != ""}>
            <h1><{$smarty.const._MD_TDMDOWNLOADS_INDEX_LATESTLIST}>:</h1>
            <div class="row">
                <{section name=i loop=$file}>
                    <{include file="db:tdmdownloads_download.tpl" down=$file[i]}>
                <{/section}>
            </div>
            <{/if}>
    <{/if}>

</div><!-- .tdmdownloads -->

<{include file="db:system_notification_select.tpl"}>
