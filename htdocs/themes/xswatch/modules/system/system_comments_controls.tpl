<div class="form-inline col-md-12" style="text-align: initial;">
    <form method="get" action="<{$pageName}>">
        <div class="left col-md-8" style="display:inline-block">
            <{$smarty.const.THEME_COMMENT_OPTIONS}>
            <{$commentModeSelect->render()}>
            <{$commentOrderSelect->render()}>
            <{$commentRefreshButton->render()}>
        </div>
    <{if ($commentPostButton|default:false) }>
            <div class="right col-md-4" style="display:inline-block">
                <{$commentPostButton->render()}>
            </div>
    <{/if}>
    <{$commentPostHidden}>
    </form>
</div>
