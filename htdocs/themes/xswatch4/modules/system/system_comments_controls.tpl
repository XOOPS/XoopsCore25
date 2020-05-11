<{$smarty.const.THEME_COMMENT_OPTIONS}>
<form method="get" action="<{$pageName}>">
	<div class="form-row mt-1">
		<div class="form-group col-md-3">
			<{$commentModeSelect->render()}>
		</div>
		<div class="form-group col-md-6 col-lg-5">
			<{$commentOrderSelect->render()}>
		</div>
		<div class="form-group col text-md-left text-center">
			<{$commentRefreshButton->render()}>
		</div>
		<{if ($commentPostButton|default:false) }>
			<div class="form-group col text-nowrap">
				<{$commentPostButton->render()}>
			</div>
		<{/if}>
	</div>	
	<{$commentPostHidden}>
</form>
