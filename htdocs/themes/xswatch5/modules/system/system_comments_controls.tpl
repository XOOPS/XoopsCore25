<!-- Comment Option Button trigger modal -->
<div class="mb-2">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#commentOptionModalLong">
    <{$smarty.const.THEME_COMMENT_OPTIONS}>
</button>
</div>

<!-- Modal -->
<div class="modal fade" id="commentOptionModalLong" tabindex="-1" role="dialog" aria-labelledby="commentOptionModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentOptionModalLongTitle"><{$smarty.const.THEME_COMMENT_OPTIONS}></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="get" action="<{$pageName}>">
                    <div class="mb-3">
                    <{$commentModeSelect->render()|replace:'id="com_mode"':''}>
                    </div>
                    <div class="mb-3">
                    <{$commentOrderSelect->render()|replace:'id="com_order"':''}>
                    </div>
                    <div class="mb-3">
                    <{$commentRefreshButton->render()}>
                    </div>
                    <{if ($commentPostButton|default:false) }>
                    <div class="mb-3">
                    <{$commentPostButton->render()}>
                    </div>
                    <{/if}>
                    <{$commentPostHidden}>
                </form>
            </div>
        </div>
    </div>
</div>
