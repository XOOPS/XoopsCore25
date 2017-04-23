<{if $xoops_notification.show}>
    <form name="notification_select" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/<{$xoops_notification.target_page}>" method="post">
        <h4 class="txtcenter"><{$lang_activenotifications}>    </h4>
        <!-- irmtfan remove value=xoops_url -->
        <input type="hidden" name="not_redirect" value="<{$xoops_notification.redirect_script}>"/>
        <input type="hidden" name="XOOPS_TOKEN_REQUEST"
               value="<{php}>echo $GLOBALS['xoopsSecurity']->createToken();<{/php}>"/>
        <table class="outer">
            <tr>
                <th colspan="3"><{$lang_notificationoptions}></th>
            </tr>
            <tr>
                <td class="head"><{$lang_category}></td>
                <td class="head"><input name="allbox" id="allbox"
                                        onclick="xoopsCheckAll('notification_select','allbox');" type="checkbox"
                                        value="<{$lang_checkall}>"/></td>
                <td class="head"><{$lang_events}></td>
            </tr>
            <{foreach name=outer item=category from=$xoops_notification.categories}>
                <{foreach name=inner item=event from=$category.events}>
                    <tr>
                        <{if $smarty.foreach.inner.first}>
                            <td class="even" rowspan="<{$smarty.foreach.inner.total}>"><{$category.title}></td>
                        <{/if}>
                        <td class="odd">
                            <{counter assign=index}>
                            <input type="hidden" name="not_list[<{$index}>][params]"
                                   value="<{$category.name}>,<{$category.itemid}>,<{$event.name}>"/>
                            <input type="checkbox" id="not_list[]" name="not_list[<{$index}>][status]" value="1"
                                   <{if $event.subscribed}>checked="checked"<{/if}> />
                        </td>
                        <td class="odd"><{$event.caption}></td>
                    </tr>
                <{/foreach}>
            <{/foreach}>
            <tr>
                <td class="foot txtcenter" colspan="3"><input type="submit" name="not_submit" value="<{$lang_updatenow}>"/></td>
            </tr>
        </table>
        <div class="txtcenter"> 
            <{$lang_notificationmethodis}>:&nbsp;<{$user_method}>&nbsp;&nbsp;
            [<a href="<{$editprofile_url}>" title="<{$lang_change}>"><{$lang_change}></a>]
        </div>
    </form>
<{/if}>
