<div class="row d-flex justify-content-center">
<form class="form-inline" method="get" action="<{$pageName}>">
    <div class="form-group">
        <{$smarty.const.THEME_COMMENT_OPTIONS}>&nbsp;
        <{$commentModeSelect->render()}>&nbsp;
        <{$commentOrderSelect->render()}>&nbsp;
        <{$commentRefreshButton->render()}>&nbsp;

        <{if ($commentPostButton|default:false) }>
            <{$commentPostButton->render()}>
        <{/if}>
    </div>
<{$commentPostHidden}>
</form>
</div>
