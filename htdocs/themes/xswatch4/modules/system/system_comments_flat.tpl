<div class="xoops-comment-body">
    <{if ($comments|default:false) }>
    <div class="row">
        <div class="col-2 col-md-2"><strong><{$lang_poster}></strong></div>
        <div class="col-10 col-md-10"><strong><{$lang_thread}></strong></div>
    </div>
    <{foreach item=comment from=$comments}>
        <{include file="db:system_comment.tpl" comment=$comment}>
    <{/foreach}>
    <{/if}>
    <{if $commentform}>
        <div class="aligncenter">
            <button class="btn-comment btn btn-primary btn-md" data-toggle="modal" data-target="#comments-form">
                <span class="fa fa-comment"></span> <{$smarty.const.THEME_COMMENT_ADD}>
            </button>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="comments-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog comments-modal">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="xoops-comment-form">
                            <{$commentform}>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">&times;</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    <{/if}>
</div>