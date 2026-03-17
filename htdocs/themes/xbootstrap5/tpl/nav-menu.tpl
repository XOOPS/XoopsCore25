<{* enable adhesive menus by setting value to 'yes', disable using 'no' *}>
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
                    <{* Recursive function for rendering nested dropdown items (BS5) *}>
                    <{function name=renderMenu}>
                        <{assign var="level" value=$level|default:0}>
                        <ul class="dropdown-menu">
                        <{foreach $items as $item}>
                            <li<{if $item.children}> class="dropdown-submenu"<{/if}>>
                                <a class="dropdown-item<{if $item.children}> dropdown-toggle<{/if}>"
                                   href="<{if $item.url neq ''}><{$item.url|escape}><{else}>#<{/if}>"
                                   <{if $item.children}>role="button" data-bs-toggle="dropdown" aria-expanded="false"<{/if}>
                                   target="<{$item.target}>"<{if $item.target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                                    <{$item.prefix}> <{$item.title|escape}> <{$item.suffix}>
                                </a>
                                <{if $item.children}>
                                    <{call name=renderMenu items=$item.children level=$level+1}>
                                <{/if}>
                            </li>
                        <{/foreach}>
                        </ul>
                    <{/function}>

                    <ul class="nav navbar-nav">
                        <{if isset($xoMenuCategories) && $xoMenuCategories}>
                        <{foreach $xoMenuCategories as $cat}>
                            <li class="nav-item<{if $cat.items}> dropdown<{/if}>">
                                <a class="nav-link<{if $cat.items}> dropdown-toggle<{/if}>"
                                   href="<{$cat.category_url|escape|default:'#'}>"
                                   <{if $cat.items}>role="button" data-bs-toggle="dropdown" aria-expanded="false"<{/if}>
                                   target="<{$cat.category_target}>"<{if $cat.category_target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                                    <{$cat.category_prefix}> <{$cat.category_title|escape}> <{$cat.category_suffix}>
                                </a>
                                <{if $cat.items}>
                                    <{call name=renderMenu items=$cat.items level=0}>
                                <{/if}>
                            </li>
                        <{/foreach}>
                        <{else}>
                        <{* Fallback: static menus when menu system is not yet installed *}>
                        <li class="nav-item active"><a href="<{$xoops_url}>" class="nav-link"><{$smarty.const.THEME_HOME}></a></li>
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
    </div><!-- .navbar-wrapper -->
        <{if $stickyHeader === 'yes'}>
</header>
<{/if}>
