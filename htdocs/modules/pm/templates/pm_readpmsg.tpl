<div>
    <h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
</div><br>
<{if $op==out}>
    <a href='viewpmsg.php?op=out' title='<{$smarty.const._PM_OUTBOX}>'><{$smarty.const._PM_OUTBOX}></a>
    &nbsp;
<{elseif $op == "save"}>
    <a href='viewpmsg.php?op=save' title='<{$smarty.const._PM_SAVEBOX}>'><{$smarty.const._PM_SAVEBOX}></a>
    &nbsp;
<{else}>
    <a href='viewpmsg.php?op=in' title='<{$smarty.const._PM_INBOX}>'><{$smarty.const._PM_INBOX}></a>
    &nbsp;
<{/if}>

<{if $message}>
    <span class='bold'>&raquo;</span>
    &nbsp;<{$message.subject}>
    <br>
    <form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}> >
        <table cellpadding='4' cellspacing='1' class='outer bnone width100'>
            <tr>
                <th colspan='2'><{if $op==out}><{$smarty.const._PM_TO}><{else}><{$smarty.const._PM_FROM}><{/if}></th>
            </tr>
            <tr class='even'>
                <td class='aligntop'>
                    <{if ( $poster != false ) }>
                        <a href='<{$xoops_url}>/userinfo.php?uid=<{$poster->getVar("uid")}>'><{$poster->getVar("uname")}></a>
                        <br>
                        <{if ( $poster->getVar("user_avatar") != "" ) }>
                            <img src='<{$xoops_url}>/uploads/<{$poster->getVar("user_avatar")}>' alt=''/>
                            <br>
                        <{/if}>
                        <{if ( $poster->getVar("user_from") != "" ) }>
                            <{$smarty.const._PM_FROMC}><{$poster->getVar("user_from")}>
                            <br>
                            <br>
                        <{/if}>
                        <{if ( $poster->isOnline() ) }>
                            <span class='bold red'><{$smarty.const._PM_ONLINE}></span>
                            <br>
                            <br>
                        <{/if}>
                    <{else}>
                        <{$anonymous}>
                    <{/if}>
                </td>
                <td>
                    <{if $message.msg_image != ""}>
                        <img src='<{$xoops_url}>/images/subject/<{$message.msg_image}>' alt=''/>
                    <{/if}>
                    <{$smarty.const._PM_SENTC}><{$message.msg_time}><br>
                    <hr/>
                    <strong><{$message.subject}></strong><br>
                    <br>
                    <{$message.msg_text}><br>
                    <br>
                </td>
            </tr>
            <tr class='foot'>
                <td class='width20 txtleft' colspan='2'>
                    <{foreach item=element from=$pmform.elements}>
                        <{$element.body}>
                    <{/foreach}>
                </td>
            </tr>
            <tr>
                <td class='txtright' colspan='2'>
                    <{if ( $previous >= 0 ) }>
                        <a href='readpmsg.php?start=<{$previous}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>'
                           title='<{$smarty.const._PM_PREVIOUS}>'>
                            <{$smarty.const._PM_PREVIOUS}>
                        </a>
                        &nbsp|&nbsp;
                    <{else}>
                        <{$smarty.const._PM_PREVIOUS}>&nbsp;|&nbsp;
                    <{/if}>
                    <{if ( $next < $total_messages ) }>
                        <a href='readpmsg.php?start=<{$next}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>'
                           title='<{$smarty.const._PM_NEXT}>'>
                            <{$smarty.const._PM_NEXT}>
                        </a>
                    <{else}>
                        <{$smarty.const._PM_NEXT}>
                    <{/if}>
                </td>
            </tr>
        </table>
    </form>
<{else}>
    <br>
    <br>
    <{$smarty.const._PM_YOUDONTHAVE}>
<{/if}>
