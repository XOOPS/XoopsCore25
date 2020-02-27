<div class="row">
	<{foreach item=blocknews from=$block.news}>
	<{if $block.full == 0}>
	<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
		<div class="card">
			<div class="card-header text-center">
				<a class="text-decoration-none" title="<{$category.name}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$blocknews.id}>">
					<{$blocknews.title|truncate:25:'...'}>
				</a>
			</div>
			<div class="card-body text-center">
				<div class="row" >
					<div class="col-12" style="height: 150px;">
						<{if $blocknews.logo != ''}>
						<a title="<{$blocknews.title}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$blocknews.id}>">
							<img class="rounded img-fluid mh-100" src="<{$blocknews.logo}>" alt="<{$blocknews.title}>">
						</a>
						<{/if}>
					</div>
					<div class="col-12 pt-2 text-left text-muted xmnews-data">
						<{if $blocknews.type == "date" || $blocknews.type == "random"}>
						<i class="fa fa-calendar" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_BLOCKS_DATE}>: <{$blocknews.date}>
						<{/if}>
						<{if $blocknews.type == "hits"}>
						<i class="fa fa-rotate-right" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_READING}>: <{$blocknews.hits}>
						<{/if}>
						<{if $blocknews.type == "rating"}>
						<i class="fa fa-star" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_RATING}>: <{$blocknews.rating}> <{$blocknews.votes}>
						<{/if}>
					</div>
					<{if $block.desclenght != '0'}>
					<div class="col-12 pt-2 text-left">						
						<{if $block.desclenght != 'all'}>
						<{$blocknews.description|truncateHtml:$block.desclenght:'...'}>
						<{else}>
						<{$blocknews.description}>
						<{/if}>						
					</div>
					<{/if}>
					<div class="col-12 pt-2">
						<a class="btn btn-primary btn-sm" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$blocknews.id}>"><{$smarty.const._MA_XMNEWS_NEWS_MORE}></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<{else}>
	<div class="col-md-12">
		<div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
			<div class="col-12 d-block d-md-none pt-4 pl-4 pr-4">
				<{if $blocknews.logo != ''}>
					<img class="rounded img-fluid" src="<{$blocknews.logo}>" alt="<{$blocknews.title}>">
				<{/if}>
			</div>
			<div class="col p-4 d-flex flex-column position-static">
				<h3 class="mb-0"><{$blocknews.title}></h3>
				<div class="mb-2 text-muted"><{if $blocknews.douser == 1}><{$smarty.const._MA_XMNEWS_NEWS_PUBLISHEDBY}> <{$blocknews.$author}><{/if}> <{$smarty.const._MA_XMNEWS_NEWS_ON}> <{$blocknews.date}></div>
				<p class="card-text mb-auto">
					<{if $blocknews.logo != ''}>
					<img class="col-3 rounded float-right d-none d-md-block" src="<{$blocknews.logo}>" alt="<{$blocknews.title}>">
					<{/if}>
					<div class="row">
						<div class="col">
							<{$blocknews.news}>
						</div>
					</div>
				</p>
			</div>
			<div class="w-100"></div>
			<div class="col-12 pl-4 pr-4 pb-4">
				<div class="card">
					<div class="card-header">
						<{$smarty.const._MA_XMNEWS_GENINFORMATION}>
					</div>
					<div class="card-body">
						<div class="row">
							<{if $blocknews.dohits == 1}>
							<div class="col-12 col-lg-6">
								<i class="fa fa-rotate-right" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_READING}>: <{$blocknews.hits}>
							</div>
							<{/if}>
							<{if $blocknews.dorating == 0}>
							<div class="col-12 col-lg-6">
								<i class="fa fa-star" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_RATING}>: <{$blocknews.rating}> <{$blocknews.votes}>
							</div>
							<{/if}>
							<{if $blocknews.domdate == 1}>
							<{if $blocknews.mdate}>
							<div class="col-12 col-lg-6">
								<i class="fa fa-calendar" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_MDATE}>: <{$blocknews.mdate}>
							</div>
							<{/if}>
							<{/if}>
						</div>
						<div class="text-center pt-2">
							<div class="btn-group text-center" role="group">
								<{if $blocknews.perm_edit == true}>
									<a class="btn btn-secondary" href="<{$xoops_url}>/modules/xmnews/action.php?op=edit&amp;news_id=<{$blocknews.id}>"><i class="fa fa-edit" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_EDIT}></a>
								<{/if}>
								<{if $blocknews.perm_clone == true}>
									<a class="btn btn-secondary" href="<{$xoops_url}>/modules/xmnews/action.php?op=clone&amp;news_id=<{$blocknews.id}>"><i class="fa fa-clone" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_CLONE}></a>
								<{/if}>
								<{if $blocknews.perm_del == true}>
									<a class="btn btn-secondary" href="<{$xoops_url}>/modules/xmnews/action.php?op=del&amp;news_id=<{$blocknews.id}>"><i class="fa fa-trash" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_DEL}></a>
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