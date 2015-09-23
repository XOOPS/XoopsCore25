<div id="navOptionsCP">
    <div id="toolbar">
        <a class="tooltip" href="<{xoAppUrl modules/system/help.php}>" title="<{$smarty.const._AM_SYSTEM_HELP}>">
            <img src='<{"$theme_icons/help.png"}>' alt='<{$smarty.const._AM_SYSTEM_HELP}>'/>
        </a>
        <{foreach item=op from=$mod_options}>
            <a class="tooltip" href="<{$op.link}>" title="<strong class='italic'><{$op.title}></strong>: <{$op.desc}>">
                <img src='<{$op.icon|default:"$theme_icons/icon_options_small.png"}>' alt="<{$op.title}>"/>
            </a>
        <{/foreach}>

        <{if $moddir!='system' && $mod_options}>
            <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=<{$modid}>" title="<{$lang_preferences}>">
                <img src="<{$theme_icons}>/prefs_small.png" alt="<{$lang_preferences}>"/>
            </a>
        <{/if}>
    </div>
</div>
