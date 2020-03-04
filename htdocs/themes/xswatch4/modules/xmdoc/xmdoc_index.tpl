<{if $index_header}>
    <div class="row">
        <div class="col-sm-12" style="padding-bottom: 10px; padding-top: 5px;">
            <{$index_header}>
        </div>
    </div>
<{/if}>
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
	<form class="form-inline" id="form_document_tri" name="form_document_tri" method="get" action="index.php">
		<div class="form-group">
			<label><{$smarty.const._MA_XMDOC_SELECTCATEGORY}>&nbsp;</label>
			<select class="form-control form-control-sm" name="news_filter" id="news_filter" onchange="location='index.php?doc_cid='+this.options[this.selectedIndex].value">
				<{$doc_cid_options}>
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
<{if $document_count != 0}>
	<div class="row">
	<{foreach item=document from=$documents}>
		<div class="col-12 col-sm-6 col-md-4 p-2">
		<div class="card">
			<div class="card-header text-center">
				<a class="text-decoration-none" title="<{$document.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank">
					<{$document.name|truncate:25:'...'}>
				</a>
			</div>
			<div class="card-body text-center">
				<div class="row" >
					<div class="col-12" style="height: 150px;">
						<{if $document.logo != ''}>
						<a title="<{$document.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank">
							<img class="rounded img-fluid mh-100" src="<{$document.logo}>" alt="<{$document.name}>">
						</a>
						<{/if}>
					</div>
					<div class="col-12 pt-2 text-left">	
						<{$document.description_short|truncateHtml:10:'...'}>
					</div>
					<div class="col-12 pt-2 text-left">
						<button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#myModal<{$document.id}>"><i class="fa fa-eye" aria-hidden="true"></i></button>
					</div>
					<div class="col-12 pt-2">
						<a class="text-decoration-none" title="<{$document.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank">
							<button class="btn btn-primary btn-sm"><{$smarty.const._MA_XMDOC_DOWNLOAD}></button>
						</a>
					</div>					
				</div>				
			</div>				
		</div>
	</div>
	<div class="modal" tabindex="-1" id="myModal<{$document.id}>" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><{$document.name}></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12 col-md-3">
							<{if $document.logo != ''}>
							<a title="<{$document.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$document.categoryid}>&amp;doc_id=<{$document.id}>" target="_blank">
								<img class="rounded img-fluid mh-100" src="<{$document.logo}>" alt="<{$document.name}>">
							</a>
							<{/if}>
						</div>						
						<div class="col-12 col-md-9 text-left">
							<{$document.description}>
						</div>
						<{if $document.showinfo == 1}>
						<div class="col-12 p-4">
							<div class="card">
								<div class="card-header">
									<{$smarty.const._MA_XMDOC_GENINFORMATION}>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-12 col-lg-6">
											<i class="fa fa-calendar" aria-hidden="true"></i> <{$smarty.const._MA_XMDOC_FORMDOC_DATE}>: <{$document.date}>
										</div>
										<div class="col-12 col-lg-6">
											<i class="fa fa-user" aria-hidden="true"></i> <{$smarty.const._MA_XMDOC_FORMDOC_AUTHOR}>: <{$document.author}>
										</div>
										<{if $document.mdate}>
										<div class="col-12 col-lg-6">
											<i class="fa fa-calendar" aria-hidden="true"></i> <{$smarty.const._MA_XMDOC_FORMDOC_MDATE}>: <{$document.mdate}>
										</div>
										<{/if}>
										<{if $document.size}>
										<div class="col-12 col-lg-6">
											<i class="fa fa-expand" aria-hidden="true"></i> <{$smarty.const._MA_XMDOC_FORMDOC_SIZE}>: <{$document.size}>
										</div>
										<{/if}>
										<div class="col-12 col-lg-6">
											<i class="fa fa-download" aria-hidden="true"></i> <{$smarty.const._MA_XMDOC_FORMDOC_DOWNLOAD}>: <{$document.counter}>
										</div>
										<{if $document.dorating == 1}>
										<div class="col-12 col-lg-6">
											<{include file="db:xmsocial_rating.tpl" down_xmsocial=$document.xmsocial_arr}>
										</div>
										<{/if}>
									</div>									
								</div>
							</div>
						</div>
						<{/if}>
					</div>
					<div class="text-center">
						<div class="btn-group text-center" role="group">
							<{if $document.perm_edit == true}>
								<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=edit&amp;document_id=<{$document.id}>"><i class="fa fa-edit" aria-hidden="true"></i> <{$smarty.const._MA_XMDOC_EDIT}></button>
							<{/if}>
							<{if $document.perm_del == true}>
								<button type="button" class="btn btn-secondary" onclick=window.location.href="<{$xoops_url}>/modules/xmdoc/action.php?op=del&amp;document_id=<{$document.id}>"><i class="fa fa-trash" aria-hidden="true"></i> <{$smarty.const._MA_XMDOC_DEL}></button>
							<{/if}>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<{/foreach}>
	</div>
	<{if $nav_menu}>
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

<{if $index_footer}>
    <div class="row" style="padding-bottom: 5px; padding-top: 5px;">
        <div class="col-sm-12">
            <{$index_footer}>
        </div>
    </div>
<{/if}>
