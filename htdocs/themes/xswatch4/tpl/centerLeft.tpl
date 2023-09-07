<{if $xoBlocks.page_topleft}>
    <div class="col-6 mr-auto">
        <{foreach item=block from=$xoBlocks.page_topleft|default:null}>
            <div class="xoops-blocks">
                <{if $block.title}><h4><{$block.title}></h4><{/if}>
                <{include file="$theme_name/tpl/blockContent.tpl"}>
            </div>
        <{/foreach}>
    </div>
<{/if}>
