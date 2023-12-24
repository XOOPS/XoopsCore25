<ul class="list-group list-group-flush">
	<{foreach item=blocknews from=$block.news}>
		<a class="list-group-item list-group-item-action p-1 text-truncate" title="<{$blocknews.title}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$blocknews.id}>">
			<{if $block.logo == true}>
				<{if $blocknews.logo != ''}>
					<img src="<{$blocknews.logo}>" alt="<{$blocknews.title}>" style="max-width:<{$block.size}>px" class="rounded">
				<{/if}>
			<{/if}>
			<{$blocknews.title}>
		</a>
	<{/foreach}>
</ul>
