<{if $cat|default:false}>
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
<{if $index_header|default:'' != ''}>
    <div class="row">
        <div class="col-sm-12">
            <{$index_header}>
			<hr />
        </div>
    </div>
<{/if}>
<div align="center">
	<{if $index_cat == 2 || $index_cat == 3}>
	<h3><{$smarty.const._MA_XMDOC_CATEGORY_LIST}></h3>
	<div class="xm-category row">
	<{foreach item=categories from=$cat_array}>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2 p-2 
			<{if $cat && $categories.id == $doc_cid}>
				bg-secondary
			<{/if}>">
			<a title="<{$categories.name}>" href="<{$xoops_url}>/modules/xmdoc/index.php?doc_cid=<{$categories.id}>">
				<div class="card xmdoc-border" <{if $categories.color != false}>style="border-color : <{$categories.color}>;"<{/if}>>
					<div class="card-header text-center" <{if $categories.color != false}>style="background-color : <{$categories.color}>;"<{/if}>>						
						<h6 class="mb-0 text-white"><{$categories.name}></h6>
					</div>
					<div class="card-body h-md-550 text-center">
						<div class="row" style="height: 90px;">
							<div class="col-12 h-75">
								<{if $categories.logo != ''}>								
									<img class="rounded img-fluid mh-100" src="<{$categories.logo}>" alt="<{$categories.name}>">
								<{/if}>
							</div>							
						</div>				
					</div>				
				</div>
			</a>
		</div>	
	<{/foreach}>
	</div>
	<{/if}>
	<{if $index_cat == 1 || $index_cat == 3}>
	<form class="form-inline" id="form_document_tri" name="form_document_tri" method="get" action="index.php">
		<div class="form-group">
			<label><{$smarty.const._MA_XMDOC_INDEX_SELECTCATEGORY}>&nbsp;</label>
			<select class="form-control form-control-sm" name="news_filter" id="news_filter" onchange="location='index.php?doc_cid='+this.options[this.selectedIndex].value">
				<{$doc_cid_options}>
			</select>
		</div>
	</form>
	<{/if}>
</div>
<{if $cat}>
<hr />
	<div class="row mb-2">
		<div class="col-3 col-md-4 col-lg-3 text-center">
			<img class="rounded img-fluid" src="<{$category_logo}>" alt="<{$category_name}>">
		</div>
		<div class="col-9 col-md-8 col-lg-9 " style="padding-bottom: 5px; padding-top: 5px;">
			<h4 class="mt-0"><{$category_name}></h4>
			<{$category_description}>
		</div>
	</div>
