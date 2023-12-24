<{include file='db:publisher_header.tpl'}>

<!-- if we are on the index page OR inside a category that has subcats OR (inside a category with no subcats
    AND $display_category_summary is set to TRUE), let's display the summary table ! //-->

<{if $indexpage|default:false || $category.subcats || ($category && $display_category_summary)}>

    <{if $display_category_summary && $category|default:''}>
        <div class="well-sm card">
            <{$lang_category_summary}>
        </div>
    <{/if}>


    <{include file='db:publisher_categories_table.tpl'}>
    <!-- End of if !$category || $category.subcats || ($category && $display_category_summary) //-->
<{/if}>

<{if isset($items)}>
    <h4 class="pub_last_articles_full"><span class="fa fa-chevron-right"></span>&nbsp;<{$lang_items_title}></h4>
    <!-- Start item loop -->
    <{foreach item=item from=$items|default:null}>
        <div class="article_full">
            <div class="article_full_category_b5">
                <{$item.category}>
            </div>

            <{if $item.image_path}>
                <div class="article_full_img_div">
                    <a href="<{$item.itemurl}>" title="<{$item.title}>">
                        <img src="<{$item.image_path}>" alt="<{$item.title}>">
                    </a>
                </div>
            <{/if}>
            <div class="pt-5" style="padding: 10px;">
                <h4><{$item.titlelink}></h4>
                <{if isset($display_whowhen_link)}>
                    <small><{$item.who_when}> (<{$item.counter}> <{$lang_reads}>)</small>
                <{/if}>
                <div style="margin-top:10px;">
                    <{$item.summary}>
                </div>
                <div class="" style="margin-top: 15px;">
                    <{if $op|default:'' != 'preview'}>
                        <span style="float: right; text-align: right;"><{$item.adminlink}></span>
                    <{else}>
                        <span style="float: right;">&nbsp;</span>
                    <{/if}>
                </div>
                <div class="" style="margin-top: 15px;">
                    <a href="<{$item.itemurl}>" class="btn btn-primary"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!--<{include file="db:publisher_singleitem.tpl" item=$item}>-->
    <{/foreach}>
    <!-- End item loop -->

    <!-- end of if $items -->

<{/if}>

<{include file='db:publisher_footer.tpl'}>
