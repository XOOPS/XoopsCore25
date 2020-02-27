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
	<{if $warning_date}>
		<div class="alert alert-warning" role="alert">
			<{$smarty.const._MA_XMNEWS_INFO_NEWSNOTPUBLISHED}>
		</div>
	<{/if}>
	<div class="row mb-2">
		<div class="col-md-12">
			<div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
				<div class="col-12 d-block d-md-none pt-4 pl-4 pr-4">
					<{if $logo != ''}>
						<{if $CAT == true}><a href="index.php?news_cid=<{$category_id}>"><{/if}><img class="rounded img-fluid" src="<{$logo}>" alt="<{$title}>"><{if $CAT == true}></a><{/if}>
					<{/if}>
				</div>
				<div class="col p-4 d-flex flex-column position-static">
					<h3 class="mb-0"><{$title}></h3>
					<div class="mb-2 text-muted"><{if $douser == 1}><{$smarty.const._MA_XMNEWS_NEWS_PUBLISHEDBY}> <{$author}><{/if}> <{$smarty.const._MA_XMNEWS_NEWS_ON}> <{$date}></div>
					<p class="card-text mb-auto">
						<{if $logo != ''}>
						<{if $CAT == true}>
						<a href="index.php?news_cid=<{$category_id}>">
						<{/if}>
						<img class="col-3 rounded float-right d-none d-md-block" src="<{$logo}>" alt="<{$title}>">
						<{if $CAT == true}>
						</a>
						<{/if}>
						<{/if}>
						<div class="row">
							<div class="col">
								<{$news}>
							</div>
						</div>
					</p>
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
				<div class="col-12 pl-4 pr-4 pb-4">
					<div class="card">
						<div class="card-header">
							<{$smarty.const._MA_XMNEWS_GENINFORMATION}>
						</div>
						<div class="card-body">
							<div class="row">
								<{if $dohits == 1}>
								<div class="col-12 col-lg-6">
									<i class="fa fa-rotate-right" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_READING}>: <{$counter}>
								</div>
								<{/if}>
								<{if $dorating == 1}>
								<div class="col-12 col-lg-6">
									<{include file="db:xmsocial_rating.tpl" down_xmsocial=$xmsocial_arr}>
								</div>
								<{/if}>
								<{if $domdate == 1}>
								<{if $mdate}>
								<div class="col-12 col-lg-6">
									<i class="fa fa-calendar" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_NEWS_MDATE}>: <{$mdate}>
								</div>
								<{/if}>
								<{/if}>
							</div>
							<div class="text-center pt-2">
								<div class="btn-group text-center" role="group">
									<{if $perm_edit == true}>
										<a class="btn btn-secondary" href="action.php?op=edit&amp;news_id=<{$news_id}>"><i class="fa fa-edit" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_EDIT}></a>
									<{/if}>
									<{if $perm_clone == true}>
										<a class="btn btn-secondary" href="action.php?op=clone&amp;news_id=<{$news_id}>"><i class="fa fa-clone" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_CLONE}></a>
									<{/if}>
									<{if $perm_del == true}>
										<a class="btn btn-secondary" href="action.php?op=del&amp;news_id=<{$news_id}>"><i class="fa fa-trash" aria-hidden="true"></i> <{$smarty.const._MA_XMNEWS_DEL}></a>
									<{/if}>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
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