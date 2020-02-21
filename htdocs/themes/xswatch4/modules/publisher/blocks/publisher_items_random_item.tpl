<{foreach item=items from=$block.items}>
 <{if $block.display_item_image == '1'}>
	<a href="<{$block.url}>"><img class="img-fluid" src="<{$block.image_path}>" alt="<{$block.alt}>" title="<{$block.alt}>" /></a>
  <{/if}>
            <{$block.titlelink}><br>
            <{if $block.display_summary == '1'}>
            <{$block.content}><br>
			<{/if}>	
            <{if $block.display_categorylink == '1'}>
            <span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-tag"></span>&nbsp;<{$block.categorylink}>
                </span>
			<{/if}>	
			<{if $block.display_poster == '1'}>
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-user"></span>&nbsp;<{$block.poster}>
                </span>
			<{/if}>	
			<{if $block.display_date == '1'}>
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-calendar"></span>&nbsp;<{$block.date}>
                </span>
			<{/if}>
			<{if $block.display_comment == '1' && $block.cancomment && $block.comment != -1}>
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-comment"></span>&nbsp;<{$block.comment}>
                </span>
			<{/if}>
            <{if $block.display_hits == '1'}>	
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-check-circle-o"></span>&nbsp;<{$block.hits}> <{$block.lang_hits}>
                </span>
			<{/if}>	


  <{if $block.display_lang_fullitem == '1'}>
<div align="right" style="padding: 15px 0 0 0;">
    <a class="btn btn-primary btn-xs" href='<{$block.url}>'><{$block.lang_fullitem}></a>
</div>
  <{/if}>
<{/foreach}>
