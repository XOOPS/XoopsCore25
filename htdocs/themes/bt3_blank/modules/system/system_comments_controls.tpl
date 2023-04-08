<div class="text-center">
<div class="form-inline">
    <form method="get" action="<{$pageName}>">
    <{$commentModeSelect->render()|replace:'id="com_mode"':''}>
    <{$commentOrderSelect->render()|replace:'id="com_order"':''}>
    <{$commentRefreshButton->render()}>
    <{if ($commentPostButton|default:false) }>
    <{$commentPostButton->render()}>
    <{/if}>
    <{$commentPostHidden}>
    </form>
</div>
</div>
