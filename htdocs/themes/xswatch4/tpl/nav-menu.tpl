    <div class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                <img src="<{$xoops_imageurl}>images/logo.png" alt="<{$xoops_sitename}>">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <{* Recursive function for rendering nested dropdown items (BS4) *}>
                <{function name=renderMenu}>
                    <{assign var="level" value=$level|default:0}>
                    <div class="dropdown-menu">
                    <{foreach $items as $item}>
                        <{if $item.children}>
                            <div class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle"
                                   href="<{if $item.url neq ''}><{$item.url|escape}><{else}>#<{/if}>"
                                   target="<{$item.target}>"<{if $item.target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                                    <{$item.prefix}> <{$item.title|escape}> <{$item.suffix}>
                                </a>
                                <{call name=renderMenu items=$item.children level=$level+1}>
                            </div>
                        <{else}>
                            <a class="dropdown-item"
                               href="<{if $item.url neq ''}><{$item.url|escape}><{else}>#<{/if}>"
                               target="<{$item.target}>"<{if $item.target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                                <{$item.prefix}> <{$item.title|escape}> <{$item.suffix}>
                            </a>
                        <{/if}>
                    <{/foreach}>
                    </div>
                <{/function}>

                <ul class="navbar-nav mr-auto">
                    <{if isset($xoMenuCategories) && $xoMenuCategories}>
                    <{foreach $xoMenuCategories as $cat}>
                        <li class="nav-item<{if $cat.items}> dropdown<{/if}>">
                            <a class="nav-link<{if $cat.items}> dropdown-toggle<{/if}>"
                               href="<{$cat.category_url|escape|default:'#'}>"
                               <{if $cat.items}>role="button" data-toggle="dropdown" aria-expanded="false"<{/if}>
                               target="<{$cat.category_target}>"<{if $cat.category_target == '_blank'}> rel="noopener noreferrer"<{/if}>>
                                <{$cat.category_prefix}> <{$cat.category_title|escape}> <{$cat.category_suffix}>
                            </a>
                            <{if $cat.items}>
                                <{call name=renderMenu items=$cat.items level=0}>
                            <{/if}>
                        </li>
                    <{/foreach}>
                    <{else}>
                    <{* Fallback: static nav when menu system is not active *}>
                    <li class="nav-item">
                        <a class="nav-link" href="<{$xoops_url}>"><{$smarty.const.THEME_HOME}></a>
                    </li>
                    <{if $xoops_isadmin|default:false}>
                    <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/admin.php"><{$smarty.const._ADMINISTRATION|default:'Administration'}></a></li>
                    <{/if}>
                    <{if $xoops_isuser|default:false}>
                    <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/user.php"><{$smarty.const._PROFILE|default:'Account'}></a></li>
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
