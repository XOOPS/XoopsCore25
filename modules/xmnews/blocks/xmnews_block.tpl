<div class="row">
	<{foreach item=news from=$block.news}>
	<{if $block.full == 0}>
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
	<{else}>
	<div class="col-md-12">
		<div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
			<div class="col-12 d-block d-md-none pt-4 pl-4 pr-4">
				<{if $news.logo != ''}>
					<img class="rounded img-fluid" src="<{$news.logo}>" alt="<{$news.title}>">
				<{/if}>
			</div>
			<div class="col p-4 d-flex flex-column position-static">
				<h3 class="mb-0"><{$news.title}></h3>
				<div class="mb-2 text-muted"><{if $news.douser == 1}><{$smarty.const._MA_XMNEWS_NEWS_PUBLISHEDBY}> <{$author}><{/if}> <{$smarty.const._MA_XMNEWS_NEWS_ON}> <{$news.date}></div>
				<p class="card-text mb-auto"><{if $news.logo != ''}><img class="col-3 rounded float-right d-none d-md-block" src="<{$news.logo}>" alt="<{$news.title}>"><{/if}><{$news.news}></p>

			</div>
			<div class="w-100"></div>	
			<div class="col-12 pl-4 pr-4 pb-4">
				<div class="card">
					<div class="card-header">
						<{$smarty.const._MA_XMNEWS_GENINFORMATION}>
					</div>
					<div class="card-body">
						<div class="row">
							<{if $news.dohits == 1}>
							<div class="col-12 col-lg-6">
								<i class="fa fa-rotate-right" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_READING}>: <{$news.hits}>
							</div>
							<{/if}>
							<{if $news.dorating == 0}>
							<div class="col-12 col-lg-6">
								<i class="fa fa-star" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_RATING}>: <{$news.rating}> <{$news.votes}>
							</div>
							<{/if}>
							<{if $news.domdate == 1}>
							<{if $news.mdate}>
							<div class="col-12 col-lg-6">
								<i class="fa fa-calendar" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_MDATE}>: <{$news.mdate}>
							</div>
							<{/if}>
							<{/if}>							
						</div>						
						<div class="text-center pt-2">
							<div class="btn-group text-center" role="group">
								<{if $news.perm_edit == true}>
									<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmnews/action.php?op=edit&amp;news_id=<{$news.id}>"><i class="fa fa-edit" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_EDIT}></button>
								<{/if}>
								<{if $news.perm_clone == true}>
									<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmnews/action.php?op=clone&amp;news_id=<{$news.id}>"><i class="fa fa-clone" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_CLONE}></button>
								<{/if}>
								<{if $news.perm_del == true}>
									<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmnews/action.php?op=del&amp;news_id=<{$news.id}>"><i class="fa fa-trash" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_DEL}></button>
								<{/if}>
							</div>
						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
	<{/if}>
	<{/foreach}>
</div>