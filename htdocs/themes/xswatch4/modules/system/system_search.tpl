<{if $results}>	
	<div class="alert alert-primary">
	<{$smarty.const._SR_KEYWORDS}>: <strong><{$keywords}></strong>
	<br>
	
	<{if $error_length != ''}>
		<{$error_length}> <strong><{$error_keywords}></strong>
		<br>
	<{/if}>
	<{if $nomatch}>	
		<br>
		<{$nomatch}>
		<br>
	<{/if}>
	</div>
	<{foreach item=search from=$search}>
		<br /><h3><button type="button" class="btn btn-danger btn-sm"><{$search.module_name}></button></h3>
		<{foreach item=data from=$search.module_data}>
			<div style="padding:8px;display:block;border-bottom:1px dashed #eeeeee;">
			<{if $data.image_link}>	
				<img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/>
			<{/if}>
				<a href="<{$data.link}>"><{$data.link_title}></a>
				<br>
				<span class='text-muted x-small'>
					<{if $data.uname}>
					&nbsp;<i class="fa fa-user-circle"></i> <a href="<{$data.uname_link}>"><{$data.uname}></a>
					<{/if}>	
					<{if $data.time}>
					&nbsp;<i class="fa fa-calendar"></i> <{$data.time|date_format:"%B %e %Y %l:%M %p"}>
					<{/if}>				
				</span>
				<br>
			</div>
		<{/foreach}>
		<{if $search.module_show_all}>
			<p>
				<a href="<{$search.module_show_all}>" class="btn btn-success btn-sm"><i class="fa fa-search"></i> <{$smarty.const._SR_SHOWALLR}> :: <{$search.module_name}> </a>
			</p>
		<{/if}>
	<{/foreach}>
<{/if}>
<{if $showallbyuser}>	
	<div class="alert alert-info">
	<{if $showall}>
		<{$smarty.const._SR_KEYWORDS}>: <strong><{$keywords}></strong>
		<br>
	<{/if}>
	</div>
	<{$showing}>
	<h3><button type="button" class="btn btn-danger"><{$module_name}></button></h3>
	<{foreach item=data from=$results_arr}>
	<div style="padding:8px;display:block;border-bottom:1px dashed #eeeeee;">
		<{if $data.image_link}>	
				<img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/>
		<{/if}>
		<a href="<{$data.link}>"><{$data.link_title}></a>
				<br>
				<span class='text-muted x-small'>
					<{if $data.uname}>
					&nbsp;<i class="fa fa-user-circle"></i> <a href="<{$data.uname_link}>"><{$data.uname}></a>
					<{/if}>	
					<{if $data.time}>
					&nbsp;<i class="fa fa-calendar"></i> <{$data.time|date_format:"%B %e %Y %l:%M %p"}>
					<{/if}>				
				</span>
				<br>
		</div>
	<{/foreach}>
	<{if $nomatch}>
			<div class="alert alert-info">
			<{$smarty.const._SR_NOMATCH}>
			</div>
	<{/if}>
	<{if $previous || next}>
		<br>
		<table>
			<tr>
			<{if $previous}>
				<td align="left">
					 <a href="<{$previous}>" class="btn btn-success"><i class="fa fa-arrow-left" aria-hidden="true"></i> <{$smarty.const._SR_PREVIOUS}> </a>
				</td>
			<{/if}>
			<{if $next}>
				<td align="right">
					<a href="<{$next}>" class="btn btn-success"><{$smarty.const._SR_NEXT}> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
				</td>
			<{/if}>
			</tr>
		</table><br /><br />
	<{/if}>
<{/if}>
<{if $form}>
	<br>
	<{$form}>
<{/if}>
