<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
	<{foreach item=itm from=$xoBreadcrumbs name=bcloop}>
		<{if $itm.link}>
			<li class="breadcrumb-item"><a href="<{$itm.link}>"><{$itm.title}></a></li>
		<{else}>
			<li class="breadcrumb-item active" aria-current="page"><{$itm.title}></li>
		 <{/if}>
	 <{/foreach}>
	</ol>
</nav>

