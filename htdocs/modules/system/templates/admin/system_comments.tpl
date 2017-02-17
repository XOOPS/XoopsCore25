<{includeq file="db:system_header.tpl"}>
<!--Comments-->
<{if $form}>
    <div class="spacer"><{$form}></div>
<{else}>
    <div class="floatleft"><{$form_sort}></div>
    <div class="floatright">
        <div class="xo-buttons">
            <button class="ui-corner-all" onclick="self.location.href='admin.php?fct=comments&op=comments_form_purge'">
                <img src="<{xoAdminIcons clear.png}>" alt="<{$smarty.const._AM_SYSTEM_COMMENTS_FORM_PURGE}>"/>
                <{$smarty.const._AM_SYSTEM_COMMENTS_FORM_PURGE}>
            </button>
        </div>
    </div>
    <div class="clear"></div>
    <table id="xo-comment-sorter" cellspacing="1" class="outer tablesorter">
        <thead>
        <tr>
            <th class="txtcenter width5"><input name='allbox' id='allbox' onclick='xoopsCheckAll("commentslist", "allbox");' type='checkbox'
                                                value='Check All'/></th>
            <th class="txtcenter width5"></th>
            <th class="txtleft"><{$smarty.const._AM_SYSTEM_COMMENTS_TITLE}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_COMMENTS_POSTED}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_COMMENTS_IP}></th>
            <th class="txtcenter"><{$smarty.const._DATE}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_COMMENTS_MODULE}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_COMMENTS_STATUS}></th>
            <th class="txtcenter width10"><{$smarty.const._AM_SYSTEM_COMMENTS_ACTION}></th>
        </tr>
        </thead>
        <form name='commentslist' id='commentslist' action='<{$php_selft}>' method="post">
            <tbody>
            <{foreach item=comments from=$comments}>
                <tr class="<{cycle values='even,odd'}> alignmiddle">
                    <td class="txtcenter"><input type='checkbox' name='commentslist_id[]' id='commentslist_id[]' value='<{$comments.comments_id}>'/></td>
                    <td class="txtcenter"><{$comments.comments_icon}></td>
                    <td>
                        <a href="admin.php?fct=comments&amp;op=comments_jump&amp;com_id=<{$comments.comments_id}>" title="<{$comments.comments_title}>">
                            <{$comments.comments_title}>
                        </a>
                    </td>
                    <td class="txtcenter"><{$comments.comments_poster}></td>
                    <td class="txtcenter"><{$comments.comments_ip}></td>
                    <td class="txtcenter"><{$comments.comments_date}></td>
                    <td class="txtcenter"><{$comments.comments_modid}></td>
                    <td class="txtcenter"><{$comments.comments_status}></td>
                    <td class="xo-actions txtcenter">
                        <img class="cursorpointer" onclick="display_dialog('<{$comments.comments_id}>', true, true, 'slide', 'slide', 300, 500);"
                             src="<{xoAdminIcons display.png}>" alt="<{$smarty.const._AM_SYSTEM_COMMENTS_VIEW}>"
                             title="<{$smarty.const._AM_SYSTEM_COMMENTS_VIEW}>"/>
                        <a href="admin/comments/comment_edit.php?com_id=<{$comments.comments_id}>" title="<{$smarty.const._EDIT}>">
                            <img src="<{xoAdminIcons edit.png}>" alt="<{$smarty.const._EDIT}>">
                        </a>
                        <a href="admin/comments/comment_delete.php?com_id=<{$comments.comments_id}>" title="<{$smarty.const._DELETE}>">
                            <img src="<{xoAdminIcons delete.png}>" alt="<{$smarty.const._DELETE}>">
                        </a>
                    </td>
                </tr>
            <{/foreach}>
            </tbody>
            <tr>
                <td><input type='submit' name='<{$smarty.const._DELETE}>' value='<{$smarty.const._DELETE}>'/></td>
                <td colspan="7">&nbsp;</td>
            </tr>
        </form>
    </table>
    <{foreach item=comments from=$comments_popup}>
        <!--Pop-pup-->
        <div id='dialog<{$comments.comments_id}>' title='<{$comments.comments_title}>' style='display:none;'>
            <img src="<{xoAdminIcons comment.png}>" alt="comments" title="comments" class="xo-commentsimg"/>

            <p><{$comments.comments_text}></p>
        </div>
    <{/foreach}>
    <!--Pop-pup-->
    <div class="txtright"><{$nav}></div>
<{/if}>
