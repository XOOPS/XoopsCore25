<{foreach item=items from=$block.items}> 
  <{if $block.display_item_image == '1'}>
	<a href="<{$block.url}>"><img src="<{$block.item_image}>" alt="<{$block.alt}>" title="<{$block.alt}>" style="padding:5px;" align="left"/></a>
  <{/if}>

   <{$block.titlelink}><br>
     <{if $block.display_summary == '1'}><{$block.content}><br><{/if}>
      <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
      <{if $block.display_poster == '1'}><span class="glyphicon glyphicon-user"></span>&nbsp; <{$block.poster}> <{/if}>
      <{if $block.display_date == '1'}> <span class="glyphicon glyphicon-calendar"></span>&nbsp; <{$block.date}> <{/if}>
      <{if $block.display_categorylink == '1'}><span class="glyphicon glyphicon-tag"></span>&nbsp;<{$block.categorylink}> <{/if}>
      <{if $block.display_hits == '1'}><span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$block.hits}> <{$block.lang_hits}><{/if}>
      <{if $block.display_comment == '1' && $block.cancomment && $block.comment != -1}> <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$block.comment}> <{/if}>
      </span>


  <{if $block.display_lang_fullitem == '1'}>
       <div align="right" style="padding: 15px 0 0 0;">
         <a class="btn btn-primary btn-xs" href='<{$block.url}>'><{$block.lang_fullitem}></a>
       </div>
  <{/if}>
<{/foreach}>
