<{include file='db:publisher_header.tpl'}>

<!-- if we are on the index page OR inside a category that has subcats OR (inside a category with no subcats
    AND $display_category_summary is set to TRUE), let's display the summary table ! //-->

<{if $indexpage|default:false || $category.subcats || ($category && $display_category_summary)}>

    <{* if $display_category_summary && $category}>
        <div class="well well-sm">
            <{$lang_category_summary}>
        </div>
    <{/if *}>


    <{include file='db:publisher_categories_table.tpl'}>
    <!-- End of if !$category || $category.subcats || ($category && $display_category_summary) //-->
<{/if}>

<{if $items}>
<div class="container">
    <h4 class="pub_last_articles_full"><span class="fa fa-newspaper-o"></span>&nbsp;<{$lang_items_title}></h4>
    <div class="row mb-3">
        <{foreach item=item from=$items}>
        <div class="card col-12 col-md-6 mt-2">
            <{if $item.image_path}>
            <a href="<{$item.itemurl}>" title="<{$item.title}>">
            <img class="card-img-top" src="<{$item.image_path}>" alt="<{$item.title}>"></a>
            <{/if}>
            <div class="card-body">
                <h5 class="card-title"><{$item.titlelink}></h5>
                <{if $show_subtitle && $item.subtitle}>
                <p class="text-muted"><{$item.subtitle}></p>
                <{/if}>
                <{if $display_whowhen_link}>
                <p class="card-text"><small class="text-muted"><{$item.who_when}> (<{$item.counter}> <{$smarty.const._MD_PUBLISHER_READS}>)</small></p>
                <{/if}>
                <{if $indexpage|default:false}>
                <p class="card-text"><{$item.summary}></p>
                <{else}>
                <p class="card-text"><{$item.summary|truncateHtml:80}></p>
                <{/if}>

                <div class="pull-right">
                    <a href="<{$item.itemurl}>" class="btn btn-primary btn-sm"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
                </div>
            </div>
        </div>
        <{/foreach}>
    </div>
    <{if $navbar|default:false}>
    <div class="generic-pagination col text-right mt-2">
        <{$navbar|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}>
    </div>
    <{/if}>
</div>
<{/if}>

<{include file='db:publisher_footer.tpl'}>
