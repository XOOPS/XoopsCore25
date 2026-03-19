<{* Bootstrap 5 Navigation Menu *}>
<{* Renders from xoMenuCategories when available, falls back to static links *}>

<{function name=renderBs5SubMenu}>
    <{foreach from=$menuItems item=subItem}>
        <{if $subItem.children}>
            <li class="dropdown-submenu">
                <a class="dropdown-item dropdown-toggle"
                   href="<{$subItem.url}>"
                   <{if $subItem.target}> target="_blank" rel="noopener noreferrer"<{/if}>>
                    <{$subItem.prefix|default:''}><{$subItem.title|escape}><{$subItem.suffix|default:''}>
                </a>
                <ul class="dropdown-menu">
                    <{call name=renderBs5SubMenu menuItems=$subItem.children}>
                </ul>
            </li>
        <{else}>
            <li>
                <a class="dropdown-item"
                   href="<{$subItem.url}>"
                   <{if $subItem.target}> target="_blank" rel="noopener noreferrer"<{/if}>>
                    <{$subItem.prefix|default:''}><{$subItem.title|escape}><{$subItem.suffix|default:''}>
                </a>
            </li>
        <{/if}>
    <{/foreach}>
<{/function}>

<ul class="nav navbar-nav ms-auto">
    <{if $xoMenuCategories|default:false}>
        <{foreach from=$xoMenuCategories item=cat}>
            <{if $cat.items}>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="<{$cat.category_url}>"
                       data-bs-toggle="dropdown"
                       aria-expanded="false"
                       <{if $cat.category_target}> target="_blank" rel="noopener noreferrer"<{/if}>>
                        <{$cat.category_prefix|default:''}><{$cat.category_title|escape}><{$cat.category_suffix|default:''}>
                    </a>
                    <ul class="dropdown-menu">
                        <{call name=renderBs5SubMenu menuItems=$cat.items}>
                    </ul>
                </li>
            <{else}>
                <li class="nav-item">
                    <a class="nav-link"
                       href="<{$cat.category_url}>"
                       <{if $cat.category_target}> target="_blank" rel="noopener noreferrer"<{/if}>>
                        <{$cat.category_prefix|default:''}><{$cat.category_title|escape}><{$cat.category_suffix|default:''}>
                    </a>
                </li>
            <{/if}>
        <{/foreach}>
    <{else}>
        <{* Fallback: static navigation *}>
        <li class="nav-item"><a class="nav-link" href="<{xoAppUrl}>">Home</a></li>
        <{if $xoops_isadmin}>
            <li class="nav-item"><a class="nav-link" href="<{xoAppUrl admin.php}>">Admin</a></li>
        <{/if}>
        <{if $xoops_isuser}>
            <li class="nav-item"><a class="nav-link" href="<{xoAppUrl edituser.php}>">Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="<{xoAppUrl user.php?op=logout}>">Logout</a></li>
        <{else}>
            <li class="nav-item"><a class="nav-link" href="<{xoAppUrl user.php}>">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="<{xoAppUrl register.php}>">Register</a></li>
        <{/if}>
    <{/if}>
</ul>
