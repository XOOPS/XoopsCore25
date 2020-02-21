<{include file='db:publisher_header.tpl'}>

<!-- if we are on the index page OR inside a category that has subcats OR (inside a category with no subcats
    AND $display_category_summary is set to TRUE), let's display the summary table ! //-->

<{if $indexpage || $category.subcats || ($category && $display_category_summary)}>

    <{if $display_category_summary && $category}>
        <div class="well well-sm">
            <{$lang_category_summary}>
        </div>
    <{/if}>


    <{include file='db:publisher_categories_table.tpl'}>
    <!-- End of if !$category || $category.subcats || ($category && $display_category_summary) //-->
<{/if}>

<{if $items}>
    <h4 class="pub_last_articles_full"><span class="glyphicon glyphicon-chevron-right"></span>&nbsp;<{$lang_items_title}></h4>
    <!-- Start item loop -->
    <{foreach item=item from=$items}>
        <{include file="db:publisher_singleitem.tpl" item=$item}>
    <{/foreach}>
    <!-- End item loop -->

    <!-- end of if $items -->

<{/if}>

<{include file='db:publisher_footer.tpl'}>
