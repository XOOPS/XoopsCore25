<div class="CPbigTitle"><{$lang_cp}></div>
<br>
<div class="cpicon">
    <{foreach item=op from=$mod_options}>
        <a class="tooltip" href="<{$op.link}>" title="<{$op.desc}>">
            <img src='<{$op.icon|default:"$theme_icons/icon_options.png"}>' alt="<{$op.desc}>"/>
            <span class="shadow"><{$op.title}></span>
        </a>
    <{/foreach}>
</div>
