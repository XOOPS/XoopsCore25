<table class="table table-bordered table-striped">
	<thead class="thead-light">
		<tr>
			<th scope="col" class="col-4"><{$smarty.const._MA_XMNEWS_NEWS_TITLE}></th>
			<th scope="col" class="col-5"><{$smarty.const._MA_XMNEWS_NEWS_DESC}></th>
			<th scope="col" class="col-1 text-center"><{$smarty.const._MA_XMNEWS_NEWS_USERID}></th>
			<th scope="col" class="col-2 text-center"><{$smarty.const._MA_XMNEWS_ACTION}></th>
		</tr>
	</thead>
	<tbody>
<{foreach item=news from=$block.news}>
	<tr>
		<td><{$news.title}></td>
		<td><{$news.description|truncateHtml:50:'...'}></td>
		<td class="text-center"><{$news.author}></td>
		<td class="text-center">
			<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmnews/action.php?op=edit&amp;news_id=<{$news.id}>"><i class="fa fa-edit" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_EDIT}></button>
		</td>
	</tr>
<{/foreach}>
	</tbody>
</table>