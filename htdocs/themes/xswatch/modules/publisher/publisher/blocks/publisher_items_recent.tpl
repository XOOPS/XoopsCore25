<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <th class="head"><{$block.lang_title}></th>
<{if $block.show_category == '1'}> <td class="head" align="left"><{$block.lang_category}></td><{/if}>
<{if $block.show_date == '1'}> <td class="head" align="right" width="120"><{$block.lang_date}></td><{/if}>
<{if $block.show_poster == '1'}> <td class="head" align="center" width="100"><{$block.lang_poster}></td><{/if}>
     </tr>
    </thead>
    <tbody>
    <{foreach item=item from=$block.items}>
        <tr>
            <td>
             <{if $block.show_image == '1'}>
	              <a href="<{$newitems.itemurl}>"><img src="<{$item.item_image}>" alt="<{$item.alt}>" title="<{$item.alt}>" align="left" style="padding:5px;"/></a><br> 
             <{/if}>
			<{$item.itemlink}>
			<{if $block.show_summary == '1'}><br><{$item.summary}><{/if}><br />
			<small>
              <{if $block.show_hits == '1'}><span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$item.hits}><{/if}> 
              <{if $block.show_comment == '1' && $item.cancomment && $item.comment != -1}><span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comment}><{/if}>
            </small>
            </td>
           
           <{if $block.show_category == '1'}><td align="left"><{$item.categorylink}></td><{/if}>
           <{if $block.show_poster == '1'}><td align="center"><{$item.poster}></td><{/if}>
           <{if $block.show_date == '1'}><td align="right"><{$item.date}></td><{/if}>	
         </tr>
    <{/foreach}>
    </tbody>

</table>

<{if $block.show_morelink == '1'}>
  <div style="text-align:right; padding: 5px;">
    <a class="btn btn-primary btn-xs" href="<{$publisher_url}>"><{$block.lang_visitItem}></a>
   </div>
<{/if}>	
