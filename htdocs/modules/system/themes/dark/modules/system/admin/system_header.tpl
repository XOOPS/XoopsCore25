<ul id="xo-breadcrumb" class="ui-corner-all" style="background-image:url('<{xoAdminNav}>bc_bg.png');">
    <{foreach item=breadcrumb from=$xo_sys_breadcrumb|default:null}>
        <{if $breadcrumb.home}>
            <li><a class="tooltip" href="<{$breadcrumb.link}>" title="<{$breadcrumb.title}>" style="background-image:url('<{$theme_img}>/bc_separator.png');"><img
                            class="home" src="<{$theme_img}>/home.png" alt="<{$breadcrumb.title}>"/></a></li>
        <{else}>
            <{if $breadcrumb.link}>
                <li><a class="tooltip" href="<{$breadcrumb.link}>" title="<{$breadcrumb.title}>" style="background-image:url('<{$theme_img}>/bc_separator.png');"><{$breadcrumb.title}></a>
                </li>
            <{else}>
                <li><{$breadcrumb.title}></li>
            <{/if}>
        <{/if}>
    <{/foreach}>
    <{if !empty($xo_sys_help)}>
        <li class="xo-help">
            <a class="cursorhelp tooltip help_view xo-help-button" title="<{$smarty.const._AM_SYSTEM_HELP_VIEW}>">
                <img src="<{xoAdminIcons 'help.png'}>" alt="<{$smarty.const._AM_SYSTEM_HELP_VIEW}>"/>
            </a>
            <a class="cursorhelp tooltip help_hide xo-help-button hidden" title="<{$smarty.const._AM_SYSTEM_HELP_HIDE}>">
                <img src="<{xoAdminIcons 'help_bw.png'}>" alt="<{$smarty.const._AM_SYSTEM_HELP_HIDE}>"/>
            </a>
        </li>
    <{/if}>
</ul>
<{if !empty($help_content)}>
    <div class="hide" id="xo-system-help">
        <{include file="$help_content"}>
    </div>
<{/if}>
<{if !empty($xo_sys_tips)}>
    <div class="tips ui-corner-all">
        <img class="floatleft tooltip" src="<{xoAdminIcons 'tips.png'}>" alt="<{$smarty.const._AM_SYSTEM_TIPS}>" title="<{$smarty.const._AM_SYSTEM_TIPS}>"/>

        <div class="floatleft"><{$xo_sys_tips}></div>
        <div class="clear">&nbsp;</div>
    </div>
<{else}>
    <br>
<{/if}>
