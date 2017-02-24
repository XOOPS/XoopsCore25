<div class="article_full">
    <div class="article_full_category">
        <{$item.category}>
    </div>

    <{if $item.image_path}>
        <div class="article_full_img_div">
            <a href="<{$item.itemurl}>" title="<{$item.title}>">
                <img src="<{$item.image_path}>" alt="<{$item.title}>"/>
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
        <div class="pull-left" style="margin-top: 15px;">
            <{if $op != 'preview'}>
                <span style="float: right; text-align: right;"><{$item.adminlink}></span>
            <{else}>
                <span style="float: right;">&nbsp;</span>
            <{/if}>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
