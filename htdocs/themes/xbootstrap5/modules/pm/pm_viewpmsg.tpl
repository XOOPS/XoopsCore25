<h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
<div class="current-tab">
    <div class="row">
        <{if $op == "out"}>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info w-100" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><{$smarty.const._PM_INBOX}></a>
            </div>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info w-100" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><{$smarty.const._PM_SAVEBOX}></a>
            </div>
        <{elseif $op == "save"}>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info w-100" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><{$smarty.const._PM_INBOX}></a>
            </div>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info w-100" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><{$smarty.const._PM_OUTBOX}></a>
            </div>
        <{elseif $op == "in"}>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info w-100" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><{$smarty.const._PM_OUTBOX}></a>
            </div>
            <div class="col-xs-6 col-md-6">
                <a class="btn btn-info w-100" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><{$smarty.const._PM_SAVEBOX}></a>
            </div>
        <{/if}>
    </div>
</div><!-- .current-tab -->

<div class="message-current-tab">
    <{if $op == "out"}>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
            <strong><{$smarty.const._PM_OUTBOX}></strong>
        </div>
    <{elseif $op == "save"}>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
            <strong><{$smarty.const._PM_SAVEBOX}></strong>
        </div>
    <{else}>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
            <strong><{$smarty.const._PM_INBOX}></strong>
        </div>
    <{/if}>
</div><!-- .message-current-tab -->

<{if $msg|default:''}>
    <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
        <strong><{$msg}></strong>
    </div>
<{/if}>

<{if $errormsg|default:''}>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
        <strong><{$errormsg}></strong>
    </div>
<{/if}>

<{if $pagenav|default:false}>
    <{$pagenav}>
<{/if}>

<form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}>="">
<div class="row xoops-message-list">
<div class="xoops-message-header">
<div class="col-xs-3 col-md-2">
<input name="allbox" id="allbox" onclick="xoopsCheckAll(&quot;<{$pmform.name}>&quot;, &quot;allbox&quot;);" type="checkbox" value="Check All">
&nbsp;
<span class="fa fa-circle-arrow-down btn btn-xs btn-primary"></span>
</div>

<{if $op == "out"}>
<div class="col-xs-2 col-md-2"><strong><{$smarty.const._PM_TO}></strong></div>
<{else}>
<div class="col-xs-2 col-md-2"><strong><{$smarty.const._PM_FROM}></strong></div>
<{/if}>

<div class="col-xs-4 col-md-5"><strong><{$smarty.const._PM_SUBJECT}></strong></div>

<div class="col-xs-3 col-md-3"><strong><{$smarty.const._PM_DATE}></strong></div>

</div><!-- .xoops-message-header -->

<{if $total_messages == 0}>
    <div class="col-md-12">
        <div class="alert alert-warning">
            <{$smarty.const._PM_YOUDONTHAVE}>
        </div>
    </div>
<{/if}>
</div><!-- .xoops-message-list -->

<{foreach item=message from=$messages|default:null}>
    <div class="row xoops-message-list xoops-message-loop">
        <div class="col-xs-3 col-md-2">
            <input type="checkbox" id="msg_id_<{$message.msg_id}>" name="msg_id[]" value="<{$message.msg_id}>">
            &nbsp;
            <{if $message.read_msg == 1}>
                <span class="fa fa-check-sign btn btn-xs btn-success"></span>
            <{else}>
                <span class="fa fa-envelope btn btn-xs btn-warning" title="<{$smarty.const._PM_NOTREAD}>"></span>
            <{/if}>
            <{if $message.msg_image|default:'' != ''}>
                <img src="<{$xoops_url}>/images/subject/<{$message.msg_image}>" alt="">
            <{/if}>
        </div>
        <div class="col-xs-2 col-md-2">
            <{if $message.postername|default:'' != ''}>
                <a href="<{$xoops_url}>/userinfo.php?uid=<{$message.posteruid}>" title=""><{$message.postername}></a>
            <{else}>
                <{$anonymous}>
            <{/if}>
        </div>

        <div class="col-xs-4 col-md-5">
            <a href="readpmsg.php?msg_id=<{$message.msg_id}>&start=<{$message.msg_no}>&total_messages=<{$total_messages}>&op=<{$op}>" title="">
                <{$message.subject}>
            </a>
        </div>

        <div class="col-xs-3 col-md-3">
            <{$message.msg_time}>
        </div>
    </div>
    <!-- .xoops-message-list -->
<{/foreach}>

<{$pmform.elements.send.body}>
<{if isset($display)}>
    <{$pmform.elements.move_messages.body}>
    <{$pmform.elements.delete_messages.body}>
    <{$pmform.elements.empty_messages.body}>
<{/if}>

<{foreach item=element from=$pmform.elements|default:null}>
    <{if $element.hidden == 1}>
        <{$element.body}>
    <{/if}>
<{/foreach}>
</form>

<{if $pagenav|default:false}>
    <{$pagenav}>
<{/if}>
