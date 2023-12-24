<{if isset($isAdmin) && $isAdmin == 1}>
    <div class="publisher_adminlinks"><{$publisher_adminpage}></div><{/if}>

<{if (!empty($commentatarticlelevel) && !empty($item.cancomment)) || isset($com_rule) && $com_rule != 0}>
    <table border="0" width="100%" cellspacing="1" cellpadding="0" align="center">
    <tr>
        <td colspan="3" align="left">
            <div style="text-align: center; padding: 3px; margin:3px;"> <{$commentsnav}> <{$lang_notice}></div>
            <div style="margin:3px; padding: 3px;">
                <!-- start comments loop -->
                <{if isset($comment_mode)}>
                    <{if $comment_mode == "flat"}>
                        <{include file="db:system_comments_flat.tpl"}>
                    <{elseif $comment_mode == "thread"}>
                        <{include file="db:system_comments_thread.tpl"}>
                    <{elseif $comment_mode == "nest"}>
                        <{include file="db:system_comments_nest.tpl"}>
                    <{/if}>
                <{/if}>
                <!-- end comments loop -->
            </div>
        </td>
    </tr>
    </table><{/if}>

<{if !empty($rssfeed_link)}>
    <div id="publisher_rpublisher_feed"><{$rssfeed_link}></div><{/if}>

<{include file='db:system_notification_select.tpl'}>
