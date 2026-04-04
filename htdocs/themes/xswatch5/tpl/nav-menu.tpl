<{* Bootstrap 5 Navigation — xswatch5 theme *}>
<{* Renders from xoMenuCategories when the menu system is active, otherwise falls back to static links *}>

<{function name=renderBs5SubMenu}>
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

    <div class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                <img src="<{$xoops_imageurl}>images/logo.png" alt="<{$xoops_sitename}>">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav me-auto">
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
                <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form class="d-flex my-2 my-lg-0" role="search" action="<{xoAppUrl 'search.php'}>" method="get">
                        <div class="input-group">
                            <input class="form-control" type="text" name="query" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
                            <button class="btn btn-secondary" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
                        </div>
                        <input type="hidden" name="action" value="results">
                    </form>
                </li>
                </ul>
                <{/if}>
                <ul class="navbar-nav ms-2">
                <li class="nav-item">
                    <button id="xswatch-mode-btn" class="btn btn-sm btn-outline-secondary" type="button" onclick="xswatchToggleTheme()">
                        <i id="xswatch-theme-icon" class="fa-solid fa-moon"></i> <span id="xswatch-mode-label"><{$smarty.const.THEME_DARK_MODE}></span>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle ms-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <{$smarty.const.THEME_SWITCHER}>
                    </button>
                    <ul id="xswatch-variant-menu" class="dropdown-menu dropdown-menu-end" style="max-height: 400px; overflow-y: auto;">
                        <{foreach from=$xswatchVariants item=variant}>
                        <li><a class="dropdown-item" href="#" data-variant="<{$variant.dir}>"><{$variant.label}></a></li>
                        <{/foreach}>
                    </ul>
                </li>
                </ul>
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
<script>
const XSWATCH_DARK_LABEL = '<{$smarty.const.THEME_DARK_MODE|escape:"javascript"}>';
const XSWATCH_LIGHT_LABEL = '<{$smarty.const.THEME_LIGHT_MODE|escape:"javascript"}>';

function xswatchUpdateModeUI(mode) {
    const icon = document.getElementById('xswatch-theme-icon');
    const label = document.getElementById('xswatch-mode-label');
    if (icon) {
        icon.className = mode === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
    if (label) {
        label.textContent = mode === 'dark' ? XSWATCH_LIGHT_LABEL : XSWATCH_DARK_LABEL;
    }
}

function xswatchToggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-bs-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', next);
    localStorage.setItem('xswatch-theme', next);
    xswatchUpdateModeUI(next);
}

function xswatchSwitchVariant(variant) {
    const bsCss = document.getElementById('xswatch-bootstrap-css');
    const xoCss = document.getElementById('xswatch-xoops-css');
    const ccCss = document.getElementById('xswatch-consent-css');
    if (bsCss) { bsCss.href = bsCss.href.replace(/css-[^/]+/, variant); }
    if (xoCss) { xoCss.href = xoCss.href.replace(/css-[^/]+/, variant); }
    if (ccCss) { ccCss.href = ccCss.href.replace(/css-[^/]+/, variant); }
    localStorage.setItem('xswatch-variant', variant);
    xswatchHighlightActiveVariant(variant);
}

function xswatchHighlightActiveVariant(variant) {
    const menu = document.getElementById('xswatch-variant-menu');
    if (!menu) { return; }
    menu.querySelectorAll('.dropdown-item').forEach(function(item) {
        item.classList.toggle('active', item.getAttribute('data-variant') === variant);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const theme = document.documentElement.getAttribute('data-bs-theme');
    xswatchUpdateModeUI(theme);

    const menu = document.getElementById('xswatch-variant-menu');
    if (menu) {
        menu.addEventListener('click', function(e) {
            const item = e.target.closest('[data-variant]');
            if (item) {
                e.preventDefault();
                xswatchSwitchVariant(item.getAttribute('data-variant'));
            }
        });
    }

    const currentVariant = localStorage.getItem('xswatch-variant')
        || document.getElementById('xswatch-bootstrap-css').href.match(/css-[^/]+/)?.[0]
        || 'css-cerulean';
    xswatchHighlightActiveVariant(currentVariant);
});
</script>
