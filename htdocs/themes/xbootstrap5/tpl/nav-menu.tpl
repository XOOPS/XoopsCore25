<{* Bootstrap 5 Navigation — xbootstrap5 theme *}>
<{* Renders from xoMenuCategories when the menu system is active, otherwise falls back to static links *}>

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

<{* Enable sticky header by setting value to 'yes' *}>
<{assign var='stickyHeader' value='yes'}>
<{if $stickyHeader === 'yes'}>
<header class="navbar navbar-expand-lg navbar-dark bg-dark adhesiveHeader">
<{/if}>
    <div class="navbar-wrapper">
        <div class="navbar navbar-dark bg-dark navbar-static-top global-nav navbar-expand-sm">
            <div class="container">
                <div class="navbar-header">
                    <button data-bs-target=".navbar-collapse" data-bs-toggle="collapse" class="navbar-toggler" type="button">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                        <img src="<{$xoops_imageurl}>images/logo.png" alt="<{$xoops_sitename}>">
                    </a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <{if isset($xoMenuCategories) && $xoMenuCategories}>
                            <{foreach from=$xoMenuCategories item=cat}>
                                <{if $cat.items}>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link"
                                           href="<{if $cat.category_url|default:'' neq ''}><{$cat.category_url|escape}><{else}>#<{/if}>"
                                           target="<{$cat.category_target}>"
                                           <{if $cat.category_target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                                            <{$cat.category_prefix|default:''}> <{$cat.category_title|escape}> <{$cat.category_suffix|default:''}>
                                        </a><a class="nav-link dropdown-toggle dropdown-toggle-split" href="#"
                                           role="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="visually-hidden">Toggle</span></a>
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
                            <{* Fallback when menu system is disabled or not yet populated *}>
                            <li class="nav-item active"><a class="nav-link" href="<{$xoops_url}>"><{$smarty.const._YOURHOME|default:'Home'}></a></li>
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

                    <{if $xoops_search|default:false}>
                        <form class="form-inline ms-auto" role="search" action="<{xoAppUrl 'search.php'}>" method="get">
                            <div class="input-group">
                                <input type="text" name="query" class="form-control" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
                                <input type="hidden" name="action" value="results">
                                <button class="btn btn-primary" type="submit"><{$smarty.const.THEME_SEARCH_BUTTON}></button>
                            </div>
                        </form>
                    <{/if}>
                </div>
            </div>
        </div>
    </div>
<{if $stickyHeader === 'yes'}>
</header>
<{/if}>
