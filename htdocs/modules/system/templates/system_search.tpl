<{if isset($results)}>
	<h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
	<{$smarty.const._SR_KEYWORDS}>: <strong><{$keywords}></strong>
	<br>
	<{if !empty($error_length)}>
		<{$error_length}> <strong><{$error_keywords}></strong>
		<br>
	<{/if}>
	<{if !empty($nomatch)}>
		<br>
		<{$nomatch}>
		<br>
	<{else}>
		<{foreach item=searchitem from=$search|default:null}>
			<h4><{$searchitem.module_name}></h4>
			<{foreach item=data from=$searchitem.module_data|default:null}>
				<img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a>
				<br>
			<{if isset($data.uname)}>
					<span class='x-small'>
						<a href="<{$data.uname_link}>"><{$data.uname}></a>
						<{if $data.time}>
							(<{$data.time}>)
						<{/if}>
					</span>
					<br>
				<{/if}>
			<{/foreach}>
			<{if !empty($searchitem.module_show_all)}>
				<p>
					<a href="<{$searchitem.module_show_all}>"><{$smarty.const._SR_SHOWALLR}></a>
				</p>
			<{/if}>
		<{/foreach}>
	<{/if}>
<{/if}>
<{if !empty($showallbyuser)}>
	<h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
	<{if isset($nomatch) && $nomatch != true}>
		<{if isset($showall)}>
			<{$smarty.const._SR_KEYWORDS}>: <strong><{$keywords}></strong>
			<br>
		<{/if}>
		<{$showing}>
		<h4><{$module_name}></h4>
		<{foreach item=data from=$results_arr|default:null}>
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
		<{if isset($nomatch)}>
			<p>
				<{$smarty.const._SR_NOMATCH}>
			</p>
		<{/if}>
		<{if $previous || $next}>
			<br>
			<table>
				<tr>
				<{if isset($previous)}>
					<td align="left">
						<a href="<{$previous}>"><{$smarty.const._SR_PREVIOUS}></a>
					</td>
				<{/if}>
				<{if isset($next)}>
					<td align="right">
						<a href="<{$next}>"><{$smarty.const._SR_NEXT}></a>
					</td>
				<{/if}>
				</tr>
			</table>
		<{/if}>
	<{/if}>
<{/if}>
<{if isset($form)}>
	<br>
	<{$form}>
<{/if}>
