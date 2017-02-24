<h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
<div class="message-current-tab">
    <div class="message-current-tab">
        <a class="btn btn-success btn-block" href="viewpmsg.php?op=<{$op}>">
        <{if $op == "out"}>
        <{$smarty.const._PM_OUTBOX}>
        <{elseif $op == "save"}>
        <{$smarty.const._PM_SAVEBOX}>
        <{else}>
        <{$smarty.const._PM_INBOX}>
        <{/if}>
        </a>
    </div><!-- .message-current-tab -->
</div>

<{if $message}>
<form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}>>
    <div class="row">
        <div class="col-md-4">
            <{if $op==out}><{$smarty.const._PM_TO}>: <{else}><{$smarty.const._PM_FROM}>: <{/if}>
            <{if ( $poster != false ) }>
                <a href="<{$xoops_url}>/userinfo.php?uid=<{$poster->getVar('uid')}>"><{$poster->getVar('uname')}></a><br>
                <{if ( $poster->getVar("user_avatar") != "")}>
                    <img src="<{$xoops_url}>/uploads/<{$poster->getVar('user_avatar')}>" alt="<{$poster->getVar('uname')}>" class="img-responsive img-rounded img-thumbnail">
                <{/if}>
                <{if ( $poster->getVar("user_from") != "" ) }>
                    <{$smarty.const._PM_FROMC}><{$poster->getVar("user_from")}>
                <{/if}>
                <{if ( $poster->isOnline() ) }>
                    <br><{$smarty.const._PM_ONLINE}>
                <{/if}>
            <{else}>
                <{$anonymous}>
            <{/if}>
        </div>
        <div class="col-md-8">
            <h4><{if $message.msg_image != ""}><img src='<{$xoops_url}>/images/subject/<{$message.msg_image}>' alt='' /><{/if}>
                <{$message.subject}>
            </h4>
            <div class="text-muted text-right"><small><{$smarty.const._PM_SENTC}>&nbsp;<{$message.msg_time}></small></div>
            <{$message.msg_text}>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <br>
            <{foreach item=element from=$pmform.elements}>
                <{$element.body}>
            <{/foreach}>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <ul class="pager">
                <{if ( $previous >= 0 ) }>
                    <li>
                        <a href='readpmsg.php?start=<{$previous}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>' title='<{$smarty.const._PM_PREVIOUS}>'>
                            <{$smarty.const._PM_PREVIOUS}>
                        </a>
                    </li>
                <{else}>
                    <li class="disabled">
                        <a href="#"><{$smarty.const._PM_PREVIOUS}></a>
                    </li>
                <{/if}>
                <{if ( $next < $total_messages ) }>
                    <li>
                        <a href='readpmsg.php?start=<{$next}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>' title='<{$smarty.const._PM_NEXT}>'>
                            <{$smarty.const._PM_NEXT}>
                        </a>
                    </li>
                <{else}>
                    <li class="disabled">
                        <a href="#"><{$smarty.const._PM_NEXT}></a>
                    </li>
                <{/if}>
            </ul>
        </div>
    </div>
</form>
<{else}>
    <{$smarty.const._PM_YOUDONTHAVE}>
<{/if}>
