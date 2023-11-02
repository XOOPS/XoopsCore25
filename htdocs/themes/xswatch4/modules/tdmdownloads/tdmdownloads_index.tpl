<div class="tdmdownloads">
    <div class="breadcrumb"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/"><{$xoops_pagetitle}></a></div>
    <{if count($categories) > 0}>
    <div class="tdm-category row">
        <{foreach item=category from=$categories|default:null}>
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
        <{if isset($bl_affichage) && $bl_affichage == 1}>
            <div class="col-md-12"><h3><{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLNAME}>:</h3></div>
            <{if !empty($bl_date)}>
                <div class="col-sm-4 col-md-4">
                    <h3 class="tdm-title"><span class="fa fa-calendar"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLDATE}></h3>
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
                    <h3 class="tdm-title"><span class="fa fa-star"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLPOP}></h3>
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
                    <h3 class="tdm-title"><span class="fa fa-thumbs-o-up"></span> <{$smarty.const._MD_TDMDOWNLOADS_INDEX_BLRATING}></h3>
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
            <{if !empty($file)}>
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
