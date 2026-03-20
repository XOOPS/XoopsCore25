<{* Bootstrap 4 Navigation — xswatch4 theme *}>
<{* Renders from xoMenuCategories when the menu system is active, otherwise falls back to static links *}>

<{function name=renderBs4SubMenu}>
    <{foreach from=$menuItems item=subItem}>
        <{if $subItem.children}>
            <li class="dropdown-submenu">
                <a class="dropdown-item"
                   href="<{if $subItem.url|default:'' neq ''}><{$subItem.url|escape}><{else}>#<{/if}>"
                   target="<{$subItem.target}>"
                   <{if $subItem.target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                    <{$subItem.prefix|default:''}> <{$subItem.title|escape}> <{$subItem.suffix|default:''}>
                </a>
                <ul class="dropdown-menu">
                    <{call name=renderBs4SubMenu menuItems=$subItem.children}>
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

    <div class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                <img src="<{$xoops_imageurl}>images/logo.png" alt="<{$xoops_sitename}>">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav mr-auto">
                    <{if isset($xoMenuCategories) && $xoMenuCategories}>
                        <{foreach from=$xoMenuCategories item=cat}>
                            <{if $cat.items}>
                                <li class="nav-item dropdown xo-hover-dropdown">
                                    <a class="nav-link dropdown-toggle"
                                       href="<{if $cat.category_url|default:'' neq ''}><{$cat.category_url|escape}><{else}>#<{/if}>"
                                       target="<{$cat.category_target}>"
                                       <{if $cat.category_target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                                        <{$cat.category_prefix|default:''}> <{$cat.category_title|escape}> <{$cat.category_suffix|default:''}>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <{call name=renderBs4SubMenu menuItems=$cat.items}>
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
                <{if !empty($xoops_search)}>
                <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0" role="search" action="<{xoAppUrl 'search.php'}>" method="get">
                        <div class="input-group mb-3">
                            <input class="form-control" type="text" name="query" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="results">
                    </form>
                </li>
                </ul>
                <{/if}>
            </div>
        </div>
    </div>

<script>
/* Touch-friendly dropdown: first tap opens submenu, second tap follows link.
   Covers both top-level categories and nested .dropdown-submenu items.
   Only closes peer menus at the same depth, never ancestors. */
(function() {
    var openedByDepth = {};
    document.addEventListener('click', function(e) {
        var link = e.target.closest('.xo-hover-dropdown > a.dropdown-toggle, .dropdown-submenu > a');
        if (!link) {
            Object.keys(openedByDepth).forEach(function(d) {
                var node = openedByDepth[d];
                if (node) { node.classList.remove('show'); var m = node.querySelector(':scope > .dropdown-menu'); if (m) m.classList.remove('show'); }
            });
            openedByDepth = {};
            return;
        }
        var li = link.closest('.xo-hover-dropdown, .dropdown-submenu');
        if (!li) { return; }

        /* Compute depth: 0 = top-level, 1+ = nested submenus */
        var depth = 0;
        var parent = li.parentElement;
        while (parent) {
            if (parent.matches && parent.matches('.dropdown-submenu, .xo-hover-dropdown')) { depth++; }
            parent = parent.parentElement;
        }

        if (openedByDepth[depth] === li) { return; } /* second tap — follow href */
        e.preventDefault();

        /* Close peer at same depth (not ancestor) */
        var peer = openedByDepth[depth];
        if (peer) {
            peer.classList.remove('show');
            var peerMenu = peer.querySelector(':scope > .dropdown-menu');
            if (peerMenu) peerMenu.classList.remove('show');
        }
        /* Close any deeper levels */
        Object.keys(openedByDepth).forEach(function(d) {
            if (parseInt(d) > depth) {
                var deep = openedByDepth[d];
                if (deep) { deep.classList.remove('show'); var dm = deep.querySelector(':scope > .dropdown-menu'); if (dm) dm.classList.remove('show'); }
                delete openedByDepth[d];
            }
        });

        li.classList.add('show');
        var menu = li.querySelector(':scope > .dropdown-menu');
        if (menu) menu.classList.add('show');
        openedByDepth[depth] = li;
    });
})();
</script>
