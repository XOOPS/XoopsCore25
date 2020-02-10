<div class="xoops-comment-body">
    <{section name=i loop=$comments}>
        <div class="row">
            <div class="col-2 col-md-2"><strong><{$lang_poster}></strong></div>
            <div class="col-10 col-md-10"><strong><{$lang_thread}></strong></div>
        </div>
        <{include file="db:system_comment.tpl" comment=$comments[i]}>

        <{if $show_threadnav == true}>
            <a href="<{$comment_url}>" title="<{$lang_top}>"><{$lang_top}></a>
            <a href="<{$comment_url}>&amp;com_id=<{$comments[i].pid}>&amp;com_rootid=<{$comments[i].rootid}>#newscomment<{$comments[i].pid}>"><{$lang_parent}></a>
        <{/if}>

        <{if $comments[i].show_replies == true}>
            <!-- start comment tree -->
            <div class="row">
                <div class="col-md-4">
                    <strong><{$lang_subject}></strong>
                </div>

                <div class="col-md-4">
                    <strong><{$lang_poster}></strong>
                </div>
                <div class="col-md-4">
                    <strong><{$lang_posted}></strong>
                </div>
            </div>
            <{foreach item=reply from=$comments[i].replies}>
                <div class="row">
                    <div class="col-md-4">
                        <{$reply.prefix}> <a href="<{$comment_url}>&amp;com_id=<{$reply.id}>&amp;com_rootid=<{$reply.root_id}>" title=""><{$reply.title}></a>
                    </div>

                    <div class="col-md-4">
                        <{$reply.poster.uname}>
                    </div>

                    <div class="col-md-4">
                        <{$reply.date_posted}>
                    </div>
                </div>
            <{/foreach}>
            <!-- end comment tree -->
        <{/if}>
    <{/section}>

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
