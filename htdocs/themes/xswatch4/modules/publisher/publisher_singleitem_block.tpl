<div class="article_full">
<{if $block.display_categorylink}>  
    <div class="article_full_category">
        <{$item.category}>
    </div>
<{/if}>
   <{if $item.image_path}>
            <div class="article_full_img_div">
                <a href="<{$item.itemurl}>" title="<{$item.title}>">
                <img src="<{$item.image_path}>" alt="<{$item.title}>"/>
                </a>
            </div>
            <{else}>
               <div class="article_full_img_div">
				<a href="<{$item.itemurl}>" title="<{$item.title}>">
                <img src="<{$block.publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>"/>
                </a>
                </div>	
    <{/if}>
    <div style="padding: 10px;">
        <h4><{$item.titlelink}></h4>
             <small>
                   
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
         </small>
        <div style="margin-top:10px;">
            <{$item.summary}>
			<{if $block.truncate}>
			      <{if $block.display_readmore}>	
			        <div class="pull-right" style="margin-top: 15px;">
                        <a href="<{$item.itemurl}>" class="btn btn-primary btn-xs">
                            <{$block.lang_readmore}>
                        </a>
                    </div>
                   <{/if}>
			<{/if}>
        </div>
        <div class="pull-left" style="margin-top: 15px;">
            <{if $op != 'preview'}>
                 <{if $block.display_adminlink}>
					<span style="float: right; text-align: right;"><{$item.adminlink}></span>
                 <{/if}>	
            <{else}>
                <span style="float: right;">&nbsp;</span>
            <{/if}>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
