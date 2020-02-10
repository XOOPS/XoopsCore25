<div class="table-responsive">
	<table class="table table-hover table-sm">
	<thead>
		<tr>
			<th scope="col"><{$smarty.const._MA_XMNEWS_NEWS_TITLE}></th>
			<th class="d-none d-sm-table-cell" scope="col"><{$smarty.const._MA_XMNEWS_NEWS_DESC}></th>
			<th scope="col" class="text-center"><{$smarty.const._MA_XMNEWS_NEWS_USERID}></th>
			<th scope="col" class="text-center"><{$smarty.const._MA_XMNEWS_ACTION}></th>
		</tr>
	</thead>
	<tbody>
<{foreach item=waitingnews from=$block.news}>
	<tr>
		<td class=""><{$waitingnews.title}></td>
		<td class="d-none d-sm-table-cell"><{$waitingnews.description|truncateHtml:50:'...'}></td>
		<td class="text-center"><{$waitingnews.author}></td>
		<td class="text-center">
			<a class="btn btn-outline-primary" title="<{$smarty.const._MA_XMNEWS_EDIT}>" href="<{$xoops_url}>/modules/xmnews/action.php?op=edit&amp;news_id=<{$waitingnews.id}>"><i class="fa fa-edit" aria-hidden="true"></i></a>
		</td>
	</tr>
<{/foreach}>
	</tbody>
</table>
</div>