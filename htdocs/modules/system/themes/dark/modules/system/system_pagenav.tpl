<{if ($pageNavType == 'Nav')}>
	<div id="xo-pagenav">
	 <{foreach item=itemNavigation from=$pageNavigation|default:null}>
		<{if ($itemNavigation.option == 'first')}>
			<a class="xo-pagarrow" href="<{$itemNavigation.url}>"><u>&laquo;</u></a>
		<{/if}>
		<{if ($itemNavigation.option == 'selected')}>
			<strong class="xo-pagact" >(<{$itemNavigation.value}>)</strong>
		<{/if}>
		<{if ($itemNavigation.option == 'break')}>
			...
		<{/if}>
		<{if ($itemNavigation.option == 'show')}>
			<a class="xo-counterpage" href="<{$itemNavigation.url}>"><{$itemNavigation.value}></a> 
		<{/if}>	
		<{if ($itemNavigation.option == 'last')}>
			<a class="xo-pagarrow" href="<{$itemNavigation.url}>"><u>&raquo;</u></a>
		<{/if}>
	 <{/foreach}>
	</div>
<{/if}>
<{if ($pageNavType == 'Select')}>
	<form name="pagenavform">
		<select name="pagenavselect" id="pagenavselect" onchange="location=this.options[this.options.selectedIndex].value;">
			<{$pageNavigation.select}>
		</select>
		<{if ($pageNavigation.button == true)}>
			<input type="submit" value="<{$smarty.const._GO}>" />
		<{/if}>
	</form>
<{/if}>
<{if ($pageNavType == 'Image')}>
	<table>
		<tr>
		 <{foreach item=itemNavigation from=$pageNavigation|default:null}>
			<{if ($itemNavigation.option == 'first')}>
				<td class="pagneutral">
					<a href="<{$itemNavigation.url}>"><u>&lt;</u></a>
				</td>
				<td>
					<img src="<{$xoops_url}>/images/blank.gif" width="6" alt="" />
				</td>
			<{/if}>
			<{if ($itemNavigation.option == 'firstempty')}>
				<td class="pagno">
				</td>
				<td>
					<img src="<{$xoops_url}>/images/blank.gif" width="6" alt="" />
				</td>
			<{/if}>
			<{if ($itemNavigation.option == 'selected')}>
				<td class="pagact">
					<strong><{$itemNavigation.value}></strong>
				</td>
			<{/if}>
			<{if ($itemNavigation.option == 'break')}>
				<td class="paginact">...</td>
			<{/if}>
			<{if ($itemNavigation.option == 'show')}>
				<td class="paginact">
					<a href="<{$itemNavigation.url}>"><{$itemNavigation.value}></a>
				</td>
			<{/if}>	
			<{if ($itemNavigation.option == 'last')}>
				<td>
					<img src="<{$xoops_url}>/images/blank.gif" width="6" alt="" />
				</td>
				<td class="pagneutral">
					<a href="<{$itemNavigation.url}>"><u>&gt;</u></a>
				</td>
			<{/if}>
			<{if ($itemNavigation.option == 'lastempty')}>
				<td>
					<img src="<{$xoops_url}>/images/blank.gif" width="6" alt="" />
				</td>
				<td class="pagno">					
				</td>
			<{/if}>
		 <{/foreach}>			
		</tr>
	</table>
<{/if}>
