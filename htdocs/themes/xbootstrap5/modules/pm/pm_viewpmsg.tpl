<h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>

<{if $msg|default:''}>
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong><{$msg}></strong>
    </div>
<{/if}>

<{if $errormsg|default:''}>
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong><{$errormsg}></strong>
    </div>
<{/if}>

<{if $pmform|default:false}>

<{if $pagenav|default:false}>
    <div class="mb-2 text-end"><{$pagenav}></div>
<{/if}>

<form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}>>

    <div class="mb-3">
        <{$pmform.elements.send.body}>
    </div>

    <div class="row mb-3">
        <div class="col-12 btn-group" role="group">
            <{if $op == "in" || (!($op == "out") && !($op == "save"))}>
                <a class="btn btn-primary" href="viewpmsg.php?op=in"><span class="fa-solid fa-inbox fa-fw"></span> <{$smarty.const._PM_INBOX}></a>
                <a class="btn btn-outline-secondary" href="viewpmsg.php?op=out"><span class="fa-solid fa-paper-plane fa-fw"></span> <{$smarty.const._PM_OUTBOX}></a>
                <a class="btn btn-outline-secondary" href="viewpmsg.php?op=save"><span class="fa-solid fa-box-archive fa-fw"></span> <{$smarty.const._PM_SAVEBOX}></a>
            <{elseif $op == "out"}>
                <a class="btn btn-outline-secondary" href="viewpmsg.php?op=in"><span class="fa-solid fa-inbox fa-fw"></span> <{$smarty.const._PM_INBOX}></a>
                <a class="btn btn-primary" href="viewpmsg.php?op=out"><span class="fa-solid fa-paper-plane fa-fw"></span> <{$smarty.const._PM_OUTBOX}></a>
                <a class="btn btn-outline-secondary" href="viewpmsg.php?op=save"><span class="fa-solid fa-box-archive fa-fw"></span> <{$smarty.const._PM_SAVEBOX}></a>
            <{elseif $op == "save"}>
                <a class="btn btn-outline-secondary" href="viewpmsg.php?op=in"><span class="fa-solid fa-inbox fa-fw"></span> <{$smarty.const._PM_INBOX}></a>
                <a class="btn btn-outline-secondary" href="viewpmsg.php?op=out"><span class="fa-solid fa-paper-plane fa-fw"></span> <{$smarty.const._PM_OUTBOX}></a>
                <a class="btn btn-primary" href="viewpmsg.php?op=save"><span class="fa-solid fa-box-archive fa-fw"></span> <{$smarty.const._PM_SAVEBOX}></a>
            <{/if}>
        </div>
    </div>

    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 3rem;">
                    <input name="allbox" id="allbox" onclick='xoopsCheckAll("<{$pmform.name}>", "allbox");' type="checkbox" value="Check All" class="form-check-input" aria-label="Select all messages">
                </th>
                <th class="text-center d-none d-sm-table-cell" style="width: 2.5rem;">
                    <span class="fa-solid fa-envelope text-primary"></span>
                </th>
                <{if $op == "out"}>
                    <th><{$smarty.const._PM_TO}></th>
                <{else}>
                    <th><{$smarty.const._PM_FROM}></th>
                <{/if}>
                <th><{$smarty.const._PM_SUBJECT}></th>
                <th class="d-none d-md-table-cell"><{$smarty.const._PM_DATE}></th>
            </tr>
        </thead>
        <tbody>
            <{if $total_messages == 0}>
                <tr>
                    <td colspan="5" class="text-center text-muted py-3"><{$smarty.const._PM_YOUDONTHAVE}></td>
                </tr>
            <{/if}>

            <{foreach item=message from=$messages|default:null}>
                <tr>
                    <td class="text-center align-middle">
                        <input type="checkbox" id="msg_id_<{$message.msg_id}>" name="msg_id[]" value="<{$message.msg_id}>" class="form-check-input">
                    </td>
                    <td class="text-center align-middle d-none d-sm-table-cell">
                        <{if $message.read_msg == 1}>
                            <span class="fa-solid fa-envelope-open text-secondary"></span>
                        <{else}>
                            <span class="fa-solid fa-envelope text-primary"></span>
                        <{/if}>
                    </td>
                    <td class="align-middle">
                        <{if $message.postername|default:'' != ''}>
                            <a href="<{$xoops_url}>/userinfo.php?uid=<{$message.posteruid}>"><{$message.postername}></a>
                        <{else}>
                            <{$anonymous}>
                        <{/if}>
                    </td>
                    <td class="align-middle">
                        <{if $message.msg_image|default:'' != ''}>
                            <img src="<{$xoops_url}>/images/subject/<{$message.msg_image}>" alt="">
                        <{/if}>
                        <a href="readpmsg.php?msg_id=<{$message.msg_id}>&amp;start=<{$message.msg_no}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>">
                            <{$message.subject}>
                        </a>
                        <div class="d-md-none text-muted small"><{$message.msg_time}></div>
                    </td>
                    <td class="align-middle d-none d-md-table-cell">
                        <{$message.msg_time}>
                    </td>
                </tr>
            <{/foreach}>
        </tbody>
    </table>

    <{if $display|default:false}>
    <div class="d-flex gap-2 mt-2">
        <{$pmform.elements.move_messages.body|replace:'btn btn-secondary':'btn btn-outline-secondary'}>
        <{$pmform.elements.delete_messages.body|replace:'btn btn-secondary':'btn btn-outline-danger'}>
        <{$pmform.elements.empty_messages.body|replace:'btn btn-secondary':'btn btn-outline-secondary'}>
    </div>
    <{/if}>

    <{foreach item=element from=$pmform.elements|default:null}>
        <{if $element.hidden == 1}>
            <{$element.body}>
        <{/if}>
    <{/foreach}>
</form>

<{if $pagenav|default:false}>
    <div class="mt-2 text-end"><{$pagenav}></div>
<{/if}>

<{/if}><{* /$pmform *}>
