<h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
<hr />
<div class="message-current-tab mb-3">
    <div class="">
        <a class="btn btn-secondary" href="viewpmsg.php?op=<{$op}>">
			<span class="fa fa-arrow-left fa-lg fa-fw"></span>
            <{if isset($op)}>
                <{if $op == "out"}>
                    <span class="fa fa-paper-plane fa-lg fa-fw"></span>
                    <{$smarty.const._PM_OUTBOX}>
                <{elseif $op == "save"}>
                    <span class="fa fa-archive fa-lg fa-fw"></span>
                    <{$smarty.const._PM_SAVEBOX}>
                <{/if}>
            <{else}>
                <span class="fa fa-inbox fa-lg fa-fw"></span>
                <{$smarty.const._PM_INBOX}>
            <{/if}>
        </a>
    </div><!-- .message-current-tab -->
</div>
<hr />
<div class="row mb-3">
	<div class="col-12 btn-group" role="group" aria-label="Basic example">
        <{if isset($op)}>
            <{if $op == "in" || (!($op == "out") && !($op == "save"))}>
                <a class="btn btn-primary" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><span class="fa fa-inbox fa-2x fa-fw"></span><br/><{$smarty.const._PM_INBOX}></a>
                <a class="btn btn-secondary" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><span class="fa fa-paper-plane fa-2x fa-fw"></span><br/><{$smarty.const._PM_OUTBOX}></a>
                <a class="btn btn-secondary" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><span class="fa fa-archive fa-2x fa-fw"></span><br/><{$smarty.const._PM_SAVEBOX}></a>
            <{elseif $op == "out"}>
                <a class="btn btn-secondary" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><span class="fa fa-inbox fa-lg fa-fw"></span><br/><{$smarty.const._PM_INBOX}></a>
                <a class="btn btn-primary" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><span class="fa fa-paper-plane fa-lg fa-fw"></span><br/><{$smarty.const._PM_OUTBOX}></a>
                <a class="btn btn-secondary" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><span class="fa fa-archive fa-lg fa-fw"></span><br/><{$smarty.const._PM_SAVEBOX}></a>
            <{elseif $op == "save"}>
                <a class="btn btn-secondary" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><span class="fa fa-inbox fa-lg fa-fw"></span><br/><{$smarty.const._PM_INBOX}></a>
                <a class="btn btn-secondary" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><span class="fa fa-paper-plane fa-lg fa-fw"></span><br/><{$smarty.const._PM_OUTBOX}></a>
                <a class="btn btn-primary" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><span class="fa fa-archive fa-lg fa-fw"></span><br/><{$smarty.const._PM_SAVEBOX}></a>
            <{/if}>
        <{/if}>
	</div>
</div>
<{if isset($message)}>
	<form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}>>
		<div class="container-fluid">
			<div class="row border p-2">
				<div class="col-md-4 text-center">
					<{if isset($op) && $op == 'out'}><b><{$smarty.const._PM_TO}></b><br> <{else}><b><{$smarty.const._PM_FROM}></b><br> <{/if}>
					<{if $poster != false }>
						<a href="<{$xoops_url}>/userinfo.php?uid=<{$poster->getVar('uid')}>">
							<h5><{$poster->getVar('uname')}></h5>
							<{if ($poster->getVar("user_avatar") != "blank.gif")}>
								<img src="<{$xoops_url}>/uploads/<{$poster->getVar('user_avatar')}>" alt="<{$poster->getVar('uname')}>" class="img-fluid img-rounded img-thumbnail" width="128">
							<{else}>
								<img src="<{$xoops_imageurl}>images/no-avatar.png" alt="<{$poster->getVar('uname')}>" class="img-fluid img-rounded img-thumbnail" width="128">
							<{/if}>
						</a>
						<{if ( $poster->getVar("user_from") != "" ) }>
							<{$smarty.const._PM_FROMC}><{$poster->getVar("user_from")}>
						<{/if}>
					   <{if ( $poster->isOnline() ) }>
							<br><br><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-user-circle-o"></i> <{$smarty.const._PM_ONLINE}></button><br>
					   <{/if}>
					<{else}>
						<{$anonymous}>
					<{/if}>
				</div>
				<div class="col-md-8">
					<h5><{if !empty($message.msg_image)}><img src='<{$xoops_url}>/images/subject/<{$message.msg_image}>' alt='' /><{/if}>
						<{$message.subject}>
					</h5>
					<div class="text-muted text-left"><small><i class="fa fa-calendar-o"></i>&nbsp;<{$smarty.const._PM_SENTC}>&nbsp;<{$message.msg_time}></small></div>
					<hr />
					<{$message.msg_text}><br><br>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<br>
				<{foreach item=element from=$pmform.elements|default:null}>
					<{$element.body}>
				<{/foreach}>
			</div>
		</div>
		<div class="mt-3">
			<div>
				<ul class="pagination justify-content-end"">
					<{if ( $previous >= 0 ) }>
						<li class="page-item">
							<a class="page-link" href='readpmsg.php?start=<{$previous}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>' title='<{$smarty.const._PM_PREVIOUS}>'>
								<span class="fa fa-arrow-left"></span> <{$smarty.const._PM_PREVIOUS}>
							</a>
						</li>
					<{else}>
						<li class="page-item disabled">
							<a class="page-link" href="#"><span class="fa fa-arrow-left"></span> <{$smarty.const._PM_PREVIOUS}></a>
						</li>
					<{/if}>
					<{if ( $next < $total_messages ) }>
						<li class="page-item">
							<a class="page-link" href='readpmsg.php?start=<{$next}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>' title='<{$smarty.const._PM_NEXT}>'>
								<{$smarty.const._PM_NEXT}> <span class="fa fa-arrow-right"></span>
							</a>
						</li>
					<{else}>
						<li class="page-item disabled">
							<a class="page-link" href="#"><{$smarty.const._PM_NEXT}> <span class="fa fa-arrow-right"></a>
						</li>
					<{/if}>
				</ul>
			</div>
		</div>
	</form>
<{else}>
    <{$smarty.const._PM_YOUDONTHAVE}>
<{/if}>
