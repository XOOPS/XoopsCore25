<a title="<{$smarty.const._MD_SUBFORUMS}>" data-toggle="collapse" href="#xoops-subforum" class="btn btn-primary pull-right">
    <span class="glyphicon glyphicon-plus-sign"></span> <{$smarty.const._MD_SUBFORUMS}>
</a>
<div class="newbb-subforum mb10 clearfix">
    <div class="collapse" id="xoops-subforum">
        <{foreachq item=sforum from=$subforum}>
        <ul class="subforum-loop list-unstyled clearfix">
        <li class="col-xs-12 col-md-6">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$sforum.forum_id}>" title="<{$sforum.forum_name}>">
                <{$sforum.forum_folder}> <strong><{$sforum.forum_name}></strong>
            </a>
        </li>

        <li class="col-xs-12 col-md-6 text-right">
            <strong><{$smarty.const._MD_LASTPOST}>:</strong>
            <{$sforum.forum_lastpost_time}> <strong><{$smarty.const._MD_BY}></strong> <{$sforum.forum_lastpost_user}>
        </li>

        <li>
        <ul class="list-inline col-md-6 hidden-xs">
            <{if $sforum.forum_moderators}><li><span class="label label-info"><{$smarty.const._MD_MODERATOR}>: <{$sforum.forum_moderators}></span></li><{/if}>

            <li><span class="label label-info"><{$smarty.const._MD_TOPICS}>: <{$sforum.forum_topics}></span></li>

            <li><span class="label label-info"><{$smarty.const._MD_POSTS}>: <{$sforum.forum_posts}></span></li>

            <!-- If subforum description -->
            <{if $sforum.forum_desc != ""}>
                <li>
                    <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#subforum-<{$sforum.forum_id}>">
                        <span class="glyphicon glyphicon-info-sign"></span>
                    </button>
                </li>
            <{/if}>
        </ul>

        <ul class="list-inline col-md-6 text-right">
        <{if $sforum.forum_lastpost_subject}>
            <li>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?post_id=<{$sforum.forum_lastpost_id}>">
                    <{$sforum.forum_lastpost_subject}>
                    <{$sforum.forum_lastpost_icon}>
                </a>
            </li>
        <{else}>
            <li><{$smarty.const._MD_NONEWPOSTS}></li>
        <{/if}>
        </ul>
        </li>

        </ul><!-- .subforum-loop -->

        <!-- Modal -->
        <div class="modal fade" id="subforum-<{$sforum.forum_id}>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><{$sforum.forum_name}></h4>
              </div>
              <div class="modal-body">
                <p><{$sforum.forum_desc}></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><{$smarty.const.THEME_CLOSE}></button>
                <button type="button" class="btn btn-primary"><{$smarty.const.THEME_GOTOTHEFORUM}></button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <{/foreach}>
    </div><!-- #xoops-subforum -->
</div><!-- .xoops-newbb-subforum -->
