<div id="xo-nav-options">
    <div id="xo-modname">
        <{$modname}>
    </div>
    <div id="xo-toolbar">
        <{foreach item=op from=$mod_options}>
            <a class="tooltip" href="<{$op.link}>" title="<{$op.title}>">
                <img src='<{$op.icon|default:"$theme_icons/icon_options.png"}>' alt="<{$op.title}>"/>
            </a>
        <{/foreach}>

        <{if $moddir!='system' && $mod_options}>
            <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=<{$modid}>" title="<{$smarty.const._OXYGEN_SITEPREF}>">
                <img src="<{$theme_icons}>/prefs.png" alt="<{$smarty.const._OXYGEN_SITEPREF}>"/>
            </a>
        <{/if}>
        <a class="tooltip" href="<{xoAppUrl modules/system/help.php}>" title="<{$smarty.const._AM_SYSTEM_HELP}>">
            <img src='<{"$theme_icons/help.png"}>'/>
        </a>
    </div>
</div>
