<div class="breadcrumbs">
    <{foreach item=itm from=$xoBreadcrumbs|default:null name=bcloop}>
        <span class="item">
        <{if !empty($itm.link)}>
            <a href="<{$itm.link}>" title="<{$itm.title}>"><{$itm.title}></a>
        <{else}>
            <{$itm.title}>
        <{/if}>
        </span>
        <{if !$smarty.foreach.bcloop.last}>
            <span class="delimiter">&raquo;</span>
        <{/if}>
    <{/foreach}>
</div>
<br class="clear"/>
