<{if $xmdoc_viewdocs == true}>
<div class="row">
	<{foreach item=viewdocument from=$document}>
	<div class="col-12 col-md-6 col-lg-4 p-2">
		<div class="card">
			<div class="card-header text-center text-truncate d-none d-sm-block">
				<a class="text-decoration-none" title="<{$viewdocument.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$viewdocument.categoryid}>&amp;doc_id=<{$viewdocument.id}>" target="_blank">
					<{$viewdocument.name}>
				</a>
			</div>
			<div class="card-header text-center d-block d-sm-none">
				<a class="text-decoration-none" title="<{$viewdocument.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$viewdocument.categoryid}>&amp;doc_id=<{$viewdocument.id}>" target="_blank">
					<{$viewdocument.name}>
				</a>
			</div>
			<div class="card-body text-center">
				<div class="row d-flex justify-content-center" >
					<div class="col-12" style="height: 150px;">
						<{if $viewdocument.logo != ''}>
						<a title="<{$viewdocument.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$viewdocument.categoryid}>&amp;doc_id=<{$viewdocument.id}>" target="_blank">
							<img class="rounded img-fluid mh-100" src="<{$viewdocument.logo}>" alt="<{$viewdocument.name}>">
						</a>
						<{/if}>
					</div>
					<div class="col-12 text-left">	
						<hr />
						<{$viewdocument.description_short}>
						<hr />
					</div>
					<div class="col-10 col-md-11 col-xl-10 btn-group" role="group">
						<{if $use_modal == 1}>
							<a class="btn btn-primary" data-toggle="modal" data-target="#myModal<{$viewdocument.id}>" role="button"> <span class="fa fa-info-circle fa-lg text-light" aria-hidden="true"></span></a>
						<{else}>
							<a class="btn btn-primary" href="<{$xoops_url}>/modules/xmdoc/document.php?doc_id=<{$viewdocument.id}>" role="button" target="_blank">
								<span class="fa fa-info-circle fa-lg" aria-hidden="true"></span>
							</a>
						<{/if}>
						<a class="btn btn-primary d-block d-sm-none"  href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$viewdocument.categoryid}>&amp;doc_id=<{$viewdocument.id}>" target="_blank" title="<{$viewdocument.name}>">
							<span class="fa fa-download fa-lg" aria-hidden="true"></span> 
						</a>
						<a class="btn btn-primary d-none d-sm-block"  href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$viewdocument.categoryid}>&amp;doc_id=<{$viewdocument.id}>" target="_blank" title="<{$viewdocument.name}>">
							<span class="fa fa-download fa-lg" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DOWNLOAD}>
						</a>
					</div>
				</div>				
			</div>				
		</div>
	</div>
	<div class="modal" tabindex="-1" id="myModal<{$viewdocument.id}>" role="dialog">
		<div class="modal-dialog modal-lg" role="viewdocument">
			<div class="modal-content">
				<div class="modal-header d-flex justify-content-between">
					<h5 class="modal-title"><{$viewdocument.name}></h5>
					<div class="row text-right">
						<div class="col">
							<{if $viewdocument.showinfo == 1}>
								<span class="badge badge-secondary fa-lg text-primary ml-1"><span class="fa fa-download" aria-hidden="true"></span><small> <{$viewdocument.counter}></small></span>
								<{if $viewdocument.size != ''}>
									<span class="badge badge-secondary fa-lg text-primary ml-1 mt-1 mt-lg-0"><span class="fa fa-archive" aria-hidden="true"></span><small> <{$viewdocument.size}></small></span>
								<{/if}>	
							<{/if}>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>	
					</div>
				</div>
				<div class="modal-body">
					<{if $viewdocument.showinfo == 1}>
						<div class="row border-bottom border-secondary mx-1 pl-1">
							<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
								  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_DATE_BT}>
								  <figcaption class="figure-caption text-center"><{$viewdocument.date}></figcaption>
							</figure>
							<{if $viewdocument.mdate}>
							<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
								  <span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_MDATE_BT}>
								  <figcaption class="figure-caption text-center"><{$viewdocument.mdate}></figcaption>
							</figure>
							<{/if}>
							<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
								  <span class="fa fa-user fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_AUTHOR}>
								  <figcaption class="figure-caption text-center"><{$viewdocument.author}></figcaption>
							</figure>
							<{if $viewdocument.dorating == 1}>
							<figure class="text-muted m-1 pr-2 text-center border-right border-secondary">
								<{include file="db:xmsocial_rating.tpl" down_xmsocial=$viewdocument.xmsocial_arr}>
								<figcaption class="figure-caption text-center"></figcaption>
							</figure>
							<{/if}>									
						</div>
					<{/if}>
						<div class="row">
							<div class="col-md-3 d-flex justify-content-center">
								<figure class="figure mt-3">
									<img src="<{$viewdocument.logo}>" class="figure-img img-fluid rounded mx-auto d-block" alt="<{$viewdocument.name}>">
									<figcaption class="figure-caption text-center"><h5 class="mt-0"><{$viewdocument.name}></h5></figcaption>
								</figure>
							</div>
							<div class="col-md-9 align-self-center">
									<{if $viewdocument.description_end}>
										<{$viewdocument.description_short}>
										<hr />
										<{$viewdocument.description_end}>
									<{else}>
										<{$viewdocument.description}>
									<{/if}>
							</div>
						</div>
				</div>
				<div class="modal-footer d-flex justify-content-center">
					<a class="btn btn-primary" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$viewdocument.categoryid}>&amp;doc_id=<{$viewdocument.id}>" target="_blank" title="<{$viewdocument.name}>">
						<span class="fa fa-download fa-lg" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DOWNLOAD}>
					</a>
				</div>
				<{if ($viewdocument.perm_edit == true) || ($viewdocument.perm_del == true)}>
					<div class="modal-footer d-flex justify-content-center">
						<div class="btn-group text-center" role="group">
							<{if $viewdocument.perm_edit == true}>
								<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=edit&amp;document_id=<{$viewdocument.id}>"><span class="fa fa-edit" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_EDIT}></button>
							<{/if}>
							<{if $viewdocument.perm_del == true}>
								<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=del&amp;document_id=<{$viewdocument.id}>"><span class="fa fa-trash" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DEL}></button>
							<{/if}>
						</div>
					</div>
				<{/if}>
			</div>
		</div>
	</div>
	<{/foreach}>
</div>
<{/if}>
<!-- .xmdoc -->