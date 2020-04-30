<div class="xmdoc">
    <{if $error_message}>
        <div class="alert alert-danger" role="alert"><{$error_message}></div>
    <{/if}>
    <{if $form}>	
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
			<li class="breadcrumb-item"><a href="action.php?op=add"><{$smarty.const._MA_XMDOC_SELECTCATEGORY}></a></li>
			<li class="breadcrumb-item active"><{$smarty.const._MA_XMDOC_ADD}></li>
		  </ol>
		</nav>
		<{if $tips != ''}>
            <div class="alert alert-info" role="alert">
                <div class="floatleft">
                    <h4><{$smarty.const._MA_XMDOC_DOCUMENT_DOCUMENT}></h4>
                    <ul>
                        <li><{$smarty.const._MA_XMDOC_CATEGORY_SIZE}>: <{$size}></li>
                        <li><{$smarty.const._MA_XMDOC_CATEGORY_EXTENSION}>: <{$extensions}></li>
                    </ul>        
                </div>
                <div class="clear">&nbsp;</div>
            </div>
        <{/if}>
        <div class="xmform">
            <{$form}>
        </div>
    <{/if}>
    <{if $categories > 0}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
			<li class="breadcrumb-item active"><{$smarty.const._MA_XMDOC_SELECTCATEGORY}></li>
		  </ol>
		</nav>

		<div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
			<{foreach item=category from=$categories}>
			<div class="col-6 col-sm-4 col-md-3 p-2">
				<div class="card">
					<div class="card-header text-center">
						<a class="text-decoration-none" title="<{$category.name}>" href="<{$xoops_url}>/modules/xmdoc/action.php?op=loaddocument&category_id=<{$category.id}>">
							<{$category.name}>
						</a>
					</div>
						<div class="card-body h-md-550 text-center">
							<div class="row" style="height: 150px;">
								<div class="col-12 h-75">
									<{if $category.logo != ''}>
									<a title="<{$category.name}>" href="<{$xoops_url}>/modules/xmdoc/action.php?op=loaddocument&category_id=<{$category.id}>">
										<img class="rounded img-fluid mh-100" src="<{$category.logo}>" alt="<{$category.name}>">
									</a>
									<{/if}>
								</div>
								<div class="col-12 pt-2">	
									<{if $category.description != ""}>
										<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#xmDesc-<{$category.id}>">+</button>
									<{else}>
										<button class="btn btn-primary btn-sm" data-toggle="modal" disabled>+</button>
									<{/if}>
								</div>								
							</div>				
						</div>				
				</div>
			</div>
			<div class="modal fade" id="xmDesc-<{$category.id}>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header"><h4 class="modal-title aligncenter"><{$category.name}></h4></div>
						<div class="modal-body">
							<{$category.description}>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">&times;</button>
						</div>
					</div>
				</div>
			</div>
			<{/foreach}>
		</div>
		<div class="clear spacer"></div>
		<{if $nav_menu}>
			<div class="floatright"><{$nav_menu}></div>
			<div class="clear spacer"></div>
		<{/if}>
    <{/if}>
</div><!-- .xmdoc-->