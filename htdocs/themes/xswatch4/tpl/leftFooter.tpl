<aside class="col-md-4">
    <{foreach item=block from=$xoBlocks.footer_left|default:null}>
        <div class="xoops-footer-blocks mt-2 mb-2">
            <{if !empty($block.title)}><h4><{$block.title}></h4><{/if}>
            <{include file="$theme_name/tpl/blockContent.tpl"}>
        </div>
    <{/foreach}>
</aside>
