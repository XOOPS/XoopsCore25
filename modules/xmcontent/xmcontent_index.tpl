<{if $index_header}>
    <div class="row">
        <div class="col-sm-12" style="padding-bottom: 10px; padding-top: 5px;">
            <{$index_header}>
        </div>
    </div>
<{/if}>
<{if $index_content != 0}>
	<{include file="db:xmcontent_viewcontent.tpl"}>
<{else}>
	<{if $content_count != 0}>
		<{foreach item=content from=$content}>
			<{if $index_columncontent == 1}>
				<div class="row" style="padding-bottom: 5px; padding-top: 5px;">
					<div class="col-sm-12">
						<div class="media">
							<img class="mr-3 img-thumbnail" src="<{$content.logo}>" alt="<{$content.title}>" style="max-width:180px; min-width:180px">
							<div class="media-body">
								<h4 class="mt-0"><{$content.title}></h4>
								<{$content.text}>
								<a href="viewcontent.php?content_id=<{$content.id}>">
									<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
								</a>
							</div>
						</div>
					</div>
				</div>
			<{/if}>
			<{if $index_columncontent == 2}>
				<{if $content.row == true}>
					<div class="row" style="margin-top: 5px;">
				<{/if}>
				<div class="col-sm-6">
					<div class="media">
						<img class="mr-3 img-thumbnail" src="<{$content.logo}>" alt="<{$content.title}>" style="max-width:160px; min-width:160px">
						<div class="media-body">
							<h4 class="mt-0"><{$content.title}></h4>
							<{$content.text}>
							<a href="viewcontent.php?content_id=<{$content.id}>">
								<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
							</a>
						</div>
					</div>
				</div>
				<{if $content.count is div by $index_columncontent || $content.end == true}>
					</div>
				<{/if}>
			<{/if}>
			<{if $index_columncontent == 3}>
				<{if $content.row == true}>
					<div class="row" style="margin-top: 5px;">
				<{/if}>
				<div class="col-sm-4">
					<div class="media">
						<img class="mr-3 img-thumbnail" src="<{$content.logo}>" alt="<{$content.title}>" style="max-width:120px; min-width:120px">
						<div class="media-body">
							<h4 class="mt-0"><{$content.title}></h4>
							<{$content.text}>
							<a href="viewcontent.php?content_id=<{$content.id}>">
								<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
							</a>
						</div>
					</div>
				</div>
				<{if $content.count is div by $index_columncontent || $content.end == true}>
					</div>
				<{/if}>
			<{/if}>
			<{if $index_columncontent == 4}>
				<{if $content.row == true}>
					<div class="row" style="margin-top: 5px;">
				<{/if}>
				<div class="col-sm-3">
					<div class="media">
						<img class="mr-3 img-thumbnail" src="<{$content.logo}>" alt="<{$content.title}>" style="max-width:80px; min-width:80px">
						<div class="media-body">
							<h4 class="mt-0"><{$content.title}></h4>
							<{$content.text}>
							<a href="viewcontent.php?content_id=<{$content.id}>">
								<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTENT_INDEX_MORE}></button>
							</a>
						</div>
					</div>
				</div>
				<{if $content.count is div by $index_columncontent || $content.end == true}>
					</div>
				<{/if}>
			<{/if}>
		<{/foreach}>
		<{if $nav_menu}>
			<div class="row">
				<div class="col-sm-12" style="padding-bottom: 10px; padding-top: 5px; padding-right: 60px; text-align: right;">
					<{$nav_menu}>
				</div>
			</div>
		<{/if}>
	<{/if}>
<{/if}>

<{if $index_footer}>
    <div class="row" style="padding-bottom: 5px; padding-top: 5px;">
        <div class="col-sm-12">
            <{$index_footer}>
        </div>
    </div>
<{/if}>
