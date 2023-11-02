<div class="xo-title" id="xo-title-modules"><{$smarty.const._OXYGEN_INSTALLEDMODULES}></div>
<div id="xo-module-icons">
    <{foreach item=mod from=$modules|default:null}>
        <a class="tooltip" href="<{$mod.link}>" title="<{$mod.description}>">
            <img src='<{$mod.icon|default:"$theme_img/modules.png"}>' alt="<{$mod.title}>"/>
            <span><{$mod.title}></span>
        </a>
    <{/foreach}>
</div>
