<div class="xoops-comment-body">
    <{section name=i loop=$comments}>
    <div class="row">
        <div class="col-xs-2 col-md-2"><strong><{$lang_poster}></strong></div>
        <div class="col-xs-10 col-md-10"><strong><{$lang_thread}></strong></div>
    </div>
    <{include file="db:system_comment.tpl" comment=$comments[i]}>
    <!-- start comment replies -->
    <{foreach item=reply from=$comments[i].replies}>
    <{assign var="indent" value="`$reply.prefix/25`"}>
    <{assign var="fullcolwidth" value="12"}>

    <{if $indent>3}>
    <{assign var="indent" value="3"}>
    <{/if}>
    <{assign var="replyspace" value="`$fullcolwidth-$indent`"}>

    <div class="row">
        <div class="col-md-offset-<{$indent}> col-md-<{$replyspace}> col-xs-offset-<{$indent}> col-xs-<{$replyspace}>">
        <{include file="db:system_comment.tpl" comment=$reply}>
        </div>
    </div>
    <{/foreach}>
    <{/section}>
    <{if $commentform}>
    <div class="aligncenter">
        <button class="btn-comment btn btn-primary btn-md" data-toggle="modal" data-target="#comments-form">
            <span class="glyphicon glyphicon-comment"></span> Add Comment
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
                    <button type="button" class="btn btn-default" data-dismiss="modal">&times;</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <{/if}>
</div>
