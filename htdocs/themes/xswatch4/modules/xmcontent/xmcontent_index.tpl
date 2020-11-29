<div class="container">
	<{if $index_header|default:'' != ''}>
		<div class="row">
			<div class="col" style="padding-bottom: 10px; padding-top: 5px;">
				<{$index_header}>
			</div>
		</div>
	<{/if}>

	<{if $index_content != 0}>
		<{include file="db:xmcontent_viewcontent.tpl"}>
	<{else}>
		<{if $content_count != 0}>
			<div class="row">
				<{foreach item=content from=$content}>
					<{if $index_columncontent == 1}>
						<div class="col-3 col-md-4 col-lg-3 text-center" style="padding-bottom: 5px; padding-top: 5px;">
							<img class="rounded img-fluid" src="<{$content.logo}>" alt="<{$content.title}>">
						</div>
						<div class="col-9 col-md-8 col-lg-9 " style="padding-bottom: 5px; padding-top: 5px;">
							<h4 class="mt-0"><{$content.title}></h4>
							<{$content.text}>
							<a href="viewcontent.php?content_id=<{$content.id}>">
								<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
							</a>
						</div>
					<{/if}>
					<{if $index_columncontent == 2}>
						<div class="col-3 col-md-2 col-lg-3 text-center" style="padding-bottom: 5px; padding-top: 5px;">
							<img class="rounded img-fluid" src="<{$content.logo}>" alt="<{$content.title}>">
						</div>
						<div class="col-9 col-md-4 col-lg-3" style="padding-bottom: 5px; padding-top: 5px;">
							<h4 class="mt-0"><{$content.title}></h4>
							<{$content.text}>
							<a href="viewcontent.php?content_id=<{$content.id}>">
								<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
							</a>
						</div>
					<{/if}>
					<{if $index_columncontent == 3}>
						<div class="col-3 col-md-2 text-center" style="padding-bottom: 5px; padding-top: 5px;">
							<img class="rounded img-fluid" src="<{$content.logo}>" alt="<{$content.title}>">
						</div>
						<div class="col-9 col-md-2" style="padding-bottom: 5px; padding-top: 5px;">
							<h4 class="mt-0"><{$content.title}></h4>
							<{$content.text}>
							<a href="viewcontent.php?content_id=<{$content.id}>">
								<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
							</a>
						</div>
					<{/if}>
					<{if $index_columncontent == 4}>
						<div class="col-3 col-md-1 text-center" style="padding-bottom: 5px; padding-top: 5px;">
							<img class="rounded img-fluid" src="<{$content.logo}>" alt="<{$content.title}>">
						</div>
						<div class="col-9 col-md-2" style="padding-bottom: 5px; padding-top: 5px;">
							<h4 class="mt-0"><{$content.title}></h4>
							<{$content.text}>
							<a href="viewcontent.php?content_id=<{$content.id}>">
								<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
							</a>
						</div>
					<{/if}>
					
				<{/foreach}>
			</div>
			<{if $nav_menu|default:false}>
				<div class="row">
					<div class="col-sm-12" style="padding-bottom: 10px; padding-top: 5px; padding-right: 60px; text-align: right;">
						<{$nav_menu}>
					</div>
				</div>
			<{/if}>
		<{/if}>
	<{/if}>

	<{if $index_footer|default:'' != ''}>
		<div class="row" style="padding-bottom: 5px; padding-top: 5px;">
			<div class="col">
				<{$index_footer}>
			</div>
		</div>
	<{/if}>
</div>
