<div class="row">
	<{if $block.document|default:'' != ''}>
	<{foreach item=blockdocument from=$block.document}>
	<div class="col-sm-12 col-md-6 col-lg-4 p-2">
		<div class="card xmdoc-border" <{if $blockdocument.color != false}>style="border-color : <{$blockdocument.color}>;"<{/if}>>
			<div class="card-header text-center text-truncate d-none d-sm-block" <{if $blockdocument.color != false}>style="background-color : <{$blockdocument.color}>;"<{/if}>>
				<div class="d-flex justify-content-center text-center">
					<a class="text-decoration-none" title="<{$blockdocument.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$blockdocument.categoryid}>&amp;doc_id=<{$blockdocument.id}>" target="_blank">
						<h5 class="mb-0 text-white"><{$blockdocument.name}></h5>
					</a>
				</div>
			</div>
			<div class="card-header text-center d-block d-sm-none">
				<div class="d-flex justify-content-center text-center">
					<a class="text-decoration-none" title="<{$blockdocument.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$blockdocument.categoryid}>&amp;doc_id=<{$blockdocument.id}>" target="_blank">
						<h5 class="mb-0 text-white"><{$blockdocument.name}></h5>
					</a>
				</div>
			</div>
			<div class="card-body text-center">
				<div class="row d-flex justify-content-center" >
					<div class="col-12" style="height: 150px;">
						<{if $blockdocument.logo != ''}>
						<a title="<{$blockdocument.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$blockdocument.categoryid}>&amp;doc_id=<{$blockdocument.id}>" target="_blank">
							<img class="rounded img-fluid mh-100" src="<{$blockdocument.logo}>" alt="<{$blockdocument.name}>">
						</a>
						<{/if}>
					</div>
					<div class="col-12 pt-2 text-left">	
						<hr />
						<{$blockdocument.description_short}>
						<hr />
					</div>

					<div class="col-6 col-md-11 col-xl-9 btn-group" role="group">
						<{if $block.use_modal == 1}>
							<a class="btn btn-primary" data-toggle="modal" data-target="#myModal<{$blockdocument.id}>" role="button"> <span class="fa fa-info-circle fa-lg text-light" aria-hidden="true"></span></a>
						<{else}>
							<a class="btn btn-primary" href="<{$xoops_url}>/modules/xmdoc/document.php?doc_id=<{$blockdocument.id}>" role="button">
								<span class="fa fa-info-circle fa-lg" aria-hidden="true"></span>
							</a>
						<{/if}>
						<a class="btn btn-primary d-block d-sm-none"  href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$blockdocument.categoryid}>&amp;doc_id=<{$blockdocument.id}>" target="_blank" title="<{$blockdocument.name}>">
							<span class="fa fa-download fa-lg" aria-hidden="true"></span> 
						</a>
						<a class="btn btn-primary d-none d-sm-block"  href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$blockdocument.categoryid}>&amp;doc_id=<{$blockdocument.id}>" target="_blank" title="<{$blockdocument.name}>">
							<span class="fa fa-download fa-lg" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DOWNLOAD}>
						</a>
					</div>
				</div>				
			</div>				
		</div>
	</div>
	<div class="modal" tabindex="-1" id="myModal<{$blockdocument.id}>" role="dialog">
		<div class="modal-dialog modal-lg" role="blockdocument">
			<div class="modal-content">
				<div class="modal-header d-flex justify-content-between">
					<h5 class="modal-title"><{$blockdocument.name}></h5>
					<div class="row text-right">
						<div class="col">
							<{if $blockdocument.showinfo == 1}>
								<span class="badge badge-secondary fa-lg text-primary ml-1"><span class="fa fa-download" aria-hidden="true"></span><small> <{$blockdocument.counter}></small></span>
								<{if $blockdocument.size != ''}>
									<span class="badge badge-secondary fa-lg text-primary ml-1 mt-1 mt-lg-0"><span class="fa fa-archive" aria-hidden="true"></span><small> <{$blockdocument.size}></small></span>
								<{/if}>	
							<{/if}>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>	
					</div>
				</div>
				<div class="modal-body">
					<{if $blockdocument.showinfo == 1}>
						<div class="row border-bottom border-secondary mx-1 pl-1">
							<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
								  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_DATE_BT}>
								  <figcaption class="figure-caption text-center"><{$blockdocument.date}></figcaption>
							</figure>
							<{if $blockdocument.mdate|default:''}>
							<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
								  <span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_MDATE_BT}>
								  <figcaption class="figure-caption text-center"><{$blockdocument.mdate}></figcaption>
							</figure>
							<{/if}>
							<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
								  <span class="fa fa-user fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_AUTHOR}>
								  <figcaption class="figure-caption text-center"><{$blockdocument.author}></figcaption>
							</figure>
							<{if $blockdocument.dorating == 1}>
							<figure class="text-muted m-1 pr-2 text-center border-right border-secondary">
								<{include file="db:xmsocial_rating.tpl" down_xmsocial=$blockdocument.xmsocial_arr}>
								<figcaption class="figure-caption text-center"></figcaption>
							</figure>
							<{/if}>									
						</div>
					<{/if}>
						<div class="row">
							<div class="col-md-3 d-flex justify-content-center">
								<figure class="figure mt-3">
									<img src="<{$blockdocument.logo}>" class="figure-img img-fluid rounded mx-auto d-block" alt="<{$blockdocument.name}>">
									<figcaption class="figure-caption text-center"><h5 class="mt-0"><{$blockdocument.name}></h5></figcaption>
								</figure>
							</div>
							<div class="col-md-9 align-self-center">
									<{if $blockdocument.description_end}>
										<{$blockdocument.description_short}>
										<hr />
										<{$blockdocument.description_end}>
									<{else}>
										<{$blockdocument.description}>
									<{/if}>
							</div>
						</div>
				</div>
				<div class="modal-footer d-flex justify-content-center">
					<a class="btn btn-primary" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$blockdocument.categoryid}>&amp;doc_id=<{$blockdocument.id}>" target="_blank" title="<{$blockdocument.name}>">
						<span class="fa fa-download fa-lg" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DOWNLOAD}>
					</a>
				</div>
				<{if ($blockdocument.perm_edit == true) || ($blockdocument.perm_del == true)}>
					<div class="modal-footer d-flex justify-content-center">
						<div class="btn-group text-center" role="group">
							<{if $blockdocument.perm_edit == true}>
								<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=edit&amp;document_id=<{$blockdocument.id}>"><span class="fa fa-edit" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_EDIT}></button>
							<{/if}>
							<{if $blockdocument.perm_del == true}>
								<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=del&amp;document_id=<{$blockdocument.id}>"><span class="fa fa-trash" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DEL}></button>
							<{/if}>
						</div>
					</div>
				<{/if}>
			</div>
		</div>
	</div>
	<{/foreach}>
	<{/if}>
</div>