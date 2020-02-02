<{include file='db:wggallery_header.tpl'}>

<{if $albums}>
	<div class='card panel-<{$panel_type}>'>
		<div class='card-header wgg-cats-header'><{$index_alb_title}></div>
		<div class='card-body'>
			<{foreach name=album item=album from=$albums}>
                <{include file='db:wggallery_albumitem_hovereffectideas.tpl' album=$album}>
			<{/foreach}>
			<{if $pagenav_albums}>
				<div class='xo-pagenav floatright'><{$pagenav_albums}></div>
				<div class='clear spacer'></div>
			<{/if}>
		</div>
	</div>
<{/if}>
<{if $categories}>
	<div class='panel panel-<{$panel_type}>'>
		<div class='panel-heading wgg-cats-header'><{$index_cats_title}></div>
		<div class='panel-body'>
			<{foreach name=category item=category from=$categories}>
                <{if $category.newrow}><div class="grid"><{/if}>
                <{include file='db:wggallery_categoryitem_hovereffectideas.tpl' category=$category}>
                <{if $category.linebreak}></div><div class='clear'>&nbsp;</div><{/if}>
			<{/foreach}>
			<{if $pagenav_cats}>
				<div class='xo-pagenav floatright'><{$pagenav_cats}></div>
				<div class='clear spacer'></div>
			<{/if}>
		</div>
	</div>
<{/if}>

<{if $alb_pid}>
	<div class='clear'>&nbsp;</div>
	<div class='wgg-goback'>
		<a class='btn btn-secondary wgg-btn' href='index.php?op=list<{if $subm_id}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_BACK}>'>
			<img class='wgg-btn-icon' src='<{$wggallery_icon_url_16}>back.png' alt='<{$smarty.const._CO_WGGALLERY_BACK}>'>
			<{if $displayButtonText}><{$smarty.const._CO_WGGALLERY_BACK}><{/if}>
		</a>
	</div>
<{/if}>

<div class='clear'>&nbsp;</div>

<{include file='db:wggallery_footer.tpl'}>
