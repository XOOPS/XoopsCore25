<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link<{if empty($block.nothome)}> active<{/if}>" href="<{xoAppUrl ' '}>" title="<{$block.lang_home}>"><span class="fa fa-home text-info fa-fw"></span> <{$block.lang_home}></a>
    </li>
    
    <!-- start module menu loop -->
    <{foreach item=module from=$block.modules|default:null}>
        <li class="nav-item <{if $module.highlight|default:false}>bg-secondary rounded pb-0<{/if}>">
            <a class="nav-link<{if $module.highlight|default:false}> active<{/if}>" href="<{$xoops_url}>/modules/<{$module.directory}>/" title="<{$module.name}>">
                <span class="fa <{if $module.highlight|default:false}>fa-check-square-o text-info<{else}>fa-square-o text-muted<{/if}> fa-fw"></span> <{$module.name}>
            </a>

            <{if $module.sublinks|default:false}>
                <ul class="ms-3 pb-1">
                    <{foreach item=sublink from=$module.sublinks|default:null}>
                        <li>
                            <a class="text-decoration-none" href="<{$sublink.url}>" title="<{$sublink.name}>"><{$sublink.name}></a>
                        </li>
                    <{/foreach}>
                </ul>
            <{/if}>
            
    <{/foreach}>
    <!-- end module menu loop -->
</ul>
