<{include file='db:publisher_header.tpl'}>

<{if $indexpage || $category.subcats || ($category && $display_category_summary)}>

    <{if $display_category_summary && $category}>
        <div class="well well-sm">
            <{$lang_category_summary}>
        </div>
    <{/if}>

    <{include file='db:publisher_categories_table.tpl'}>
    <!-- End of if !$category || $category.subcats || ($category && $display_category_summary) //-->
<{/if}>
<h4 class="pub_last_articles_list"><span class="glyphicon glyphicon-chevron-right"></span>&nbsp;<{$lang_items_title}></h4>
<div class="publisher_items_list_">
    <{if $items}>
    <{foreach item=item from=$items}>
        <div class="article_list">
            <{if $item.image_path}>
                <div class="article_list_img">
                    <a href="<{$item.itemurl}>" title="<{$item.title}>">
                        <img src="<{$item.image_path}>" alt="<{$item.title}>"/>
                    </a>
                </div>
            <{/if}>
            <div class="article_list_summary">
                <div class="article_list_title">
                    <h3><{$item.titlelink}></h3>
                    <span>
                        <span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.category}>
                    </span>
                    <span>
                        <span class="glyphicon glyphicon-user"></span>&nbsp;<{$item.who}>
                    </span>
                    <span>
                        <span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$item.when}>
                    </span>
                    <span>
                        <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comments}>
                    </span>
                </div>

            </div>
            <div class="clearfix"></div>
        </div>
    <{/foreach}>
</div>

    <div align="right"><{$navbar}></div>

<{$press_room_footer}>


<{/if}>
<!-- end of if $items -->

<{include file='db:publisher_footer.tpl'}>
