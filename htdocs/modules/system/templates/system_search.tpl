<{if $results}>	
	<h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
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
	<{foreach item=search from=$search}>
		<h4><{$search.module_name}></h4>
		<{foreach item=data from=$search.module_data}>
			<img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a>
			<br>
			<{if $data.uname}>
				<span class='x-small'>
					<a href="<{$data.uname_link}>"><{$data.uname}></a>
					<{if $data.time}>
						(<{$data.time}>)
					<{/if}>				
				</span>
				<br>
			<{/if}>
		<{/foreach}>
		<{if $search.module_show_all}>
			<p>
				<a href="<{$search.module_show_all}>"><{$smarty.const._SR_SHOWALLR}></a>
			</p>
		<{/if}>
	<{/foreach}>
<{/if}>
<{if $showallbyuser}>	
	<h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
	<{if $showall}>
		<{$smarty.const._SR_KEYWORDS}>: <strong><{$keywords}></strong>
		<br>
	<{/if}>
	<{$showing}>
	<h4><{$module_name}></h4>
	<{foreach item=data from=$results_arr}>
		<img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a>
		<br>
		<{if $data.uname}>
			<span class='x-small'>
				<a href="<{$data.uname_link}>"><{$data.uname}></a>
				<{if $data.time}>
					(<{$data.time}>)
				<{/if}>				
			</span>
			<br>
		<{/if}>
	<{/foreach}>
	<{if $nomatch}>
		<p>
			<{$smarty.const._SR_NOMATCH}>
		</p>
	<{/if}>
	<{if $previous || next}>
		<br>
		<table>
			<tr>
			<{if $previous}>
				<td align="left">
					<a href="<{$previous}>"><{$smarty.const._SR_PREVIOUS}></a>
				</td>
			<{/if}>
			<{if $next}>
				<td align="right">
					<a href="<{$next}>"><{$smarty.const._SR_NEXT}></a>
				</td>
			<{/if}>
			</tr>
		</table>
	<{/if}>
<{/if}>
<{if $form}>
	<br>
	<{$form}>
<{/if}>
