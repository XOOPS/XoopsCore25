<div class="xo-title" id="xo-title-modules"><{$smarty.const._OXYGEN_INSTALLEDMODULES}></div>
<div id="xo-module-icons">
    <{foreach item=mod from=$modules}>
        <a class="tooltip" href="<{$mod.link}>" title="<img src='<{$mod.icon}>'/><span><{$mod.title}></span><br><span><{$mod.description}></span></span>">
            <img src='<{$mod.icon|default:"$theme_img/modules.png"}>' alt="<{$mod.title}>"/>
            <span><{$mod.title}></span>
        </a>
    <{/foreach}>
</div>
