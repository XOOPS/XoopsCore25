<div class="xmnews">
	<{if $cat|default:false}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb" <{if $category_color != false}>style="border-color : <{$category_color}>;"<{/if}>>
			<li class="breadcrumb-item"><span class="fa fa-newspaper text-secondary fa-lg fa-fw me-2 mt-1"></span><a href="index.php"><{$index_module}></a></li>
			<li class="breadcrumb-item active" aria-current="page"><{$category_name}></li>
		  </ol>
		</nav>
	<{else}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item active" aria-current="page"><span class="fa fa-newspaper text-secondary fa-lg fa-fw me-2 mt-1"></span> <{$index_module}></li>
		  </ol>
		</nav>
	<{/if}>
		<{if $news_cid_options|default:false}>
			<form class="d-flex align-items-center mb-3" id="form_news_tri" name="form_news_tri" method="get" action="index.php">
				<div class="mb-3">
					<label><{$smarty.const._MA_XMNEWS_NEWS_SELECTCATEGORY}>&nbsp;</label>
					<select class="form-select form-select-sm" name="news_filter" id="news_filter" onchange="location='index.php?news_cid='+this.options[this.selectedIndex].value">
						<{$news_cid_options}>
					</select>
				</div>
			</form>
		<{/if}>
	<{if $cat|default:false}>
		<div class="row mb-2">
			<{if $category_logo != ''}>
			<div class="col-3 col-md-4 col-lg-3 text-center">
				<img class="rounded img-fluid" src="<{$category_logo}>" alt="<{$category_name}>">
			</div>
			<{/if}>
			<div class="col-9 col-md-8 col-lg-9 " style="padding-bottom: 5px; padding-top: 5px;">
				<h4 class="mt-0"><{$category_name}></h4>
				<{$category_description}>
			</div>
		</div>
	<{/if}>
	<{if $news_count != 0}>
		<div class="row row-cols-lg-3 row-cols-md-2 row-cols-1 justify-content-center">
			<{foreach item=newsitem from=$news}>
				<div class="col mb-3">
					<div class="card h-100 xmnews-border" <{if $newsitem.color != false}>style="border-color : <{$newsitem.color}>;"<{/if}>>
						<div class="card-header" <{if $newsitem.color != false}>style="background-color : <{$newsitem.color}>;"<{/if}>>
							<div class="d-flex justify-content-center text-center">
								<h5 class="mb-0 text-white"><{$newsitem.title}><{if $news_cid == 0}><br /><a href="index.php?news_cid=<{$newsitem.cid}>"><span class="badge rounded-pill text-bg-dark" style="font-weight: lighter;" ><{$newsitem.cat_name}></span><{/if}></a></h5>
							</div>
						</div>

						<{if $newsitem.logo != ''}>
							<{if $CAT|default:false == true}>
								<a href="index.php?news_cid=<{$category_id}>">
							<{/if}>
							<img class="img-fluid" src="<{$newsitem.logo}>" alt="<{$newsitem.title}>">
							<{if $CAT|default:false == true}>
								</a>
							<{/if}>
						<{/if}>

						<div class="card-body">
							<{$newsitem.description}>
							<div class="text-end mt-1 ">
								<button type="button" class="btn btn-primary btn-sm text-end" onclick=window.location.href="article.php?news_id=<{$newsitem.id}>"><span class="fa fa-book" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_MORE}></button>
							</div>
						</div>

						<div class="card-footer text-secondary">
							<div class="row">
								<{if $newsitem.douser == 1}>
									<div class="col-5 text-start">
										<span class="fa fa-user fa-fw" aria-hidden="true"></span> <{$newsitem.author}>
									</div>
								<{/if}>
								<{if $newsitem.dodate == 1}>
									<div class="col-7 text-end">
										<span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$newsitem.date}>
									</div>
								<{/if}>
									
							</div>
							<div class="row">
								<{if $newsitem.dohits == 1}>
									<div class="col-5 text-start">
										<span class="fa fa-eye fa-fw" aria-hidden="true"></span> <{$newsitem.counter}>
									</div>
								<{/if}>
								<{if $newsitem.domdate == 1}>
									<{if $newsitem.mdate|default:false}>
										<div class="col-7 text-end">
											<span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$newsitem.mdate}>
										</div>
									<{/if}>
								<{/if}>										
							</div>
							<{if $newsitem.dorating == 1}>
								<{if $xmsocial == true}>
									<div class="row">
										<div class="col">
											<span class="fa fa-star" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_RATING}> <{$newsitem.rating}>	
										</div>	
									</div>
								<{/if}>
							<{/if}>
						</div>
					</div>
				</div>				
			
			<{/foreach}>
		</div>
		<div class="clear spacer"></div>
		<{if $nav_menu|default:false}>
			<div class="floatright"><{$nav_menu}></div>
			<div class="clear spacer"></div>
		<{/if}>
	<{else}>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			<{$smarty.const._MA_XMNEWS_ERROR_NONEWS}>
		</div>
	<{/if}>
	<div style="margin:3px; padding: 3px;">
		<{include file='db:system_notification_select.tpl'}>
    </div>
</div><!-- .xmnews -->