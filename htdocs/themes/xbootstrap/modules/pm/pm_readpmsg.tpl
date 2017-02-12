<h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
<div class="message-current-tab">
    <{if $op==out}>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong><a href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><{$smarty.const._PM_OUTBOX}></a></strong>
        </div>
    <{elseif $op == "save"}>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong><a href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><{$smarty.const._PM_SAVEBOX}></a></strong>
        </div>
    <{else}>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <a href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><{$smarty.const._PM_INBOX}></a>
        </div>
    <{/if}>
</div>

<{if $message}>
<blockquote>
    <p><{$message.subject}></p>
</blockquote>

<div class="row message-body">
    <form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>"
            <{$pmform.extra}>>
        <div class="col-xs-4 col-md-4 sender-info">
            <{if $op==out}><strong><{$smarty.const._PM_TO}>: </strong><{else}><strong><{$smarty.const._PM_FROM}>: </strong><{/if}>
            <{if ( $poster != false ) }>
                <a href="<{$xoops_url}>/userinfo.php?uid=<{$poster->getVar('uid')}>"><{$poster->getVar('uname')}></a>
                <{if ( $poster->getVar("user_avatar") != "")}>
                    <img src="<{$xoops_url}>/uploads/<{$poster->getVar('user_avatar')}>" alt="<{$poster->getVar('uname')}>"
                         class="img-responsive img-rounded img-thumbnail">
                <{/if}>
                <{if ( $poster->getVar("user_from") != "" ) }>
                    <{$smarty.const._PM_FROMC}><{$poster->getVar("user_from")}>
                <{/if}>
                <{if ( $poster->isOnline() ) }>
                    <{$smarty.const._PM_ONLINE}>
                <{/if}>
            <{else}>
                <{$anonymous}>
            <{/if}>
        </div><!-- .sender-info -->

        <div class="col-xs-8 col-md-8 message-read">
            <p class="label label-info"><strong><{$smarty.const._PM_SENTC}> </strong><{$message.msg_time}></p>

            <p><{$message.msg_text}></p>

            </div><!-- .message-read -->
        </div>
    <div class="row message-body">
            <div class="col-xs-4 col-md-4">
            </div>
            <div class="col-xs-8 col-md-8">
                <{foreach item=element from=$pmform.elements}>
                <{$element.body}>
                <{/foreach}>
                <br>
                <div class="alignright">
                    <{if ($previous >= 0 )}>
                    <a class="btn btn-primary btn-sm" href="readpmsg.php?start=<{$previous}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>"
                       title="<{$smarty.const._PM_PREVIOUS}>">
                        <span class="glyphicon glyphicon-circle-arrow-left"></span>
                    </a>
                    <{else}>
                    <button class="btn btn-primary btn-sm" disabled="disabled">
                        <span class="glyphicon glyphicon-circle-arrow-left"></span>
                    </button>
                    <{/if}>
                    <{if ( $next < $total_messages ) }>
                    <a class="btn btn-primary btn-sm" href="readpmsg.php?start=<{$next}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>"
                       title="<{$smarty.const._PM_NEXT}>">
                        <span class="glyphicon glyphicon-circle-arrow-right"></span>
                    </a>
                    <{else}>
                    <button class="btn btn-primary btn-sm" disabled="disabled">
                        <span class="glyphicon glyphicon-circle-arrow-right"></span>
                    </button>
                    <{/if}>
            </div>
        </div>
    </form>
    <{else}>
    <{$smarty.const._PM_YOUDONTHAVE}>
    <{/if}>
</div>
</div><!-- .message-body -->
