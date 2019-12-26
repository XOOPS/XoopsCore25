<div class="row">
	<{foreach item=news from=$block.news}>
	<div class="col-6 col-sm-6 col-md-4 p-2">
		<div class="card">
			<div class="card-header text-center">
				<a class="text-decoration-none" title="<{$category.name}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$news.id}>">
					<{$news.title|truncate:25:'...'}>
				</a>
			</div>
			<div class="card-body text-center">
				<div class="row" >
					<div class="col-12" style="height: 150px;">
						<{if $news.logo != ''}>
						<a title="<{$news.title}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$news.id}>">
							<img class="rounded img-fluid mh-100" src="<{$news.logo}>" alt="<{$news.title}>">
						</a>
						<{/if}>
					</div>
					<div class="col-12 pt-2 text-left text-muted xmnews-data">
						<{if $news.type == "date" || $news.type == "random"}>
						<i class="fa fa-calendar" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_BLOCKS_DATE}>: <{$news.date}>
						<{/if}>
						<{if $news.type == "hits"}>
						<i class="fa fa-rotate-right" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_READING}>: <{$news.hits}>
						<{/if}>
						<{if $news.type == "rating"}>
						<i class="fa fa-star" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_RATING}>: <{$news.rating}> (<{$news.votes}>)
						<{/if}>
					</div>
					<div class="col-12 pt-2 text-left">	
						<{$news.description|truncateHtml:20:'...'}>
					</div>
					<div class="col-12 pt-2">	
						<button class="btn btn-primary btn-sm" onclick=window.location.href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$news.id}>"><{$smarty.const._MA_XMNEWS_NEWS_MORE}></button>
					</div>					
				</div>				
			</div>				
		</div>
	</div>
	<{/foreach}>
</div>