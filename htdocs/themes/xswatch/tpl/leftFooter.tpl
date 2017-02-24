<aside class="col-sm-4 col-md-4">
    <{foreach item=block from=$xoBlocks.footer_left}>
        <div class="xoops-footer-blocks">
            <{if $block.title|default:false}><h4><{$block.title}></h4><{/if}>
            <{includeq file="$theme_name/tpl/blockContent.tpl"}>
        </div>
    <{/foreach}>
</aside>
