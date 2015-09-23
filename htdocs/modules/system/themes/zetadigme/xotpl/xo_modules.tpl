<div class="CPbigTitle"><{$lang_insmodules}></div>
<div class="cpicon">
    <{foreach item=mod from=$modules}>
        <a class="tooltip" href="<{$mod.link}>" title="<{$mod.description}>">
            <img src="<{$mod.icon|default:'$theme_img/modules.png'}>" alt="<{$mod.title}>"/>
            <span><{$mod.title}></span>
        </a>
    <{/foreach}>
</div>


