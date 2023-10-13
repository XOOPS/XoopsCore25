<div id="mainmenu">
    <a class="menuTop <{if empty($block.nothome)}>maincurrent<{/if}>" href="<{xoAppUrl}>" title="<{$block.lang_home}>"><{$block.lang_home}></a>
    <!-- start module menu loop -->
    <{foreach item=module from=$block.modules|default:null}>
        <a class="menuMain <{if !empty($module.highlight)}>maincurrent<{/if}>" href="<{$xoops_url}>/modules/<{$module.directory}>/" title="<{$module.name}>"><{$module.name}></a>
        <{foreach item=sublink from=$module.sublinks|default:null}>
            <a class="menuSub" href="<{$sublink.url}>" title="<{$sublink.name}>"><{$sublink.name}></a>
        <{/foreach}>
    <{/foreach}>
    <!-- end module menu loop -->
</div>
