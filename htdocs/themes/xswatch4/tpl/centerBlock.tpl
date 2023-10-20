<{if $xoBlocks.page_topcenter}>
    <div class="col-12">
        <{foreach item=block from=$xoBlocks.page_topcenter|default:null}>
            <div class="xoops-blocks">
                <{if $block.title}><h4><{$block.title}></h4><{/if}>
                <{include file="$theme_name/tpl/blockContent.tpl"}>
            </div>
        <{/foreach}>
    </div>
<{/if}>
