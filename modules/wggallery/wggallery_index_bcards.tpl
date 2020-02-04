<{include file="db:wggallery_header.tpl"}>

<div class="container">
<{if $albums}>
	<div>
		<div class="row alert alert-info border wgg-cats-header" role="alert"><{$index_alb_title}></div>
		<div class="row">
			<{foreach item=album from=$albums}>
                <{include file="db:wggallery_albumitem_bcards.tpl" album=$album}>
			<{/foreach}>
		</div>
	</div>
	<{if $pagenav_albums}>
	<div class="row">
		<div class="col mb-2">
			<div class="generic-pagination xo-pagenav pull-right"><{$pagenav_albums|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}></div>
		</div>
	</div>
	<{/if}>
<{/if}>
<{if $categories}>
	<div>
		<div class="row alert alert-info border wgg-cats-header" role="alert"><{$index_cats_title}></div>
		<div class="row">
			<{foreach item=category from=$categories}>
			<{include file="db:wggallery_categoryitem_bcards.tpl" category=$category}>
			<{/foreach}>
		</div>
	</div>
	<{if $pagenav_cats}>
	<div class="row">
		<div class="col mb-2">
		<div class="generic-pagination xo-pagenav pull-right"><{$pagenav_cats|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}></div>
		</div>
	</div>
	<{/if}>
<{/if}>
</div>

<{if $alb_pid}>
	<div class="clear">&nbsp;</div>
	<div class="wgg-goback">
		<a class="btn btn-secondary wgg-btn" href="index.php?op=list<{if $subm_id}>&amp;subm_id=<{$subm_id}><{/if}>" title="<{$smarty.const._CO_WGGALLERY_BACK}>">
			<img class="wgg-btn-icon" src="<{$wggallery_icon_url_16}>back.png" alt="<{$smarty.const._CO_WGGALLERY_BACK}>">
			<{if $displayButtonText}><{$smarty.const._CO_WGGALLERY_BACK}><{/if}>
		</a>
	</div>
<{/if}>
<div class="clear">&nbsp;</div>
<{include file="db:wggallery_footer.tpl"}>
