<{if $block.display_cat_image}>
<{if $block.category && $block.category.image_path != ''}>
    <div align="center">
        <a href="<{$block.category.categoryurl}>" title="<{$block.category.name}>">
            <img src="<{$block.category.image_path}>" width="185" height="80" alt="<{$block.category.name}>"/>
        </a>
    </div>
<{/if}>
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
                     <{if $block.display_categorylink}>
               	    <span>
                        <span class="fa fa-tag"></span>&nbsp;<{$item.category}>
                    </span>
					<{/if}>
					<{if $block.display_who_link}> 
                    <span>
                        <span class="fa fa-user"></span>&nbsp;<{$item.who}>
                    </span>
					<{/if}>
					 <{if $block.display_when_link}>
					 <span>
                    <span class="fa fa-calendar"></span>&nbsp;<{$item.when}>
                    </span>
					<{/if}>
					<{if $block.display_reads}>
					<span>
                        <span class="fa fa-check-circle-o"></span>&nbsp;<{$item.counter}> <{$block.lang_reads}>
                    </span>
					<{/if}>
                    <{if $block.display_comment_link && $item.cancomment && $item.comments != -1}>
					<span>
                        <span class="fa fa-comment"></span>&nbsp;<{$item.comments}>
                    </span>
					<{/if}>
                </div>
                <{if $item.image_path}>
                    <div class="spot_article_wf_img">
                        <img src="<{$item.image_path}>" alt="<{$item.title}>"/>
                    </div>
                <{/if}>
                <div class="article_wf_summary">
                    <{$item.summary}>
					<{if $block.display_adminlink}>
					<br><{$item.adminlink}>
					<{/if}>
                </div>

                <{if $block.truncate}>
				<{if $block.display_readmore}>
                    <div class="pull-right" style="margin-top: 15px;">
                        <a href="<{$item.itemurl}>" class="btn btn-primary btn-xs">
                            <{$block.lang_readmore}>
                        </a>
                    </div>
                <{/if}>
				<{/if}>
                <div style="clear: both;"></div>
            </div>
        <{/if}>
    <{/foreach}>
<{/if}>

<{if $block.lang_displaymore}>
    <div class="clear"></div>
    <div class="pull-right"><a class="btn-readmore" href="<{$block.publisher_url}>" title="<{$block.lang_displaymore}>"><{$block.lang_displaymore}></a></div>
<{/if}>
