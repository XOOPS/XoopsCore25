<{if $results|default:false}>
	<h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
	<{$smarty.const._SR_KEYWORDS}>: <strong><{$keywords}></strong>
	<br>
	<{if $error_length != ''}>
		<{$error_length}> <strong><{$error_keywords}></strong>
		<br>
	<{/if}>
	<{if $nomatch|default:false}>
		<br>
		<{$nomatch}>
		<br>
	<{else}>
		<{foreach item=searchitem from=$search}>
			<h4><{$searchitem.module_name}></h4>
			<{foreach item=data from=$searchitem.module_data}>
				<img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a>
				<br>
				<{if $data.uname|default:''}>
					<span class='x-small'>
						<a href="<{$data.uname_link}>"><{$data.uname}></a>
						<{if $data.time}>
							(<{$data.time}>)
						<{/if}>
					</span>
					<br>
				<{/if}>
			<{/foreach}>
			<{if $searchitem.module_show_all|default:false}>
				<p>
					<a href="<{$searchitem.module_show_all}>"><{$smarty.const._SR_SHOWALLR}></a>
				</p>
			<{/if}>
		<{/foreach}>
	<{/if}>
<{/if}>
<{if $showallbyuser|default:false}>
	<h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
	<{if $nomatch|default:false != true}>
		<{if $showall|default:false}>
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
		<{if $previous || $next}>
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
<{/if}>
<{if $form|default:''}>
	<br>
	<{$form}>
<{/if}>
