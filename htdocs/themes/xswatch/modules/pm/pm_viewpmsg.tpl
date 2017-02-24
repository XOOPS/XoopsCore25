<h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
<{if $op}>
<div class="current-tab">
    <div class="row">
        <{if $op == "out"}>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info btn-block" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><{$smarty.const._PM_INBOX}></a>
            </div>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info btn-block" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><{$smarty.const._PM_SAVEBOX}></a>
            </div>
        <{elseif $op == "save"}>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info btn-block" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><{$smarty.const._PM_INBOX}></a>
            </div>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info btn-block" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><{$smarty.const._PM_OUTBOX}></a>
            </div>
        <{elseif $op == "in"}>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info btn-block" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><{$smarty.const._PM_OUTBOX}></a>
            </div>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info btn-block" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><{$smarty.const._PM_SAVEBOX}></a>
            </div>
        <{/if}>
    </div>
</div><!-- .current-tab -->

<div class="message-current-tab">
    <{if $op == "out"}>
    <div class="alert alert-success" role="alert"><{$smarty.const._PM_OUTBOX}></div>
    <{elseif $op == "save"}>
    <div class="alert alert-success" role="alert"><{$smarty.const._PM_SAVEBOX}></div>
    <{else}>
    <div class="alert alert-success" role="alert"><{$smarty.const._PM_INBOX}></div>
    <{/if}>
</div><!-- .message-current-tab -->

<{if $msg}>
    <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><{$msg}></strong>
    </div>
<{/if}>

<{if $errormsg}>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><{$errormsg}></strong>
    </div>
<{/if}>

<{if $pagenav}>
    <{$pagenav}>
<{/if}>

<form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}>>
<table class="table table-striped table-condensed" cellspacing='1' cellpadding='4'>

    <tr class="txtcenter alignmiddle">
        <th class="txtcenter"><input name='allbox' id='allbox' onclick='xoopsCheckAll("<{$pmform.name}>", "allbox");' type='checkbox' value='Check All' title="<{$smarty.const.THEME_SELECT_ALL}>"/></th>
        <th><span class="glyphicon glyphicon-download-alt"></span></th>
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
        <tr<{if $message.read_msg != 1}> class="info"<{/if}>>
            <td class='aligntop txtcenter'>
                <input type='checkbox' id='msg_id_<{$message.msg_id}>' name='msg_id[]' value='<{$message.msg_id}>' />
            </td>
            <td class='aligntop'>
            <{if $message.read_msg == 1}>
                <img src='<{xoModuleIcons16 mail_read.png}>' alt='{translate key="READ"}' title='{translate key="READ"}'/>
            <{else}>
                <img src='<{xoModuleIcons16 mail_notread.png}>' alt='{translate key="NOT_READ"}' title='{translate key="NOT_READ"}'/>
            <{/if}>
            <{if $message.msg_image|default:false}>
                <img src='<{$xoops_url}>/images/subject/<{$message.msg_image}>' alt='' />
            <{/if}>
            </td>
            <td class='alignmiddle'>
                <{if $message.postername != ""}>
                    <a href='<{$xoops_url}>/userinfo.php?uid=<{$message.posteruid}>' title=''><{$message.postername}></a>
                <{else}>
                    <{$anonymous}>
                <{/if}>
            </td>
            <td class='alignmiddle'>
                <a href='readpmsg.php?msg_id=<{$message.msg_id}>&amp;start=<{$message.msg_no}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>' title=''>
                    <{$message.subject}>
                </a>
            </td>
            <td class='alignmiddle txtcenter'>
                <{$message.msg_time}>
            </td>
        </tr>
    <{/foreach}>
    <tr class='bg2 txtleft'>
        <td class='txtleft' colspan='6'>
            <{$pmform.elements.send.body|replace:'formButton':'btn btn-default'}>
            <{if $display}>
            <{$pmform.elements.move_messages.body|replace:'formButton':'btn btn-default'}>
            <{$pmform.elements.delete_messages.body|replace:'formButton':'btn btn-default'}>
            <{$pmform.elements.empty_messages.body|replace:'formButton':'btn btn-default'}>
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
    <{$pagenav}>
<{/if}>
<{/if}>
