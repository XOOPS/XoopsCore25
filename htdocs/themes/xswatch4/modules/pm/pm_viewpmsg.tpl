<h4><{$smarty.const._PM_PRIVATEMESSAGE}></h4>
<{if isset($op)}>
	<{if isset($msg)}>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong><{$msg}></strong>
		</div>
	<{/if}>

	<{if isset($errormsg)}>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong><{$errormsg}></strong>
		</div>
	<{/if}>

	<{if isset($pagenav)}>
	<div class="generic-pagination col text-right mb-2">
		<{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
	</div>
	<{/if}>

	<form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}>>
		<hr />
		<{$pmform.elements.send.body}>
		<hr />

		<div class="row mb-3">
			<div class="col-12 btn-group" role="group" aria-label="Basic example">
				<{if $op == "in" || (!($op == "out") && !($op == "save"))}>
					<a class="btn btn-primary" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><span class="fa fa-inbox fa-2x fa-fw"></span><br /><{$smarty.const._PM_INBOX}></a>
					<a class="btn btn-secondary" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><span class="fa fa-paper-plane fa-2x fa-fw"></span><br /><{$smarty.const._PM_OUTBOX}></a>
					<a class="btn btn-secondary" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><span class="fa fa-archive fa-2x fa-fw"></span><br /><{$smarty.const._PM_SAVEBOX}></a>
				<{elseif $op == "out"}>
					<a class="btn btn-secondary" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><span class="fa fa-inbox fa-2x fa-fw"></span><br /><{$smarty.const._PM_INBOX}></a>
					<a class="btn btn-primary" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><span class="fa fa-paper-plane fa-2x fa-fw"></span><br /><{$smarty.const._PM_OUTBOX}></a>
					<a class="btn btn-secondary" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><span class="fa fa-archive fa-2x fa-fw"></span><br /><{$smarty.const._PM_SAVEBOX}></a>
				<{elseif $op == "save"}>
					<a class="btn btn-secondary" href="viewpmsg.php?op=in" title="<{$smarty.const._PM_INBOX}>"><span class="fa fa-inbox fa-2x fa-fw"></span><br /><{$smarty.const._PM_INBOX}></a>
					<a class="btn btn-secondary" href="viewpmsg.php?op=out" title="<{$smarty.const._PM_OUTBOX}>"><span class="fa fa-paper-plane fa-2x fa-fw"></span><br /><{$smarty.const._PM_OUTBOX}></a>
					<a class="btn btn-primary" href="viewpmsg.php?op=save" title="<{$smarty.const._PM_SAVEBOX}>"><span class="fa fa-archive fa-2x fa-fw"></span><br /><{$smarty.const._PM_SAVEBOX}></a>
				<{/if}>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-hover" cellspacing='1' cellpadding='4'>
					<!-- Table - Head -->
					<tr class="table-secondary">
						<th class="text-center"><input name='allbox' id='allbox' onclick='xoopsCheckAll("<{$pmform.name}>", "allbox");' type='checkbox' value='Check All' title="<{$smarty.const.THEME_SELECT_ALL}>"/></th>
						<th class="text-center d-none d-sm-table-cell"><span class="fa fa-envelope text-primary"></span> <span class="fa fa-envelope-open text-secondary"></span></th>
						<{if isset($op) && $op == "out"}>
							<th class="text-center"><{$smarty.const._PM_TO}></th>
						<{else}>
							<th class="text-center"><{$smarty.const._PM_FROM}></th>
						<{/if}>
						<th class='d-none d-sm-table-cell'><{$smarty.const._PM_SUBJECT}></th>
						<th class='d-table-cell d-sm-none'><{$smarty.const._PM_SUBJECT}> / <{$smarty.const._PM_DATE}></th>
						<th class='d-none d-sm-table-cell'><{$smarty.const._PM_DATE}></th>
					</tr>
					<!-- Table - End Head -->

					<{if isset($total_messages) && $total_messages == 0}>
						<tr>
							<td class='even text-center bg-secondary' colspan='6'><{$smarty.const._PM_YOUDONTHAVE}></td>
						</tr>
					<{/if}>

					<{foreach item=message from=$messages|default:null}>
						<tr>
							<td class='d-none d-sm-table-cell aligntop text-center'>
								<input type='checkbox' id='msg_id_<{$message.msg_id}>' name='msg_id[]' value='<{$message.msg_id}>' />
							</td>
							<td class='d-table-cell d-sm-none aligntop text-center'>
								<{if isset($message.read_msg) && $message.read_msg == 1}>
									<span class="fa fa-envelope-open fa-2x text-secondary"></span>
								<{else}>
									<span class="fa fa-envelope fa-2x text-primary"></span>
								<{/if}>
							</td>

							<td class="text-center d-none d-sm-table-cell">
								<{if isset($message.read_msg) && $message.read_msg == 1}>
									<span class="fa fa-envelope-open fa-2x text-secondary"></span>
								<{else}>
									<span class="fa fa-envelope fa-2x text-primary"></span>
								<{/if}>
							</td>

							<td class='text-center'>
								<{if isset($message.postername) && $message.postername!= ""}>
									<{assign var="tempPosteruid" value=$message.posteruid}>
									<{xoUserInfo uid=$message.posteruid}>
									<a href='<{$xoops_url}>/userinfo.php?uid=<{$message.posteruid}>' alt="<{$message.postername}>" title='<{$message.postername}>'>
										<{if isset($userInfo.user_avatar) && $userInfo.user_avatar != "blank.gif"}>
											<img src="<{$xoops_url}>/uploads/<{$userInfo.user_avatar}>" alt="<{$message.postername}>" class="img-rounded img-thumbnail" width="128">
										<{else}>
											<img src="<{$xoops_imageurl}>images/no-avatar.png" alt="<{$message.postername}>"  class="img-rounded img-thumbnail" width="128">
										<{/if}>
										<br />
										<{$message.postername}>
									</a>
								<{else}>
									<{$anonymous}>
								<{/if}>
							</td>

							<td class='d-none d-sm-table-cell'>
								<{if isset($message.msg_image)}>
									<img src='<{$xoops_url}>/images/subject/<{$message.msg_image}>' alt='' />
								<{/if}>
								<a href='readpmsg.php?msg_id=<{$message.msg_id}>&amp;start=<{$message.msg_no}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>' title=''>
									<{$message.subject}>
								</a>
							</td>
							<td class='d-table-cell d-sm-none'>
								<{if isset($message.msg_image)}>
									<img src='<{$xoops_url}>/images/subject/<{$message.msg_image}>' alt='' />
								<{/if}>
								<a href='readpmsg.php?msg_id=<{$message.msg_id}>&amp;start=<{$message.msg_no}>&amp;total_messages=<{$total_messages}>&amp;op=<{$op}>' title=''>
									<{$message.subject}>
								</a>
								<br />
								<{$message.msg_time}>
							</td>

							<td class='d-none d-sm-table-cell'>
								<{$message.msg_time}>
							</td>
						</tr>
					<{/foreach}>
				</table>
			</div>
		</div>
		<hr />
		<{if isset($display)}>
			<{$pmform.elements.move_messages.body|replace:'formButton':'btn btn-success'|replace:'" >':'" ><span class="fa fa-sign-in fa-2x"></span><br />'}>
			<{$pmform.elements.delete_messages.body|replace:'formButton':'btn btn-secondary'|replace:'" >':'" ><span class="fa fa-times fa-2x"></span><br />'}>
			<{$pmform.elements.empty_messages.body|replace:'formButton':'btn btn-secondary'|replace:'" >':'" ><span class="fa fa-trash fa-2x"></span><br />'}>
		<{/if}>
		<{foreach item=element from=$pmform.elements|default:null}>
			<{if isset($element.hidden) && $element.hidden == 1}>
				<{$element.body}>
			<{/if}>
		<{/foreach}>
		</div>
	</form>

	<{if isset($pagenav)}>
	<div class="generic-pagination col text-right mt-2">
		<{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
	</div>
	<{/if}>

<{/if}>
