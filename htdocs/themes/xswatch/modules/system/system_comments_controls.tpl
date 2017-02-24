<div class="text-center">
<div class="form-inline">
    <form method="get" action="<{$pageName}>">
    <{$commentModeSelect->render()}>
    <{$commentOrderSelect->render()}>
    <{$commentRefreshButton->render()}>
    <{if ($commentPostButton|default:false) }>
    <{$commentPostButton->render()}>
    <{/if}>
    <{$commentPostHidden}>
    </form>
</div>
</div>