<{/if}>
<{if $document_count|default:0 != 0}>
	<hr />
	<div class="row">
		<{foreach item=document from=$documents}>
			<div class="col-sm-12 col-md-6 col-lg-4 p-2">
				<div class="card xmdoc-border" <{if $document.color != false}>style="border-color : <{$document.color}>;"<{/if}>>
					<div class="card-header text-center text-truncate d-none d-sm-block" <{if $document.color != false}>style="background-color : <{$document.color}>;"<{/if}>>
						<div class="d-flex justify-content-center text-center">
							<a class="text-decoration-none" title="<{$document.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank">
								<h5 class="mb-0 text-white"><{$document.name}></h5>
							</a>
						</div>
					</div>
					<div class="card-header text-center d-block d-sm-none">
						<div class="d-flex justify-content-center text-center">
							<a class="text-decoration-none" title="<{$document.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank">
								<h5 class="mb-0 text-white"><{$document.name}></h5>
							</a>
						</div>
					</div>
					<div class="card-body text-center">
						<div class="row d-flex justify-content-center">
							<div class="col-12" style="height: 150px;">
								<{if $document.logo != ''}>
									<a title="<{$document.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank">
										<img class="rounded img-fluid mh-100" src="<{$document.logo}>" alt="<{$document.name}>">
									</a>
								<{/if}>
							</div>
							<div class="col-12 text-left">	
								<hr />
								<{$document.description_short}>
								<hr />
							</div>
							<div class="col-6 col-md-11 col-xl-9 btn-group" role="group">
								<{if $use_modal == 1}>
									<a class="btn btn-primary" data-toggle="modal" data-target="#myModal<{$document.id}>" role="button"> <span class="fa fa-info-circle fa-lg text-light" aria-hidden="true"></span></a>
								<{else}>
									<a class="btn btn-primary" href="<{$xoops_url}>/modules/xmdoc/document.php?doc_id=<{$document.id}>" role="button">
										<span class="fa fa-info-circle fa-lg" aria-hidden="true"></span>
									</a>
								<{/if}>
								<a class="btn btn-primary d-block d-sm-none"  href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank" title="<{$document.name}>">
									<span class="fa fa-download fa-lg" aria-hidden="true"></span> 
								</a>
								<a class="btn btn-primary d-none d-sm-block"  href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank" title="<{$document.name}>">
									<span class="fa fa-download fa-lg" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DOWNLOAD}>
								</a>
							</div>
						</div>				
					</div>				
				</div>
			</div>
			<div class="modal" tabindex="-1" id="myModal<{$document.id}>" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header d-flex justify-content-between">
							<h5 class="modal-title"><{$document.name}></h5>
							<div class="row text-right">
								<div class="col">
									<{if $document.showinfo == 1}>
										<span class="badge badge-secondary fa-lg text-primary ml-1"><span class="fa fa-download" aria-hidden="true"></span><small> <{$document.counter}></small></span>
										<{if $document.size != ''}>
											<span class="badge badge-secondary fa-lg text-primary ml-1 mt-1 mt-lg-0"><span class="fa fa-archive" aria-hidden="true"></span><small> <{$document.size}></small></span>
										<{/if}>	
									<{/if}>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								</div>	
							</div>
						</div>
						<div class="modal-body">
							<{if $document.showinfo == 1}>
								<div class="row border-bottom border-secondary mx-1 pl-1">
									<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
										  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_DATE_BT}>
										  <figcaption class="figure-caption text-center"><{$document.date}></figcaption>
									</figure>
									<{if $document.mdate|default:''}>
									<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
										  <span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_MDATE_BT}>
										  <figcaption class="figure-caption text-center"><{$document.mdate}></figcaption>
									</figure>
									<{/if}>
									<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
										  <span class="fa fa-user fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_FORMDOC_AUTHOR}>
										  <figcaption class="figure-caption text-center"><{$document.author}></figcaption>
									</figure>
									<{if $document.dorating == 1}>
									<figure class="text-muted m-1 pr-2 text-center border-right border-secondary">
										<{include file="db:xmsocial_rating.tpl" down_xmsocial=$document.xmsocial_arr}>
										<figcaption class="figure-caption text-center"></figcaption>
									</figure>
									<{/if}>									
								</div>
							<{/if}>
								<div class="row">
									<div class="col-md-3 d-flex justify-content-center">
										<figure class="figure mt-3">
											<img src="<{$document.logo}>" class="figure-img img-fluid rounded mx-auto d-block" alt="<{$document.name}>">
											<figcaption class="figure-caption text-center"><h5 class="mt-0"><{$document.name}></h5></figcaption>
										</figure>
									</div>
									<div class="col-md-9 align-self-center">
											<{if $document.description_end}>
												<{$document.description_short}>
												<hr />
												<{$document.description_end}>
											<{else}>
												<{$document.description}>
											<{/if}>
									</div>
								</div>
						</div>
						<div class="modal-footer d-flex justify-content-center">
							<a class="btn btn-primary" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank" title="<{$document.name}>">
								<span class="fa fa-download fa-lg" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DOWNLOAD}>
							</a>
						</div>
						<{if ($document.perm_edit == true) || ($document.perm_del == true)}>
							<div class="modal-footer d-flex justify-content-center">
								<div class="btn-group text-center" role="group">
									<{if $document.perm_edit == true}>
										<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=edit&amp;document_id=<{$document.id}>"><span class="fa fa-edit" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_EDIT}></button>
									<{/if}>
									<{if $document.perm_del == true}>
										<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=del&amp;document_id=<{$document.id}>"><span class="fa fa-trash" aria-hidden="true"></span> <{$smarty.const._MA_XMDOC_DEL}></button>
									<{/if}>
								</div>
							</div>
						<{/if}>
					</div>
				</div>
			</div>			
		<{/foreach}>
	</div>
	<{if $nav_menu|default:false}>
		<div class="row">
			<div class="col-sm-12" style="padding-bottom: 10px; padding-top: 5px; padding-right: 60px; text-align: right;">
				<{$nav_menu}>
			</div>
		</div>
	<{/if}>
<{else}>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<{$smarty.const._MA_XMDOC_ERROR_NODOCUMENT}>
	</div>
<{/if}>

<{if $index_footer|default:'' != ''}>
    <div class="row pb-2">
        <div class="col-sm-12">
            <hr />
			<{$index_footer}>
        </div>
    </div>
<{/if}>
