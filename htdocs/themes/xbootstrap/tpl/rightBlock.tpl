<{if $xoBlocks.canvas_right}>
    <div class="col-sm-3 col-md-3 xoops-side-blocks">
        <{foreach item=block from=$xoBlocks.canvas_right}>
        <div class="xoops-blocks">
            <aside>
                <{if $block.title}><h4 class="block-title"><{$block.title}></h4><{/if}>
                <{$block.content}>
            </aside>
        </div>
        <{/foreach}>
    </div>
<{/if}>
