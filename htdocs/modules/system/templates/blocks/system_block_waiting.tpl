<ul>
    <{foreach item=module from=$block.modules|default:null}>
        <li><a href="<{$module.adminlink}>" title="<{$module.lang_linkname}>"><{$module.lang_linkname}></a>: <{$module.pendingnum}></li>
    <{/foreach}>
</ul>
