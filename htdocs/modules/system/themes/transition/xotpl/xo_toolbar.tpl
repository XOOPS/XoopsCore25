<div id="xo-nav-options">
    <div id="xo-modname">
        <{$modname}>
    </div>
    <ul id="xo-toolbar">
        <{foreach item=op from=$mod_options}>
        <li>
            <a class="tooltip" href="<{$op.link}>" title="<{$op.title}>">
                <img src='<{$op.icon|default:"$theme_icons/icon_options.png"}>' alt="<{$op.title}>"/>
            </a>
        </li>
        <{/foreach}>

        <{if $moddir!='system' && $mod_options}>
        <li>
            <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=<{$modid}>" title="<{$smarty.const._OXYGEN_SITEPREF}>">
                <img src="<{$theme_icons}>/prefs.png" alt="<{$smarty.const._OXYGEN_SITEPREF}>"/>
            </a>
        </li>
        <{/if}>
        <li>
        <a class="tooltip" href="<{xoAppUrl modules/system/help.php}>" title="<{$smarty.const._AM_SYSTEM_HELP}>">
            <img src='<{"$theme_icons/help.png"}>'/>
        </a>
        </li>
    </ul>
</div>
