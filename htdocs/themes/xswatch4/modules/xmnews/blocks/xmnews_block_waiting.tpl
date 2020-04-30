<{if $block.news != ''}>
	<table class="table table-striped table-hover table-sm">
		<thead class="bg-warning">
			<tr>
				<th class="text-center" scope="col"><{$smarty.const._MA_XMNEWS_NEWS_TITLE}></th>
				<{if $block.desclenght != 0}>
				<th class="text-center d-none d-md-table-cell" scope="col"><{$smarty.const._MA_XMNEWS_NEWS_DESC}></th>
				<{/if}>
				<th class="text-center" scope="col"><{$smarty.const._MA_XMNEWS_NEWS_USERID}></th>
				<th class="text-center" scope="col"><{$smarty.const._MA_XMNEWS_ACTION}></th>
			</tr>
		</thead>
		<tbody>
		<{foreach item=waitingnews from=$block.news}>
			<tr>
				<td class="text-sm-center text-warning text-nowrap"><{$waitingnews.title}></td>
				<{if $block.desclenght != '0'}>
				<td class="d-none d-md-block text-warning">
				<{if $block.desclenght != 'all'}>
				<{$waitingnews.description|truncateHtml:$block.desclenght:'...'}>
				<{else}>
				<{$waitingnews.description}>
				<{/if}>
				</td>
				<{/if}>
				<td class="text-center text-warning text-nowrap"><{$waitingnews.author}></td>
				<td class="text-center">
					<a class="btn btn-outline-primary text-warning" title="<{$smarty.const._MA_XMNEWS_EDIT}>" href="<{$xoops_url}>/modules/xmnews/action.php?op=edit&amp;news_id=<{$waitingnews.id}>"><i class="fas fa-edit" aria-hidden="true"></i></a>
				</td>
			</tr>
		<{/foreach}>
		</tbody>
	</table>
<{else}>
	<div class="alert alert-primary"><{$smarty.const._MA_XMNEWS_BLOCKS_NOWAITING}></div>
<{/if}>
