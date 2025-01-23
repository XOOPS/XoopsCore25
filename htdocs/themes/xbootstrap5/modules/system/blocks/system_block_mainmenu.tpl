<ul class="nav flex-column">
    <li class="<{if !$block.nothome|default:''}>active<{/if}>"><a href="<{xoAppUrl ' '}>" title="<{$block.lang_home}>"><span
                    class="fa fa-home"></span> <{$block.lang_home}></a></li>
    <!-- start module menu loop -->
    <{foreach item=module from=$block.modules|default:null}>
        <li class="nav-item<{if $module.highlight|default:false}> active<{/if}>">
            <a class="nav-link" href="<{$xoops_url}>/modules/<{$module.directory}>/" title="<{$module.name}>"><span class="fa fa-check"></span>
                <{$module.name}>
            </a>
            <ul>
                <{foreach item=sublink from=$module.sublinks|default:null}>
                    <li>
                        <a class="dropdown" href="<{$sublink.url}>" title="<{$sublink.name}>"><{$sublink.name}></a>
                    </li>
                <{/foreach}>
            </ul>
        </li>
    <{/foreach}>
    <!-- end module menu loop -->
</ul>
