<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
	<{foreach item=itm from=$xoBreadcrumbs|default:null name=bcloop}>
        <{if !empty($itm.title)}>
        <{if !empty($itm.link)}>
		<li class="breadcrumb-item">
            <a href="<{$itm.link}>" title="<{$itm.title}>"><{$itm.title}></a>
        </li>
        <{else}>
        <li class="breadcrumb-item active">
            <{$itm.title}>
        </li>
        <{/if}>
        <{/if}>
	<{/foreach}>
    </ol>
</nav>
