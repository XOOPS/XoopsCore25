<a title="<{$smarty.const._MD_NEWBB_SUBFORUMS}>" data-toggle="collapse" href="#xoops-subforum" class="btn btn-primary pull-right">
    <span class="fa fa-plus"></span> <{$smarty.const._MD_NEWBB_SUBFORUMS}>
</a>
<div class="newbb-subforum mb10 clearfix">
    <div class="collapse" id="xoops-subforum">

        <table class="table table-hover">
            <thead>
            <tr class="table-sm">
                <th scope="col"> </th>
                <th scope="col"><{$smarty.const._MD_NEWBB_FORUM}></th>
                <th class="d-none d-sm-table-cell" scope="col"><{$smarty.const._MD_NEWBB_TOPICS}></th>
                <th class="d-none d-sm-table-cell" scope="col"><{$smarty.const._MD_NEWBB_POSTS}></th>
                <th scope="col"><{$smarty.const._MD_NEWBB_LASTPOST}></th>
            </tr>
            </thead>
            <{foreach item=sforum from=$subforum}>
            <tbody>
                <tr>
                    <td><{$sforum.forum_folder}></td>
                    <td><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$sforum.forum_id}>" title="<{$sforum.forum_name}>"><{$sforum.forum_name}></a>
                        <br><small><{$sforum.forum_desc}></small></td>
                    <td class="d-none d-sm-table-cell"><{$sforum.forum_topics}></td>
                    <td class="d-none d-sm-table-cell"><{$sforum.forum_posts}></td>
                    <td>
                        <{if $sforum.forum_lastpost_subject}>
                        <{$sforum.forum_lastpost_time}> <{$smarty.const._MD_NEWBB_BY}> <{$sforum.forum_lastpost_user}>
                    <br>
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?post_id=<{$sforum.forum_lastpost_id}>">
                            <{$sforum.forum_lastpost_subject}>
                            <span class="fa fa-forward" aria-hidden="true" title="<{$smarty.const._MD_NEWBB_GOTOLASTPOST}>"></span>
                        </a>
                        <{else}>
                        <{$smarty.const._MD_NEWBB_NOTOPIC}>
                        <{/if}>
                    </td>
                </tr>
                <{/foreach}>
            </tbody>
        </table>
    </div><!-- #xoops-subforum -->
</div><!-- .xoops-newbb-subforum -->
