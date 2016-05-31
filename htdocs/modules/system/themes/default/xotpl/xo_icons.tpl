<div class="xo-title" id="xo-title-icons"><{$lang_cp}></div>
<{*start system icons*}>
<div id="xo-system-icons">
    <div id="xo-icon">
        <{foreach item=op from=$mod_options}>
            <a class="tooltip" href="<{$op.link}>" title="<{$op.desc}>">
                <img src='<{$op.icon|default:"$theme_icons/icon_options.png"}>' alt="<{$op.desc}>"/>
                <br><span><{$op.title}></span>
            </a>
        <{/foreach}>
        <a class="tooltip" href="<{xoAppUrl modules/system/admin.php}>" title="<{$smarty.const._AM_SYSTEM_CONFIG}>">
            <img src='<{"$theme_icons/configuration.png"}>'/>
            <span><{$smarty.const._AM_SYSTEM_CONFIG}></span>
        </a>
        <a class="tooltip" href="<{xoAppUrl modules/system/help.php}>" title="<{$smarty.const._AM_SYSTEM_HELP}>">
            <img src='<{"$theme_icons/help.png"}>'/>
            <span><{$smarty.const._AM_SYSTEM_HELP}></span>
        </a>
    </div>
</div>
