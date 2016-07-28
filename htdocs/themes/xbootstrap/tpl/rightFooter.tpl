<aside class="col-sm-4 col-md-4">
    <{foreach item=block from=$xoBlocks.footer_right}>
        <div class="xoops-footer-blocks">
            <{if $block.title}><h4><{$block.title}></h4><{/if}>
            <{$block.content}>
        </div>
    <{/foreach}>
</aside>
