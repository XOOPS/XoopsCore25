<aside class="col-sm-4 col-md-4">
    <{foreach item=block from=$xoBlocks.footer_left|default:null}>
        <div class="xoops-footer-blocks">
            <{if $block.title|default:false}><h4><{$block.title}></h4><{/if}>
            <{$block.content}>
        </div>
    <{/foreach}>
</aside>
