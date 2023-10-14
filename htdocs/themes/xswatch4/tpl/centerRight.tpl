<{if $xoBlocks.page_topright}>
    <div class="col-6 ml-auto">
        <{foreach item=block from=$xoBlocks.page_topright|default:null}>
            <div class="xoops-blocks">
                <{if $block.title}><h4><{$block.title}></h4><{/if}>
                <{include file="$theme_name/tpl/blockContent.tpl"}>
            </div>
        <{/foreach}>
    </div>
<{/if}>
