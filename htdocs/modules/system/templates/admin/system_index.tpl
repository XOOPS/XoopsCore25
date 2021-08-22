<{include file="db:system_header.tpl"}>
<script type="text/javascript">
    IMG_ON = '<{xoAdminIcons success.png}>';
    IMG_OFF = '<{xoAdminIcons cancel.png}>';
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
    <{foreach item=menu from=$menu}>
        <{if $menu.title}>
            <tr class="<{cycle values='even,odd'}>">
                <td class="bold width15">
                    <a class="tooltip" href="admin.php?fct=<{$menu.file}>" title="<{$smarty.const._AM_SYSTEM_GO}>: <{$menu.title}>">
                        <img class="xo-imgmini" src='<{$theme_icons}>/<{$menu.icon}>' alt="<{$menu.title}>"/>
                        <{$menu.title}>
                    </a>
                </td>
                <td class=""><{$menu.desc}></td>
                <td class="width15"><{$menu.infos|default:''}></td>
                <td class="xo-actions width2">
                    <{if $menu.used|default:false}>
                        <img id="loading_<{$menu.file}>" src="images/spinner.gif" style="display:none;" alt="<{$smarty.const._AM_SYSTEM_LOADING}>"/>
                        <img class="tooltip" id="<{$menu.file}>"
                             onclick="system_setStatus( { op: 'system_activate', type: '<{$menu.file}>' }, '<{$menu.file}>', 'admin.php' )"
                             src="<{if $menu.status}><{xoAdminIcons success.png}><{else}><{xoAdminIcons cancel.png}><{/if}>"
                             alt="<{$smarty.const._AM_SYSTEM_STATUS}>" title="<{$smarty.const._AM_SYSTEM_STATUS}>"/>
                    <{/if}>
                </td>
            </tr>
        <{/if}>
    <{/foreach}>
    </tbody>
</table>
