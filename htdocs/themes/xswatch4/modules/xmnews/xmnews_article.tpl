<div class="xmmews">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
		<li class="breadcrumb-item"><a href="index.php?news_cid=<{$category_id}>"><{$category_name}></a></li>
		<li class="breadcrumb-item active" aria-current="page"><{$title}></li>
	  </ol>
	</nav>
	<{if $status == 2}>
		<div class="alert alert-warning" role="alert">
			<{$smarty.const._MA_XMNEWS_INFO_NEWSWAITING}>
		</div>
	<{/if}>
	<{if $status == 0}>
		<div class="alert alert-danger" role="alert">
			<{$smarty.const._MA_XMNEWS_INFO_NEWSDISABLE}>
		</div>
	<{/if}>
	<{if $warning_date|default:false}>
		<div class="alert alert-warning" role="alert">
			<{$smarty.const._MA_XMNEWS_INFO_NEWSNOTPUBLISHED}>
		</div>
	<{/if}>
	<div class="row mb-2">
		<div class="col-md-12">
<!--		<div class="no-gutters rounded overflow-hidden flex-md-row mb-0 shadow-sm h-md-250 position-relative">-->
				<div class="card" <{if $category_color != false}>style="border-color : <{$category_color}>;"<{/if}>>
					<div class="card-header category_color" <{if $category_color != false}>style="background-color : <{$category_color}>;"<{/if}>>
						<div class="d-flex justify-content-between">
							<h3 class="mb-0 text-white"><{$title}></h3>
							<{if $dohits == 1}>
								<div class="row align-items-center text-right">
									<div class="col">
										<span class="badge badge-secondary fa-lg text-primary ml-2"><span class="fa fa-eye fa-lg" aria-hidden="true"></span><small> <{$counter}></small></span>
									</div>	
								</div>	
							<{/if}>
						</div>
					</div>
					<{if ($douser == 1) || ($dodate == 1) || (($domdate == 1) && ($mdate)) || ($dorating == 1) }> 
						<div class="row border-bottom border-secondary mx-1 pl-1">
							<{if $douser == 1}>
								<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
									  <span class="fa fa-user fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHEDBY_BT}>
									  <figcaption class="figure-caption text-center"><{$author}></figcaption>
								</figure>
							<{/if}>
							<{if ($dodate == 1) && (($domdate == 1) && ($mdate|default:false)) && ($douser == 1)}>
								<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
									  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHED_BT}>
									  <figcaption class="figure-caption text-center d-none d-md-block"><{$date}></figcaption>
									  <figcaption class="figure-caption text-center d-block d-md-none"><{$date|truncate:10:''}> </figcaption>
								</figure>
							<{else}>
								<{if $dodate == 1}>
									<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
										  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_PUBLISHED_BT}>
										  <figcaption class="figure-caption text-center"><{$date}></figcaption>
									</figure>
								<{/if}>
							<{/if}>	
							<{if $domdate == 1}>
								<{if $mdate|default:false}>
									<figure class="figure text-muted m-1 pr-2 text-center border-right border-secondary">
										<span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_NEWS_MDATE_BT}>
										<figcaption class="figure-caption text-center"><{$mdate}></figcaption>
									</figure>
								<{/if}>
							<{/if}>
							<{if $dorating == 1}>
								<figure class="text-muted m-1 pr-2 text-center border-right border-secondary">
									<span class="d-block"><{include file="db:xmsocial_rating.tpl" down_xmsocial=$xmsocial_arr}></span>
									<figcaption class="figure-caption text-center"></figcaption>
								</figure>	
							<{/if}>
						</div>
					<{/if}>
					<div class="d-block d-md-none pt-2 px-4">
						<{if $logo != ''}>
							<{if $CAT == true}><a href="index.php?news_cid=<{$category_id}>"><{/if}><img class="card-img-top rounded img-fluid" src="<{$logo}>" alt="<{$title}>"><{if $CAT == true}></a><{/if}>
						<{/if}>
					</div>
					<div class="card-body">
						<p class="card-text mb-auto">
							<div class="row">
								<div class="col">
									<{if $logo != ''}>
									<{if $CAT == true}>
									<a href="index.php?news_cid=<{$category_id}>">
									<{/if}>
									<img class="col-3 rounded float-right d-none d-md-block" src="<{$logo}>" alt="<{$title}>">
									<{if $CAT == true}>
									</a>
									<{/if}>
									<{/if}>
									<p>
									<{$news}>
									</p>
								</div>
							</div>
						</p>
						<div class="w-100"></div>
						<{if $social == true}>
							<{include file="db:xmsocial_social.tpl"}>
							<br>
						<{/if}>
						<{if $xmdoc_viewdocs|default:false == true}>
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
<!--		</div>-->
		</div>				
	</div>		
				
	<{if ($perm_edit == true) || ($perm_clone == true) || ($perm_del == true)}> 
	<div class="col-12 pl-4 pr-4 pb-2">
				<div class="text-center pt-2">
					<div class="btn-group text-center" role="group">
						<{if $perm_edit == true}>
							<a class="btn btn-secondary" href="action.php?op=edit&amp;news_id=<{$news_id}>"><span class="fa fa-edit" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_EDIT}></a>
						<{/if}>
						<{if $perm_clone == true}>
							<a class="btn btn-secondary" href="action.php?op=clone&amp;news_id=<{$news_id}>"><span class="fa fa-clone" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_CLONE}></a>
						<{/if}>
						<{if $perm_del == true}>
							<a class="btn btn-secondary" href="action.php?op=del&amp;news_id=<{$news_id}>"><span class="fa fa-trash" aria-hidden="true"></span> <{$smarty.const._MA_XMNEWS_DEL}></a>
						<{/if}>
					</div>
				</div>
	</div>
	<{/if}>
	<{if $docomment == 1}>
	<div style="text-align: center; padding: 3px; margin:3px;">
        <{$commentsnav}>
        <{$lang_notice}>
    </div>
    <div style="margin:3px; padding: 3px;">
        <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    </div>
	<{/if}>
	<div style="margin:3px; padding: 3px;">
		<{include file='db:system_notification_select.tpl'}>
    </div>
</div><!-- .xmarticle -->
