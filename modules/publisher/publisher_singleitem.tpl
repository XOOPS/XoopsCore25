<div class="sitem">
    <{if $item.image_path}>
        <div class="sitem_img_div">
            <a href="<{$item.itemurl}>" title="<{$item.title}>">
                <img class="sitem_img" src="<{$item.image_path}>" alt="<{$item.title}>" width="100"/>
            </a>
        </div>
    <{/if}>
    <div style="padding: 10px;">
        <h4><{$item.titlelink}></h4>
        <{if $display_whowhen_link}>
            <small><{$item.who_when}> (<{$item.counter}> <{$lang_reads}>)</small>
        <{/if}>
        <div style="margin-top:10px;">
            <{$item.summary}>
        </div>
        <div class="pull-left">
            <{if $op != 'preview'}>
                <span style="float: right; text-align: right;"><{$item.adminlink}></span>
            <{else}>
                <span style="float: right;">&nbsp;</span>
            <{/if}>
        </div>
        <div class="pull-right">
            <a href="<{$item.itemurl}>" class="btn btn-primary btn-xs"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
