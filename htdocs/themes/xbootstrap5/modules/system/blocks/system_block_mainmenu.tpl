<ul class="nav flex-column">
    <li class="<{if !$block.nothome|default:''}>active<{/if}>">
        <a href="<{xoAppUrl ' '}>" title="<{$block.lang_home}>">
            <span class="fa-solid fa-home"></span> <{$block.lang_home}>
        </a>
    </li>
    <{foreach item=module from=$block.modules|default:null}>
        <li class="nav-item<{if $module.highlight|default:false}> active<{/if}>">
            <a class="nav-link" href="<{$xoops_url}>/modules/<{$module.directory}>/" title="<{$module.name}>">
                <span class="<{if isset($iconOverrides[$module.directory].main)}>
                                   <{$iconOverrides[$module.directory].main}>
                               <{else}>
                                   <{$module.icon|default:'fa-solid fa-caret-right'}>
                               <{/if}>"></span>
                <{$module.name}>
            </a>
            <ul class="no-bullets">
                <{foreach item=sublink from=$module.sublinks|default:null}>
                    <li>
                        <a class="dropdown" href="<{$sublink.full_url}>" title="<{$sublink.name}>">
                            <span class="<{if isset($iconOverrides[$module.directory].sub[$sublink.id])}>
                                               <{$iconOverrides[$module.directory].sub[$sublink.id]}>
                                           <{else}>
                                               <{$sublink.icon|default:'fa-solid fa-caret-right'}>
                                           <{/if}>"></span>
                            <{$sublink.name}>
                        </a>
                    </li>
                <{/foreach}>
            </ul>
        </li>
    <{/foreach}>
</ul>
