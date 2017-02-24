<div class="col-sm-6 col-md-6">
    <{foreach item=block from=$xoBlocks.page_bottomright}>
    <div class="xoops-bottom-blocks">
        <{if $block.title}><h4><{$block.title}></h4><{/if}>
        <{includeq file="$theme_name/tpl/blockContent.tpl"}>
    </div>
    <{/foreach}>
</div>
