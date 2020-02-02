<{include file='db:wggallery_header.tpl'}>

<{if $albums}>
	<div class='card panel-<{$panel_type}>'>
		<div class='card-header wgg-cats-header'><{$index_alb_title}></div>
		<div class='row card-body'>
			<{foreach item=album from=$albums}>
                <{include file='db:wggallery_albumitem_simple.tpl' album=$album}>
			<{/foreach}>
			<div class='clear'>&nbsp;</div>
			<{if $pagenav_albums}>
			<div class="col">
				<div class="generic-pagination xo-pagenav pull-right"><{$pagenav_albums|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}></div>
			</div>
			<{/if}>
		</div>
	</div>
<{/if}>
<{if $categories}>
	<div class='card panel-<{$panel_type}>'>
		<div class='card-header wgg-cats-header'><{$index_cats_title}></div>
		<div class='card-body'>
			<{foreach item=category from=$categories}>
                <{if $number_cols_cat == 6}>
                    <div class='col-xs-12 col-sm-2'>
                <{elseif $number_cols_cat == 4}>
                    <div class='col-xs-12 col-sm-3'>
                <{elseif $number_cols_cat == 3}>
                    <div class='col-xs-12 col-sm-4'>
                <{elseif $number_cols_cat == 2}>
                    <div class='col-xs-12 col-sm-6'>
                <{else}>
                    <div class='col-xs-12 col-sm-12'>
                <{/if}>
                    <{include file='db:wggallery_categoryitem_simple.tpl' category=$category}>
                </div>
                <{if $category.linebreak}>
                    <div class='clear linebreak'>&nbsp;</div>
                <{/if}>
			<{/foreach}>
			<{if $pagenav_cats}>
				<div class="col">
					<div class="generic-pagination xo-pagenav pull-right"><{$pagenav_cats|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}></div>
				</div>
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
