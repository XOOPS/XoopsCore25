<!-- Comment Option Button trigger modal -->
<div class="mb-2">
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#commentOptionModalLong">
    <{$smarty.const.THEME_COMMENT_OPTIONS}>
</button>
</div>

<!-- Modal -->
<div class="modal fade" id="commentOptionModalLong" tabindex="-1" role="dialog" aria-labelledby="commentOptionModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentOptionModalLongTitle"><{$smarty.const.THEME_COMMENT_OPTIONS}></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="get" action="<{$pageName}>">
                    <div class="form-group">
                    <{$commentModeSelect->render()|replace:'id="com_mode"':''}>
                    </div>
                    <div class="form-group">
                    <{$commentOrderSelect->render()|replace:'id="com_order"':''}>
                    </div>
                    <div class="form-group">
                    <{$commentRefreshButton->render()}>
                    </div>
                    <{if !empty($commentPostButton)}>
                    <div class="form-group">
                    <{$commentPostButton->render()}>
                    </div>
                    <{/if}>
                    <{$commentPostHidden}>
                </form>
            </div>
        </div>
    </div>
</div>
