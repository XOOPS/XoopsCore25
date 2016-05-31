<h4 class="txtcenter"><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
<{if $op}>
    <br>
    <div class="floatright txtright" style="width: 18%;">
        <{if $op == "out"}>
            <a href='viewpmsg.php?op=in' title='<{$smarty.const._PM_INBOX}>'><{$smarty.const._PM_INBOX}></a>
            |
            <a href='viewpmsg.php?op=save' title='<{$smarty.const._PM_SAVEBOX}>'><{$smarty.const._PM_SAVEBOX}></a>
        <{elseif $op == "save"}>
            <a href='viewpmsg.php?op=in' title='<{$smarty.const._PM_INBOX}>'><{$smarty.const._PM_INBOX}></a>
            |
            <a href='viewpmsg.php?op=out' title='<{$smarty.const._PM_OUTBOX}>'><{$smarty.const._PM_OUTBOX}></a>
        <{elseif $op == "in"}>
            <a href='viewpmsg.php?op=out' title='<{$smarty.const._PM_OUTBOX}>'><{$smarty.const._PM_OUTBOX}></a>
            |
            <a href='viewpmsg.php?op=save' title='<{$smarty.const._PM_SAVEBOX}>'><{$smarty.const._PM_SAVEBOX}></a>
        <{/if}>
    </div>
    <div class="floatleft width80">
        <{if $op == "out"}><{$smarty.const._PM_OUTBOX}>
        <{elseif $op == "save"}><{$smarty.const._PM_SAVEBOX}>
        <{else}><{$smarty.const._PM_INBOX}><{/if}>
    </div>
    <br>
    <br>
    <{if $msg}>
        <div class="confirmMsg"><{$msg}></div>
    <{/if}>
    <{if $errormsg}>
        <div class="errorMsg"><{$errormsg}></div>
    <{/if}>

    <{if $pagenav}>
        <div class="floatright txtright pad5">
            <{$pagenav}>
        </div>
        <br class="clear"/>
    <{/if}>
    <form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}> >
        <table cellspacing='1' cellpadding='4' class='outer bnone width100'>

            <tr class='txtcenter alignmiddle'>
                <th><input name='allbox' id='allbox' onclick='xoopsCheckAll("<{$pmform.name}>", "allbox");' type='checkbox' value='Check All'/></th>
                <th><img class='bnone' src='<{xoAppUrl images/download.gif}>' alt=''/></th>
                <th>&nbsp;</th>
                <{if $op == "out"}>
                    <th><{$smarty.const._PM_TO}></th>
                <{else}>
                    <th><{$smarty.const._PM_FROM}></th>
                <{/if}>
                <th><{$smarty.const._PM_SUBJECT}></th>
                <th class='txtcenter'><{$smarty.const._PM_DATE}></th>
            </tr>

            <{if $total_messages == 0}>
                <tr>
                    <td class='even txtcenter' colspan='6'><{$smarty.const._PM_YOUDONTHAVE}></td>
                </tr>
            <{/if}>
            <{foreach item=message from=$messages}>
                <tr class='<{cycle values="odd, even"}> txtleft'>
                    <td class='aligntop txtcenter width2'>
                        <input type='checkbox' id='msg_id_<{$message.msg_id}>' name='msg_id[]' value='<{$message.msg_id}>'/>
                    </td>
                    <{if $message.read_msg == 1}>
                        <td class='aligntop width5 txtcenter'><img src='<{xoModuleIcons16 mail_read.png}>' alt='<{$smarty.const._PM_READ}>'title='<{$smarty.const._PM_READ}>'/></td>
                    <{else}>
                        <td class='aligntop width5 txtcenter'><img src='<{xoModuleIcons16 mail_notread.png}>' alt='<{$smarty.const._PM_NOTREAD}>'title='<{$smarty.const._PM_NOTREAD}>'/></td>
                    <{/if}>
                    <td class='aligntop width5 txtcenter'>
                        <{if $message.msg_image != ""}>
                            <img src='<{$xoops_url}>/images/subject/<{$message.msg_image}>' alt=''/>
                        <{/if}>
                    </td>
                    <td class='alignmiddle width10'>
                        <{if $message.postername != ""}>
                            <a href='<{$xoops_url}>/userinfo.php?uid=<{$message.posteruid}>' title=''><{$message.postername}></a>
                        <{else}>
                            <{$anonymous}>
                        <{/if}>
                    </td>
                    <td class='alignmiddle'>
                        <a href='readpmsg.php?msg_id=<{$message.msg_id}>&amp;start=<{$message.msg_no}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>'title=''>
                            <{$message.subject}>
                        </a>
                    </td>
                    <td class='alignmiddle txtcenter width20'>
                        <{$message.msg_time}>
                    </td>
                </tr>
            <{/foreach}>
            <tr class='bg2 txtleft'>
                <td class='txtleft' colspan='6'>
                    <{$pmform.elements.send.body}>
                    <{if $display}>
                        &nbsp;<{$pmform.elements.move_messages.body}>
                        &nbsp;<{$pmform.elements.delete_messages.body}>
                        &nbsp;<{$pmform.elements.empty_messages.body}>
                    <{/if}>
                    <{foreach item=element from=$pmform.elements}>
                        <{if $element.hidden == 1}>
                            <{$element.body}>
                        <{/if}>
                    <{/foreach}>
                </td>
            </tr>
        </table>
    </form>
    <{if $pagenav}>
        <div class="floatright txtright pad5">
            <{$pagenav}>
        </div>
    <{/if}>
<{/if}>
