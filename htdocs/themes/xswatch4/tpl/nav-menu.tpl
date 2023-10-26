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

                    <li class="nav-item">
                        <a class="nav-link" href="<{$xoops_url}>"><{$smarty.const.THEME_HOME}></a>
                    </li>

                    <{xoInboxCount assign='unread_count'}>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="xswatch-account-menu"><{$smarty.const.THEME_ACCOUNT}> <span class="caret"></span></a>
                        <div class="dropdown-menu" aria-labelledby="xswatch-account-menu">
                            <{if !empty($xoops_isuser)}>
                            <a class="dropdown-item" href="<{$xoops_url}>/user.php"><{$smarty.const.THEME_ACCOUNT_EDIT}></a>
                            <a class="dropdown-item" href="<{$xoops_url}>/viewpmsg.php"><{$smarty.const.THEME_ACCOUNT_MESSAGES}> <span class="badge badge-primary badge-pill"><{xoInboxCount}></span></a>
                            <a class="dropdown-item" href="<{$xoops_url}>/notifications.php"><{$smarty.const.THEME_ACCOUNT_NOTIFICATIONS}></a>
                            <a class="dropdown-item" href="<{$xoops_url}>/user.php?op=logout"><{$smarty.const.THEME_ACCOUNT_LOGOUT}></a>
                            <{if !empty($xoops_isadmin)}>
                            <a class="dropdown-item" href="javascript:xswatchToolbarToggle();"><{$smarty.const.THEME_ACCOUNT_TOOLBAR}> <span id="xswatch-toolbar-ind"></span></a>
                            <{/if}>
                            <{else}>
                            <a class="dropdown-item" href="<{$xoops_url}>/user.php"><{$smarty.const.THEME_ACCOUNT_LOGIN}></a>
                            <a class="dropdown-item" href="<{$xoops_url}>/register.php"><{$smarty.const.THEME_ACCOUNT_REGISTER}></a>
                            <{/if}>
                        </div>
                    </li>

                    <!-- begin custom menus - customize these for your system -->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:;"><{$smarty.const.THEME_MODULE1}></a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="xswatch-custom-menu"><{$smarty.const.THEME_MODULE2}> <span class="caret"></span></a>
                        <div class="dropdown-menu" aria-labelledby="xswatch-custom-menu">
                            <a class="dropdown-item" href="javascript:;">Topic 1</a>
                            <a class="dropdown-item" href="javascript:;">Topic 2</a>
                            <a class="dropdown-item" href="javascript:;">Topic 3</a>
                            <a class="dropdown-item" href="javascript:;">Topic 4</a>
                            <a class="dropdown-item" href="javascript:;">Topic 5</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<{$xoops_url}>/modules/newbb"><{$smarty.const.THEME_MODULE3}></a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<{$xoops_url}>/modules/contact"><{$smarty.const.THEME_MODULE4}></a>
                    </li>
                    <!-- end custom menus -->
                </ul>
                <{if !empty($xoops_search)}>
                <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0" role="search" action="<{xoAppUrl 'search.php'}>" method="get">
						<div class="input-group mb-3">
							<input class="form-control" type="text" name="query" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
							<div class="input-group-append">
								<button class="btn btn-secondary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
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
