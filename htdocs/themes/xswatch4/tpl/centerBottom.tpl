<div class="col-12">
    <{foreach item=block from=$xoBlocks.page_bottomcenter|default:null}>
    <div class="xoops-bottom-blocks">
        <{if $block.title}><h4><{$block.title}></h4><{/if}>
        <{include file="$theme_name/tpl/blockContent.tpl"}>
    </div>
    <{/foreach}>
</div>
