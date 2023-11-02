<{include file="db:system_header.tpl"}>
<script type="text/javascript">
    IMG_ON = "<{xoAdminIcons 'success.png'}>";
    IMG_OFF = "<{xoAdminIcons 'cancel.png'}>";
</script>
<table cellspacing="1" class="outer">
    <thead>
    <tr>
        <th class="txtcenter"><{$smarty.const._AM_SYSTEM_SECTION}></th>
        <th class="txtcenter"><{$smarty.const._AM_SYSTEM_DESC}></th>
        <th class="txtcenter"><{$smarty.const._AM_SYSTEM_USAGE}></th>
        <th class="txtcenter"><{$smarty.const._AM_SYSTEM_ACTIVE}></th>
    </tr>
    </thead>

    <tbody>
    <{foreach item=menuitem from=$menu|default:null}>
        <{if $menuitem.title}>
            <tr class="<{cycle values='even,odd'}>">
                <td class="bold width15">
                    <a class="tooltip" href="admin.php?fct=<{$menuitem.file}>" title="<{$smarty.const._AM_SYSTEM_GO}>: <{$menuitem.title}>">
                        <img class="xo-imgmini" src='<{$theme_icons}>/<{$menuitem.icon|default:''}>' alt="<{$menuitem.title|default:''}>"/>
                        <{$menuitem.title}>
                    </a>
                </td>
                <td class=""><{$menuitem.desc}></td>
                <td class="width15"><{$menuitem.infos|default:''}></td>
                <td class="xo-actions width2">
                    <{if !empty($menuitem.used)}>
                        <img id="loading_<{$menuitem.file}>" src="images/spinner.gif" style="display:none;" alt="<{$smarty.const._AM_SYSTEM_LOADING}>"/>
                        <img class="tooltip" id="<{$menuitem.file}>"
                             onclick="system_setStatus( { op: 'system_activate', type: '<{$menuitem.file}>' }, '<{$menuitem.file}>', 'admin.php' )"
                             src="<{if $menuitem.status}><{xoAdminIcons 'success.png'}><{else}><{xoAdminIcons 'cancel.png'}><{/if}>"
                             alt="<{$smarty.const._AM_SYSTEM_STATUS}>" title="<{$smarty.const._AM_SYSTEM_STATUS}>"/>
                    <{/if}>
                </td>
            </tr>
        <{/if}>
    <{/foreach}>
    </tbody>
</table>
