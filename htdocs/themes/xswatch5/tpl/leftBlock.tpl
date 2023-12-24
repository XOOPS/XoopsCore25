<{if $xoBlocks.canvas_left}>
    <div class="col-md-3 xoops-side-blocks">
        <{foreach item=block from=$xoBlocks.canvas_left|default:null}>
            <aside>
                <{if $block.title}><h4 class="block-title"><{$block.title}></h4><{/if}>
                <{include file="$theme_name/tpl/blockContent.tpl"}>
            </aside>
        <{/foreach}>
    </div>
<{/if}>
