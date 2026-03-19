<{* Bootstrap 5 Navigation Menu *}>
<{* Renders from xoMenuCategories when available, falls back to static links *}>

<{function name=renderBs5SubMenu}>
    <{foreach from=$menuItems item=subItem}>
        <{if $subItem.children}>
            <li class="dropdown-submenu">
                <a class="dropdown-item dropdown-toggle"
                   href="<{if $subItem.url|default:'' neq ''}><{$subItem.url|escape}><{else}>#<{/if}>"
                   target="<{$subItem.target}>"
                   <{if $subItem.target == '_blank'}> rel="noopener noreferrer"<{/if}>
                   aria-expanded="false">
                    <{$subItem.prefix|default:''}> <{$subItem.title|escape}> <{$subItem.suffix|default:''}>
                </a>
                <ul class="dropdown-menu">
                    <{call name=renderBs5SubMenu menuItems=$subItem.children}>
                </ul>
            </li>
        <{else}>
            <li>
                <a class="dropdown-item"
                   href="<{if $subItem.url|default:'' neq ''}><{$subItem.url|escape}><{else}>#<{/if}>"
                   target="<{$subItem.target}>"
                   <{if $subItem.target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                    <{$subItem.prefix|default:''}> <{$subItem.title|escape}> <{$subItem.suffix|default:''}>
                </a>
            </li>
        <{/if}>
    <{/foreach}>
<{/function}>

<ul class="nav navbar-nav ms-auto">
    <{if isset($xoMenuCategories) && $xoMenuCategories}>
        <{foreach from=$xoMenuCategories item=cat}>
            <{if $cat.items}>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="<{if $cat.category_url|default:'' neq ''}><{$cat.category_url|escape}><{else}>#<{/if}>"
                       target="<{$cat.category_target}>"
                       <{if $cat.category_target == '_blank'}> rel="noopener noreferrer"<{/if}>
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <{$cat.category_prefix|default:''}> <{$cat.category_title|escape}> <{$cat.category_suffix|default:''}>
                    </a>
                    <ul class="dropdown-menu">
                        <{call name=renderBs5SubMenu menuItems=$cat.items}>
                    </ul>
                </li>
            <{else}>
                <li class="nav-item">
                    <a class="nav-link"
                       href="<{if $cat.category_url|default:'' neq ''}><{$cat.category_url|escape}><{else}>#<{/if}>"
                       target="<{$cat.category_target}>"
                       <{if $cat.category_target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                        <{$cat.category_prefix|default:''}> <{$cat.category_title|escape}> <{$cat.category_suffix|default:''}>
                    </a>
                </li>
            <{/if}>
        <{/foreach}>
    <{else}>
        <{* Fallback navigation when menu system is disabled or not yet populated *}>
        <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>"><{$smarty.const._YOURHOME|default:'Home'}></a></li>
        <{if $xoops_isadmin|default:false}>
            <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/admin.php"><{$smarty.const._ADMINISTRATION|default:'Administration'}></a></li>
        <{/if}>
        <{if $xoops_isuser|default:false}>
            <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/edituser.php"><{$smarty.const._PROFILE|default:'Account'}></a></li>
            <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/user.php?op=logout"><{$smarty.const._LOGOUT}></a></li>
        <{else}>
            <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/user.php"><{$smarty.const._LOGIN}></a></li>
            <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/register.php"><{$smarty.const._REGISTER}></a></li>
        <{/if}>
    <{/if}>
</ul>
