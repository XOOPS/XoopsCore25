<div class="row">
	<{if $block.news|default:false}>
	<{foreach item=blocknews from=$block.news}>
		<{if $block.full == 0}>
			<div class="col-xs-12 col-sm-6 col-lg-3 mb-3 px-1 px-sm-2 mx-3 mx-sm-0">
				<div class="card xmnews-border" <{if $blocknews.color != false}>style="border-color : <{$blocknews.color}>;"<{/if}>>
					<div class="card-header text-center text-truncate d-none d-sm-block" <{if $blocknews.color != false}>style="background-color : <{$blocknews.color}>;"<{/if}>>
						<a class="text-decoration-none text-white" title="<{$category.name|default:''}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$blocknews.id}>">
							<{$blocknews.title}>
						</a>
					</div>
					<div class="card-header text-center d-block d-sm-none">
						<a class="text-decoration-none" title="<{$category.name|default:''}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$blocknews.id}>">
							<{$blocknews.title}>
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
									<div class="d-block d-lg-none d-xl-block"><span class="fa fa-calendar" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_BLOCKS_DATE}>: <{$blocknews.date}></div>
									<div class="d-none d-lg-block d-xl-none"><br /><span class="fa fa-calendar" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_BLOCKS_DATE}>: <{$blocknews.date|truncate:10:''}></div>
								<{/if}>
								<{if $blocknews.type == "hits"}>
									<span class="fa fa-eye" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_READING}>: <{$blocknews.hits}>
								<{/if}>
								<{if $blocknews.type == "rating"}>
								<{if $block.xmsocial == true}>
								<span class="fa fa-star" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_RATING}>: <{$blocknews.rating}>
								<{/if}>
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
				<div class="card xmnews-border" <{if $blocknews.color != false}>style="border-color : <{$blocknews.color}>;"<{/if}>>
					<div class="card-header" <{if $blocknews.color != false}>style="background-color : <{$blocknews.color}>;"<{/if}>>
						<div class="d-flex justify-content-between">
							<h3 class="mb-0 text-white"><{$blocknews.title}></h3>
							<{if $blocknews.dohits == 1}>
								<div class="row align-items-center text-right">
									<div class="col">
										<span class="badge badge-secondary fa-lg text-primary ml-2"><span class="fa fa-eye fa-lg" aria-hidden="true"></span><small> <{$blocknews.hits}></small></span>
									</div>	
								</div>	
							<{/if}>
						</div>
					</div>
					<{if ($blocknews.douser == 1) || ($blocknews.dodate == 1) || (($blocknews.domdate == 1) && ($blocknews.mdate)) || ($blocknews.dorating == 1) }> 
						<div class="row border-bottom border-secondary mx-1 pl-1">
							<{if $blocknews.douser == 1}>
								<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
									  <span class="fa fa-user fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHEDBY_BT}>
									  <figcaption class="figure-caption text-center"><{$blocknews.author}></figcaption>
								</figure>
							<{/if}>
							<{if ($blocknews.dodate == 1) && (($blocknews.domdate == 1) && ($blocknews.mdate)) && ($blocknews.douser == 1)}>
								<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
									  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHED_BT}>
									  <figcaption class="figure-caption text-center d-none d-md-block"><{$blocknews.date}></figcaption>
									  <figcaption class="figure-caption text-center d-block d-md-none"><{$blocknews.date|truncate:10:''}> </figcaption>
								</figure>
							<{else}>
								<{if $blocknews.dodate == 1}>
									<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
										  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHED_BT}>
										  <figcaption class="figure-caption text-center"><{$blocknews.date}></figcaption>
									</figure>
								<{/if}>
							<{/if}>	
							<{if $blocknews.domdate == 1}>
								<{if $blocknews.mdate}>
									<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
										<span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_MDATE_BT}>
										<figcaption class="figure-caption text-center"><{$blocknews.mdate}></figcaption>
									</figure>
								<{/if}>
							<{/if}>
							<{if $blocknews.dorating == 1}>
								<{if $block.xmsocial == true}>
									<figure class="text-muted m-1 pr-2 text-center border-right border-secondary">
										<span class="fa fa-star" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_RATING}>
										<figcaption class="figure-caption text-center"><{$blocknews.rating}></figcaption>
									</figure>	
								<{/if}>
							<{/if}>
						</div>
					<{/if}>
					<div class="d-block d-md-none pt-2 px-4">
						<{if $blocknews.logo != ''}>
							<img class="card-img-top rounded img-fluid" src="<{$blocknews.logo}>" alt="<{$blocknews.title}>">
						<{/if}>
					</div>

					<div class="card-body">
						<div class="col d-flex flex-column position-static">

							<p class="card-text mb-auto">
								<div class="row">
									<div class="col">
										<{if $blocknews.logo != ''}>
											<img class="col-3 rounded float-right d-none d-md-block" src="<{$blocknews.logo}>" alt="<{$blocknews.title}>">
										<{/if}>
										<p>
										<{$blocknews.news}>
										</p>
									</div>
								</div>
							</p>
						</div>
						<div class="w-100"></div>
					</div>
				</div>
			</div>				
		<{/if}>
	<{/foreach}>
	<{/if}>
</div>