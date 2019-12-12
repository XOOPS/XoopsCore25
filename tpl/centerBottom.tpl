<div class="col-sm-12 col-md-12">
    <{foreach item=block from=$xoBlocks.page_bottomcenter}>
    <div class="xoops-bottom-blocks">
        <{if $block.title}><h4><{$block.title}></h4><{/if}>
        <{includeq file="$theme_name/tpl/blockContent.tpl"}>
    </div>
    <{/foreach}>
</div>
