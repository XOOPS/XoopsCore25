<{if $block.category && $block.category.image_path != ''}>
    <div align="center">
        <a href="<{$block.category.categoryurl}>" title="<{$block.category.name}>">
            <img src="<{$block.category.image_path}>" width="185" height="80" alt="<{$block.category.name}>"/>
        </a>
    </div>
<{/if}>


<{if $block.display_type=='block'}>
    <{foreach item=item from=$block.items}>
        <{include file="db:publisher_singleitem_block.tpl" item=$item}>
    <{/foreach}>

<{else}>
    <{foreach item=item from=$block.items name=spotlight}>
        <{if $item.summary != ''}>
            <div class="spot_publisher_items_list">
                <div class="article_wf_title">
                    <h3><{$item.titlelink}></h3>
                    <span>
                        <span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.category}>
                    </span>
                    <span>
                        <span class="glyphicon glyphicon-user"></span>&nbsp;<{$item.who}>
                    </span>
                    <span>
                        <span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$item.when}>
                    </span>
                    <span>
                        <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comments}>
                    </span>
                </div>
                <{if $item.image_path}>
                    <div class="spot_article_wf_img">
                        <img src="<{$item.image_path}>" alt="<{$item.title}>"/>
                    </div>
                <{/if}>
                <div class="article_wf_summary">
                    <{$item.summary}>
                </div>

                <{if $block.truncate}>
                    <div class="pull-right" style="margin-top: 15px;">
                        <a href="<{$item.itemurl}>" class="btn btn-primary btn-xs">
                            <{$block.lang_readmore}>
                        </a>
                    </div>
                <{/if}>
                <div style="clear: both;"></div>
            </div>
        <{/if}>
    <{/foreach}>
<{/if}>
