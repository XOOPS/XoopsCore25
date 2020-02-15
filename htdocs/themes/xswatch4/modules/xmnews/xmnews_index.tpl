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
			<div class="col-3 col-md-4 col-lg-3 text-center" style="padding-bottom: 5px; padding-top: 5px;">
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
		<{foreach item=news from=$news}>
			<div class="row mb-2">
				<div class="col-md-12">
					<div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
						<div class="col-12 d-block d-lg-none pt-4 pl-4 pr-4">
							<{if $news.logo != ''}>
							<img class="rounded img-fluid" src="<{$news.logo}>" alt="<{$news.title}>">
							<{/if}>
						</div>
						<div class="col p-4 d-flex flex-column position-static">
							<h3 class="mb-0"><{$news.title}></h3>
							<div class="mb-2 text-muted"><{if $news.douser == 1}><{$smarty.const._MA_XMNEWS_NEWS_PUBLISHEDBY}> <{$news.author}><{/if}> <{if $news.dodate == 1}><{$smarty.const._MA_XMNEWS_NEWS_ON}> <{$news.date}><{/if}></div>
							<p class="card-text mb-auto"><{$news.description}></p>

						</div>
						<div class="col-2 col-md-4 p-4 d-none d-lg-block">
							<{if $news.logo != ''}>
							<img class="rounded img-fluid" src="<{$news.logo}>" alt="<{$news.title}>">
							<{/if}>
						</div>
						<div class="w-100"></div>
						<div class="col-12 pl-4 pb-4">
							<a class="btn btn-secondary" href="article.php?news_id=<{$news.id}>"><i class="fa fa-book" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_MORE}></a>
						</div>
					</div>
				</div>
			</div>
		<{/foreach}>
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