<{if ($pageNavType == 'Nav')}>
	<ul class="pagination pagination-sm justify-content-end">
	 <{foreach item=itemNavigation from=$pageNavigation}>
		<{if ($itemNavigation.option == 'first')}>
			<li class="page-item">
				<a class="page-link" href="<{$itemNavigation.url}>">
					<span>&laquo;</span>
				</a>
			</li>
		<{/if}>
		<{if ($itemNavigation.option == 'selected')}>
			<li class="page-item active">
				<a class="page-link" href="#"><{$itemNavigation.value}></a>
			</li>
		<{/if}>
		<{if ($itemNavigation.option == 'break')}>
			<li class="page-item disabled">
				<a class="page-link" href="#">...</a>
			</li>
		<{/if}>
		<{if ($itemNavigation.option == 'show')}>
			<li class="page-item"><a class="page-link" href="<{$itemNavigation.url}>"><{$itemNavigation.value}></a></li>
		<{/if}>	
		<{if ($itemNavigation.option == 'last')}>
			<li class="page-item">
				<a class="page-link" href="<{$itemNavigation.url}>">
					<span>&raquo;</span>
				</a>
			</li>
		<{/if}>
	 <{/foreach}>
	</ul>
<{/if}>
<{if ($pageNavType == 'Select')}>
	<form name="pagenavform" class="form-inline justify-content-end">
		<div class="form-group">
			<select class="form-control form-control-sm" name="pagenavselect" id="pagenavselect" onchange="location=this.options[this.options.selectedIndex].value;">
				<{$pageNavigation.select}>
			</select>
			<{if ($pageNavigation.button == true)}>
				<input type="submit" class="btn btn-primary btn-sm" value="<{$smarty.const._GO}>" />
			<{/if}>
		</div>
	</form>
<{/if}>
<{if ($pageNavType == 'Image')}>
	<table>
		<tr>
		 <{foreach item=itemNavigation from=$pageNavigation}>
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
  