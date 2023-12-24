<{if $down_xmsocial.perm == true}>
<small>
	<div id="unit_long<{$down_xmsocial.itemid}>">
		<div id="unit_ul<{$down_xmsocial.itemid}>" class="xmsocial_unit-rating">
			<div class="xmsocial_current-rating" style="width:<{$down_xmsocial.size}>;"></div>
			<{foreach item=itemstars from=$down_xmsocial.stars}>
			<div>
				<a class="xmsocial_r<{$itemstars}>-unit rater" href='<{$xoops_url}>/modules/xmsocial/rate.php?mod=<{$down_xmsocial.module}>&amp;itemid=<{$down_xmsocial.itemid}>&amp;rating=<{$itemstars}>&amp;opt=<{$down_xmsocial.options}>' title="<{$itemstars}>" rel="nofollow"><{$itemstars}></a>
			</div>
			<{/foreach}>
		</div>
		<div>
			<{$smarty.const._MA_XMSOCIAL_RATING_RATING}>: <{$down_xmsocial.rating}> (<{$down_xmsocial.votes}> <{$down_xmsocial.text}>)
		</div>
	</div>
</small>
<{else}>
<i class="fa fa-star" aria-hidden="true"></i>
<{$smarty.const._MA_XMSOCIAL_RATING_RATING}>: <{$down_xmsocial.rating}> (<{$down_xmsocial.votes}> <{$down_xmsocial.text}>)
<{/if}>