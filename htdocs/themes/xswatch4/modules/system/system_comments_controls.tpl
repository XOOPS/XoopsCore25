<div class="row d-flex justify-content-center ml-2 ml-sm-0">
    <form class="form-inline" method="get" action="<{$pageName}>">
        <div class="text-center mr-3"><{$smarty.const.THEME_COMMENT_OPTIONS}></div>
        <div class="form-group ">
        <{$commentModeSelect->render()}><div class="ml-1"></div>
        <{$commentOrderSelect->render()}><div class="ml-1"></div>
        <{$commentRefreshButton->render()}><div class="ml-1"></div>
        <{if ($commentPostButton|default:false) }><{$commentPostButton->render()}><{/if}>
    </div>
<{$commentPostHidden}>
</form>
</div>
