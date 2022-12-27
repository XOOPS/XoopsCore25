<{include file="db:system_header.tpl"}>

<{if $users_display|default:false == true}>
    <!--Display form sort-->
    <div class="xo-headercontent">
        <div class="floatleft"><{$form_sort}></div>
        <div class="floatright">
            <div class="xo-buttons">
                <a class="ui-corner-all tooltip" href="admin.php?fct=users&amp;op=users_synchronize&amp;status=2"
                   title="<{$smarty.const._AM_SYSTEM_USERS_SYNCHRONIZE}>">
                    <img src="<{xoAdminIcons 'reload.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_SYNCHRONIZE}>"/>
                    <{$smarty.const._AM_SYSTEM_USERS_SYNCHRONIZE}>
                </a>
                <a class="ui-corner-all tooltip" href="admin.php?fct=users&amp;op=users_add" title="<{$smarty.const._AM_SYSTEM_USERS_ADDUSER}>">
                    <img src="<{xoAdminIcons 'user_add.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_ADDUSER}>"/>
                    <{$smarty.const._AM_SYSTEM_USERS_ADDUSER}>
                </a>
            </div>
        </div>
    </div>
    <div class="clear">&nbsp;</div>
    <table id="xo-users-sorter" cellspacing="1" class="outer tablesorter">
        <thead>
        <tr>
            <th class="txtcenter width3"><input name='allbox' id='allbox' onclick='xoopsCheckAll("memberslist", "allbox");' type='checkbox'
                                                value='Check All'/></th>
            <th class="txtcenter width5"><{$smarty.const._AM_SYSTEM_USERS_STATUS}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_USERS_UNAME}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_USERS_REALNAME}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_USERS_EMAIL}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_USERS_REG_DATE}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_USERS_LAST_LOGIN}></th>
            <th class="txtcenter"><{$smarty.const._AM_SYSTEM_USERS_POSTS}></th>
            <th class="txtcenter" width='11%'><{$smarty.const._AM_SYSTEM_USERS_ACTION}></th>
        </tr>
        </thead>
        <!--Display data-->
        <{if $users_count == true}>
            <form name='memberslist' id='memberslist' action='<{xoAppUrl "modules/system/admin.php?fct=users"}>' method='POST'>
                <tbody>
                <{foreach item=user from=$users}>
                    <tr class="<{cycle values='even,odd'}> alignmiddle">
                        <td class="txtcenter"><{if $user.checkbox_user}><input type='checkbox' name='memberslist_id[]' id='memberslist_id[]'
                                                                                value='<{$user.uid}>'/><{/if}>
                        </td>
                        <td class="txtcenter"><img class="xo-imgmini" src="<{$user.group}>" alt=""/></td>
                        <td class="txtcenter"><a title="<{$user.uname}>" href="<{$xoops_url}>/userinfo.php?uid=<{$user.uid}>"><{$user.uname}></a></td>
                        <td class="txtcenter"><{$user.name}></td>
                        <td class="txtcenter"><{$user.email}></td>
                        <td class="txtcenter"><{$user.reg_date}></td>
                        <td class="txtcenter"><{$user.last_login}></td>
                        <td class="txtcenter">
                            <div id="display_post_<{$user.uid}>"><{$user.posts}></div>
                            <div id='loading_<{$user.uid}>' class="txtcenter" style="display:none;"><img src="./images/mimetypes/spinner.gif" title="Loading"
                                                                                                          alt="Loading" width="12px"/></div>
                        </td>
                        <td class="xo-actions txtcenter">
                            <{if $user.user_level > 0}>
                                <img class="tooltip" onclick="display_post('<{$user.uid}>');" src="<{xoAdminIcons 'reload.png'}>"
                                     alt="<{$smarty.const._AM_SYSTEM_USERS_SYNCHRONIZE}>" title="<{$smarty.const._AM_SYSTEM_USERS_SYNCHRONIZE}>"/>
                                <img class="tooltip" onclick="display_dialog('<{$user.uid}>', true, true, 'slide', 'slide', 300, 400);"
                                     src="<{xoAdminIcons 'display.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_VIEW}>"
                                     title="<{$smarty.const._AM_SYSTEM_USERS_VIEW}>"/>
                                <a class="tooltip" href="admin.php?fct=users&amp;op=users_edit&amp;uid=<{$user.uid}>"
                                   title="<{$smarty.const._AM_SYSTEM_USERS_EDIT}>">
                                    <img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_EDIT}>"/></a>
                                <a class="tooltip" href="admin.php?fct=users&amp;op=users_delete&amp;uid=<{$user.uid}>"
                                   title="<{$smarty.const._AM_SYSTEM_USERS_DEL}>">
                                    <img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_DEL}>"/></a>
                            <{else}>
                                <a class="tooltip" href="admin.php?fct=users&amp;op=users_active&amp;uid=<{$user.uid}>"
                                   title="<{$smarty.const._AM_SYSTEM_USERS_ACTIVE}>">
                                    <img src="<{xoAdminIcons 'xoops/active_user.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_ACTIVE}>"/></a>
                                <img class="tooltip" onclick="display_dialog('<{$user.uid}>', true, true, 'slide', 'slide', 300, 400);"
                                     src="<{xoAdminIcons 'display.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_VIEW}>"
                                     title="<{$smarty.const._AM_SYSTEM_USERS_VIEW}>"/>
                                <a class="tooltip" href="admin.php?fct=users&amp;op=users_edit&amp;uid=<{$user.uid}>"
                                   title="<{$smarty.const._AM_SYSTEM_USERS_EDIT}>">
                                    <img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_EDIT}>"/></a>
                                <a class="tooltip" href="admin.php?fct=users&amp;op=users_delete&amp;uid=<{$user.uid}>"
                                   title="<{$smarty.const._AM_SYSTEM_USERS_DEL}>">
                                    <img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_USERS_DEL}>"/></a>
                            <{/if}>
                        </td>
                    </tr>
                <{/foreach}>
                </tbody>
                <tr>
                    <td class='txtleft' colspan='6'>
                        <select name='fct' onChange='changeDisplay (this.value, "groups", "edit_group")'>
                            <option value=''>---------</option>
                            <option value='mailusers'><{$smarty.const._AM_SYSTEM_USERS_SENDMAIL}></option>
                            <option value='groups'><{$smarty.const._AM_SYSTEM_USERS_EDIT_GROUPS}></option>
                            <option value='users'><{$smarty.const._AM_SYSTEM_USERS_DELETE}></option>
                        </select>&nbsp;
                        <select name='edit_group' id='edit_group' onChange='changeDisplay (this.value, this.value, "selgroups")' style="display:none;">
                            <option value=''>---------</option>
                            <option value='add_group'><{$smarty.const._AM_SYSTEM_USERS_ADD_GROUPS}></option>
                            <option value='delete_group'><{$smarty.const._AM_SYSTEM_USERS_DELETE_GROUPS}></option>
                        </select>
                        <{$form_select_groups}>
                        <{$form_token}>
                        <input type="hidden" name="op" value="action_group">
                        <input type='submit' name='Submit'/>
                    </td>
                </tr>
            </form>
        <{/if}>
        <!--No found-->
        <{if $users_no_found|default:false == true}>
            <tr class="<{cycle values='even,odd'}> alignmiddle">
                <td colspan='8' class="txtcenter"><{$smarty.const._AM_SYSTEM_USERS_NO_FOUND}></td>
            </tr>
        <{/if}>
    </table>
    <!--Pop-pup-->
    <{if $users_count|default:false == true}>
        <{foreach item=users from=$users_popup}>
            <div id="dialog<{$users.uid}>" title="<{$users.uname}>" style='display:none;'>
                <table>
                    <tr>
                        <td class="txtcenter">
                            <img src="<{$users.user_avatar}>" class="user_avatar" alt="<{$users.uname}>" title="<{$users.uname}>"/>
                        </td>
                        <td class="txtcenter">
                            <a href='mailto:<{$users.email}>'><img src="<{xoAdminIcons 'mail_send.png'}>" alt="" title=<{$smarty.const._AM_SYSTEM_USERS_EMAIL}>
                                /></a>
                            <a href='javascript:openWithSelfMain("<{$xoops_url}>/pmlite.php?send2=1&amp;to_userid=<{$users.uid}>","pmlite",565,500);'><img
                                        src="<{xoAdminIcons 'pm.png'}>" alt="" title="<{$smarty.const._AM_SYSTEM_USERS_PM}>"></a>
                            <{if $users.url|default:'' != ''}><a href='<{$users.url}>' rel='external'><img src="<{xoAdminIcons 'url.png'}>" alt="" title="<{$smarty.const._AM_SYSTEM_USERS_URL}>"></a><{/if}>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <ul style="border: 1px solid #666; padding: 8px;">
                                <{if $users.user_name|default:false}>
                                    <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_NAME}></span>&nbsp;:&nbsp;<{$users.name}></li>
                                <{/if}>
                                <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_UNAME}></span>&nbsp;:&nbsp;<{$users.uname}></li>
                                <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_EMAIL}></span>&nbsp;:&nbsp;<{$users.email}></li>
                                <{if $users.user_url|default:false}>
                                    <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_URL}></span>&nbsp;:&nbsp;<{$users.url}></li>
                                <{/if}>
                                <{if $users.user_icq}>
                                    <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_ICQ}></span>&nbsp;:&nbsp;<{$users.user_icq}></li>
                                <{/if}>
                                <{if $users.user_aim}>
                                    <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_AIM}></span>&nbsp;:&nbsp;<{$users.user_aim}></li>
                                <{/if}>
                                <{if $users.user_yim}>
                                    <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_YIM}></span>&nbsp;:&nbsp;<{$users.user_yim}></li>
                                <{/if}>
                                <{if $users.user_msnm}>
                                    <li><span class="bold"><{$smarty.const._AM_SYSTEM_USERS_MSNM}></span>&nbsp;:&nbsp;<{$users.user_msnm}></li>
                                <{/if}>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        <{/foreach}>
    <{/if}>
    <!--Pop-pup-->
    <div class='txtright'><{$nav|default:''}></div>
<{/if}>
<br>
<!-- Display Avatar form (add,edit) -->
<{if $form|default:false}>
    <div class="spacer"><{$form}></div>
<{/if}>
