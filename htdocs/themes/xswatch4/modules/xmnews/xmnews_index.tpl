<div class="xmnews">
	<{if $cat}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
			<li class="breadcrumb-item active" aria-current="page"><{$category_name}></li>
		  </ol>
		</nav>
	<{else}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item active" aria-current="page"><{$index_module}></li>
		  </ol>
		</nav>
	<{/if}>
	<div align="center">
		<form class="form-inline" id="form_news_tri" name="form_news_tri" method="get" action="index.php">
			<div class="form-group">
				<label><{$smarty.const._MA_XMNEWS_NEWS_SELECTCATEGORY}>&nbsp;</label>
				<select class="form-control form-control-sm" name="news_filter" id="news_filter" onchange="location='index.php?news_cid='+this.options[this.selectedIndex].value">
					<{$news_cid_options}>
				</select>
			</div>
		</form>
	</div>
	<br>
	<br>
	<{if $cat}>
		<div class="row">
			<div class="col-3 col-md-4 col-lg-3 text-center">
				<img class="rounded img-fluid" src="<{$category_logo}>" alt="<{$category_name}>">
			</div>
			<div class="col-9 col-md-8 col-lg-9 " style="padding-bottom: 5px; padding-top: 5px;">
				<h4 class="mt-0"><{$category_name}></h4>
				<{$category_description}>
			</div>
		</div>
		<br>
	<{/if}>
	<{if $news_count != 0}>
		<div class="row">
			<{foreach item=news from=$news}>
				<div class="col-md-12 mb-3">
<!--				<div class="row no-gutters rounded overflow-hidden flex-md-row mb-0 shadow-sm h-md-250 position-relative">-->
						<div class="card">
							<div class="card-header">
								<div class="d-flex justify-content-between">
									<h3 class="mb-0"><{$news.title}></h3>
									<{if $news.dohits == 1}>
										<div class="row align-items-center text-right">
											<div class="col">
												<span class="badge badge-secondary fa-lg text-primary ml-2"><span class="fa fa-eye fa-lg" aria-hidden="true"></span><small> <{$news.counter}></small></span>
											</div>	
										</div>	
									<{/if}>
								</div>
							</div>
							<{if ($news.douser == 1) || ($news.dodate == 1) || (($news.domdate == 1) && ($news.mdate)) || ($news.dorating == 1) }> 
								<div class="row border-bottom border-secondary mx-1 pl-1">
									<{if $news.douser == 1}>
										<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
											  <span class="fa fa-user fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHEDBY_BT}>
											  <figcaption class="figure-caption text-center"><{$news.author}></figcaption>
										</figure>
									<{/if}>
									<{if ($news.dodate == 1) && (($news.domdate == 1) && ($news.mdate)) && ($news.douser == 1)}>
										<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
											  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHED_BT}>
											  <figcaption class="figure-caption text-center d-none d-md-block"><{$news.date}></figcaption>
											  <figcaption class="figure-caption text-center d-block d-md-none"><{$news.date|truncate:10:''}> </figcaption>
										</figure>
									<{else}>
										<{if $news.dodate == 1}>
											<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
												  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHED_BT}>
												  <figcaption class="figure-caption text-center"><{$news.date}></figcaption>
											</figure>
										<{/if}>
									<{/if}>	
									<{if $news.domdate == 1}>
										<{if $news.mdate}>
											<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
												<span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_MDATE_BT}>
												<figcaption class="figure-caption text-center"><{$news.mdate}></figcaption>
											</figure>
										<{/if}>
									<{/if}>
									<{if $news.dorating == 1}>
										<{if $xmsocial == true}>
											<figure class="text-muted m-1 pr-2 text-center border-right border-secondary">
												<span class="fa fa-star" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_RATING}>
												<figcaption class="figure-caption text-center"><{$news.rating}></figcaption>	
											</figure>	
										<{/if}>
									<{/if}>
								</div>
							<{/if}>
							<div class="d-block d-md-none pt-2 px-4">
								<{if $news.logo != ''}>
									<{if $CAT == true}><a href="index.php?news_cid=<{$category_id}>"><{/if}><img class="card-img-top rounded img-fluid" src="<{$news.logo}>" alt="<{$news.title}>"><{if $CAT == true}></a><{/if}>
								<{/if}>
							</div>

							<div class="card-body">
								<div class="col d-flex flex-column position-static">

									<p class="card-text mb-auto">
										<div class="row">
											<div class="col">
												<{if $news.logo != ''}>
												<{if $CAT == true}>
												<a href="index.php?news_cid=<{$category_id}>">
												<{/if}>
												<img class="col-3 rounded float-right d-none d-md-block" src="<{$news.logo}>" alt="<{$title}>">
												<{if $CAT == true}>
												</a>
												<{/if}>
												<{/if}>
												
												<p class="card-text mb-auto"><{$news.description}></p>
											</div>
									</p>
										</div>
								</div>
								<div class="w-100"></div>
								<div class="col-12 pl-4 pt-4 pb-2">
									<button type="button" class="btn btn-primary" onclick=window.location.href="article.php?news_id=<{$news.id}>"><span class="fa fa-book" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_MORE}></button>
								</div>
								<div class="w-100"></div>
								<{if $xmdoc_viewdocs == true}>
								<div class="col-12 pl-4 pr-4 pb-4"> 
									<div class="card">
										<div class="card-header">
											<{$smarty.const._MA_XMNEWS_NEWS_XMDOC}>
										</div>
										<div class="card-body">
											<{include file="db:xmdoc_viewdoc.tpl"}>
										</div>
									</div>
								</div>
								<div class="w-100"></div>
								<{/if}>
							</div>
						</div>
<!--				</div>-->
				</div>				
			
			<{/foreach}>
		</div>
		<div class="clear spacer"></div>
		<{if $nav_menu}>
			<div class="floatright"><{$nav_menu}></div>
			<div class="clear spacer"></div>
		<{/if}>
	<{else}>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<{$smarty.const._MA_XMNEWS_ERROR_NONEWS}>
		</div>
	<{/if}>
	<div style="margin:3px; padding: 3px;">
		<{include file='db:system_notification_select.tpl'}>
    </div>
</div><!-- .xmnews -->