<{if $block.category_count != 0}>
<div class="row">
<{foreach item=category from=$block.category}>
	<{if $block.display == "V"}>		
	<div class="col-12 pt-2 pb-2">
		<div class="card">
			<div class="card-header text-center">
				<a class="text-decoration-none" title="<{$category.title}>" href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>">
					<{$category.title|truncate:25:'...'}>
				</a>
			</div>
			<div class="card-body text-center">
				<div class="row" >
					<div class="col-12" style="height: 150px;">
						<{if $block.show_logo == True}>
						<a title="<{$category.title}>" href="<{$xoops_url}>/modules/xmnews/article.php?news_id=<{$category.id}>">
							<img class="rounded img-fluid mh-100" src="<{$category.logo}>" alt="<{$category.title}>">
						</a>
						<{/if}>
					</div>
					<{if $block.show_description == True}>
					<div class="col-12 pt-2 text-left">	
						<{$category.description|truncateHtml:20:'...'}>
					</div>
					<{/if}>
					<div class="col-12 pt-2">	
						<button class="btn btn-primary btn-sm" onclick=window.location.href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>"><{$smarty.const._MB_XMCONTACT_CONTACT}></button>
					</div>					
				</div>				
			</div>				
		</div>
	</div>
	<{/if}>
	<{if $block.display == "H"}>
		<{if $block.nb_column == 2}>
		<div class="col-12 col-sm-6pt-2 pb-2">
			<div class="card">
				<div class="card-header text-center">
					<a class="text-decoration-none" title="<{$category.title}>" href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>">
						<{$category.title|truncate:25:'...'}>
					</a>
				</div>
				<div class="card-body text-center">
					<div class="row" >
						<div class="col-12" style="height: 150px;">
							<{if $block.show_logo == True}>
							<a title="<{$category.title}>" href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>">
								<img class="rounded img-fluid mh-100" src="<{$category.logo}>" alt="<{$category.title}>">
							</a>
							<{/if}>
						</div>
						<{if $block.show_description == True}>
						<div class="col-12 pt-2 text-left">	
							<{$category.description|truncateHtml:20:'...'}>
						</div>
						<{/if}>
						<div class="col-12 pt-2">	
							<button class="btn btn-primary btn-sm" onclick=window.location.href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>"><{$smarty.const._MB_XMCONTACT_CONTACT}></button>
						</div>					
					</div>				
				</div>				
			</div>
		</div>
		<{/if}>
		<{if $block.nb_column == 3}>
		<div class="col-12 col-sm-6 col-md-4 pt-2 pb-2">
			<div class="card">
				<div class="card-header text-center">
					<a class="text-decoration-none" title="<{$category.title}>" href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>">
						<{$category.title|truncate:25:'...'}>
					</a>
				</div>
				<div class="card-body text-center">
					<div class="row" >
						<div class="col-12" style="height: 150px;">
							<{if $block.show_logo == True}>
							<a title="<{$category.title}>" href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>">
								<img class="rounded img-fluid mh-100" src="<{$category.logo}>" alt="<{$category.title}>">
							</a>
							<{/if}>
						</div>
						<{if $block.show_description == True}>
						<div class="col-12 pt-2 text-left">	
							<{$category.description|truncateHtml:20:'...'}>
						</div>
						<{/if}>
						<div class="col-12 pt-2">	
							<button class="btn btn-primary btn-sm" onclick=window.location.href="<{$xoops_url}>/modules/xmcontact/index.php?op=form&cat_id=<{$category.id}>"><{$smarty.const._MB_XMCONTACT_CONTACT}></button>
						</div>					
					</div>				
				</div>				
			</div>
		</div>
		<{/if}>
	<{/if}>
<{/foreach}>
</div>
<{/if}>
<{if $block.simple_contact|default:false}>
	<div class="col-12 pt-2">	
		<button class="btn btn-primary btn-sm" onclick=window.location.href="<{$xoops_url}>/modules/xmcontact/index.php?op=form"><{$smarty.const._MB_XMCONTACT_CONTACT}></button>
	</div>	
<{/if}>