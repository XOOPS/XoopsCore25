<!doctype html>
<html lang="<{$xoops_langcode}>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>">
    <meta http-equiv="content-language" content="<{$xoops_langcode}>">
    <title>Xmdoc manager</title>
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl xoops.css}>">
	<link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl modules/system/css/imagemanager.css}>">
	<link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl modules/system/css/admin.css}>">
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl media/font-awesome/css/font-awesome.min.css}>">
	<{if $bootstrap_css != ''}>
	<link rel="stylesheet" type="text/css" media="screen" href="<{$bootstrap_css}>">
	<{/if}>
</head>
<body onload="window.resizeTo(<{$xsize|default:1024}>, <{$ysize|default:768}>);window.moveTo(400,300);">
	<div class="m-3">
		<div class="card text-center mb-3">
			<{if $selected|default:false}>
				<div class="card-header">
					<{$seldoc_count}>
					<{if $seldoc_count > 1}>
						<{$smarty.const._MA_XMDOC_FORMDOC_SELECTED}>
					<{else}>
						<{$smarty.const._MA_XMDOC_FORMDOC_1SELECTED}>
					<{/if}>
				</div>
				<div class="card-body">
					<div class="row">
						<{foreach item=seldocitem from=$seldoc}>
							<div class="col-6 col-sm-3 col-lg-2 p-1">	
								<div class="card">								
									<div class="card-body text-center text-truncate"><strong><{$seldocitem.name}></strong><br><{$seldocitem.logo}></div>
								</div>
							</div>
						<{/foreach}>
					</div>
					<div class="alert alert-warning" role="alert">						
						<{if $seldoc_count > 1}>
							<{$smarty.const._MA_XMDOC_FORMDOC_WARNING}>
						<{else}>
							<{$smarty.const._MA_XMDOC_FORMDOC_1WARNING}>
						<{/if}>
					</div>
				</div>
				<div class="card-footer">
					<form class="text-center" name="selreset" id="selreset" action="docmanager.php" method="post">
						<input type="hidden" name="selectreset" value="true" />
						<input type='submit' class='formButton' name='subselect'  id='subselect' value='<{$smarty.const._MA_XMDOC_FORMDOC_RESETSELECTED}>' title='<{$smarty.const._MA_XMDOC_FORMDOC_RESETSELECTED}>'  />
						<input value="<{$smarty.const._MA_XMDOC_FORMDOC_VALIDATE}>" type="button" onclick="window.close();"/>
					</form>
				</div>
			<{else}>
				<div class="card-header"><{$smarty.const._MA_XMDOC_FORMDOC_NODOCSELECTED}></div>
			<{/if}>
		</div>
		
		<div class="card text-center mb-3">
			<div class="card-header"><{$smarty.const._MA_XMDOC_FORMDOC_ADD}></div>
			<div class="card-body">
				<div class="row mx-2 d-flex align-items-center">
					<div class="col-9 border-end">
						<{if $form}>
							<div class="xmform mb-3">
								<h5><{$smarty.const._MA_XMDOC_SEARCH}></h5>
								<{$form}>
							</div>
						<{/if}>
					</div>
					<div class="col-3">
						<a href="<{$xoops_url}>/modules/xmdoc/action.php?op=add" class="btn btn-primary btn-sm" target="_blank" role="button" aria-pressed="true" title="<{$smarty.const._MA_XMDOC_DOCUMENT_ADD}>">
							<{$smarty.const._MA_XMDOC_DOCUMENT_ADD}>
						</a>
					</div>
				</div>
					<{if $error_message|default:'' != ''}>
						<div class="errorMsg text-start mt-2">
							<{$error_message}>
						</div>
					<{/if}>
					<{if $document|default:'' != ""}>
						<div class="">
							<form name="formsel" id="formsel" action="docmanager.php" method="post">
								
								<!--<table cellspacing="0" id="imagemain">-->
								<table class="table table-hover table-striped table-bordered mt-4" id="">
									<thead>
										<tr class="table-secondary">
											<th class="text-center" colspan="4" ><{$smarty.const._MA_XMDOC_FORMDOC_LISTDOCUMENT}></th>	
										</tr>
										<tr class="table-secondary">
											<th class="text-center"><{$smarty.const._MA_XMDOC_FORMDOC_SELECT}></th>
											<th class="text-center"><{$smarty.const._MA_XMDOC_DOCUMENT_NAME}></th>
											<th class="text-center d-none d-sm-table-cell"><{$smarty.const._MA_XMDOC_DOCUMENT_DESC}></th>
											<th class="text-center"><{$smarty.const._MA_XMDOC_FORMDOC_CHECKLINK}></th>
										</tr>
									<thead>
									<tbody>
									<{foreach item=documentitem from=$document}>
										<tr class="table-primary" scope="row">
											<td class="align-middle text-center">

<!--
												<input type="checkbox" name="selDocs[]" id="selDocs<{$documentitem.id}>"  title="Selectio documents" value="<{$documentitem.id}>"  />
-->												
												
												
												<fieldset>
													<div class="mb-3">
														<div class="form-check">
															<input type="checkbox" name="selDocs[]" id="selDocs<{$documentitem.id}>" class="custom-control-input" value="<{$documentitem.id}>" >
															<label class="form-check-label" for="selDocs<{$documentitem.id}>"></label>
														</div>
													</div>
												</fieldset>
											</td>
											<td class="align-middle text-center">
												<{$documentitem.name}><br /><{$documentitem.logo}>
											</td>
											<td class="align-middle text-start d-none d-sm-table-cell">
												<{$documentitem.description|truncateHtml:60:'...'}>
											</td>
											<td class="align-middle text-center">
												<a title="<{$documentitem.name}>" href="<{$xoops_url}>/modules/xmdoc/download.php?cat_id=<{$documentitem.categoryid}>&amp;doc_id=<{$documentitem.id}>" target="_blank">
													<span class="fa fa-link fa-3x"></span>
												</a>
											</td>
										</tr>
									<{/foreach}>
									</tbody>
								</table>
							<input type='submit' class='formButton' name='select'  id='select' value='<{$smarty.const._MA_XMDOC_FORMDOC_SELECT}>' title='<{$smarty.const._MA_XMDOC_FORMDOC_SELECT}>'  />
							</form>
						</div>
					<{/if}>
			</div>
		</div>

		<div class="clear spacer"></div>
		<{if $nav_menu|default:false}>
			<div class="floatright"><{$nav_menu}></div>
			<div class="clear spacer"></div>
		<{/if}>
	</div><!-- .xmdoc -->
	<div id="footer" class="text-center">
		<input value="<{$smarty.const._CLOSE}>" type="button" onclick="window.close();"/>
	</div>
</body>
</html>
