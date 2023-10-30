    <div id="xswatch-toolbar" class="navbar fixed-bottom navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                <img src="<{$xoops_imageurl}>images/toolsicon.png" alt="<{$xoops_sitename}>">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#admin-navbar-collapse" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="admin-navbar-collapse">
                <div class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="<{xoAppUrl '/'}>"><span class="fa fa-home"></span> <{$smarty.const.THEME_TOOLBAR_HOME}></a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:xswatchEnableBlockEdits();"><span class="fa fa-edit"></span> <{$smarty.const.THEME_TOOLBAR_SHOW_BLOCK_EDIT}></a></li>
                    <li class="nav-item dropup">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="xswatch-toolbar-settings-menu"><{$smarty.const.THEME_TOOLBAR_SETTINGS}> <span class="caret"></span></a>
                        <div class="dropdown-menu" aria-labelledby="xswatch-toolbar-settings-menu">
                            <a class="dropdown-item" href="<{xoAppUrl 'admin.php'}>"><span class="fa fa-dashboard"></span> <{$smarty.const.THEME_TOOLBAR_CONTROL_PANEL}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=preference'}>"><span class="fa fa-tasks"></span> <{$smarty.const.THEME_TOOLBAR_SYSTEM_CONFIG}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=preferences'}>"><span class="fa fa-wrench"></span> <{$smarty.const.THEME_TOOLBAR_PREFERENCES}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=preferences&op=showmod&mod=1'}>"><span class="fa fa-cog"></span> <{$smarty.const.THEME_TOOLBAR_SYSTEM_MODULE}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=preferences&op=show&confcat_id=1'}>"><span class="fa fa-list-alt"></span> <{$smarty.const.THEME_TOOLBAR_GENERAL_SETTINGS}></a>
                        </div>
                    </li>
                    <li class="nav-item dropup">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="xswatch-toolbar-tools-menu"><{$smarty.const.THEME_TOOLBAR_TOOLS}> <span class="caret"></span></a>
                        <div class="dropdown-menu" aria-labelledby="xswatch-toolbar-tools-menu">
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php'}>"><span class="fa fa-tasks"></span> <{$smarty.const.THEME_TOOLBAR_SYSTEM_OPTIONS}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=modulesadmin'}>"><span class="fa fa-list-alt"></span> <{$smarty.const.THEME_TOOLBAR_MODULES}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=blocksadmin'}>"><span class="fa fa-object-ungroup"></span> <{$smarty.const.THEME_TOOLBAR_BLOCKS}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=tplsets'}>"><span class="fa fa-file"></span> <{$smarty.const.THEME_TOOLBAR_TEMPLATES}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=maintenance'}>"><span class="fa fa-wrench"></span> <{$smarty.const.THEME_TOOLBAR_MAINTENANCE}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=preferences&op=show&confcat_id=1#debug_mode'}>"><span class="fa fa-terminal"></span> <{$smarty.const.THEME_TOOLBAR_DEBUGMODE}></a>
                            <{if isset($xoops_dirname) && $xoops_dirname != 'system'}>
                            <a class="dropdown-item" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/index.php"><span class="fa fa-hand-o-up"></span> <{$smarty.const.THEME_TOOLBAR_THIS_MODULE}></a>
                            <{/if}>
                        </div>
                    </li>
                    <li class="nav-item dropup">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="xswatch-toolbar-users-menu"><{$smarty.const.THEME_TOOLBAR_USER_TOOLS}> <span class="caret"></span></a>
                        <div class="dropdown-menu" aria-labelledby="xswatch-toolbar-users-menu">
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=users'}>"><span class="fa fa-user-o"></span> <{$smarty.const.THEME_TOOLBAR_USERS}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=groups'}>"><span class="fa fa-users"></span> <{$smarty.const.THEME_TOOLBAR_GROUPS}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=userrank'}>"><span class="fa fa-sort-amount-desc"></span> <{$smarty.const.THEME_TOOLBAR_RANKS}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=findusers'}>"><span class="fa fa-search"></span> <{$smarty.const.THEME_TOOLBAR_FIND}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=mailusers'}>"><span class="fa fa-envelope"></span> <{$smarty.const.THEME_TOOLBAR_MAIL}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=comments'}>"><span class="fa fa-comment"></span> <{$smarty.const.THEME_TOOLBAR_COMMENTS}></a>
                        </div>
                    </li>
                    <li class="nav-item dropup">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="xswatch-toolbar-image-menu"><{$smarty.const.THEME_TOOLBAR_IMAGE_TOOLS}> <span class="caret"></span></a>
                        <div class="dropdown-menu" aria-labelledby="xswatch-toolbar-image-menu">
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=images'}>"><span class="fa fa-picture-o"></span> <{$smarty.const.THEME_TOOLBAR_IMAGES}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=avatars'}>"><span class="fa fa-user"></span> <{$smarty.const.THEME_TOOLBAR_AVATARS}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=smilies'}>"><span class="fa fa-thumbs-o-up"></span> <{$smarty.const.THEME_TOOLBAR_SMILIES}></a>
                            <a class="dropdown-item" href="<{xoAppUrl 'modules/system/admin.php?fct=banners'}>"><span class="fa fa-line-chart"></span> <{$smarty.const.THEME_TOOLBAR_BANNERS}></a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="javascript:xswatchToolbarToggle();"><span class="fa fa-remove"></span> <{$smarty.const.THEME_TOOLBAR_CLOSE}></a></li>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    var toolbar_block_edits = false;

    function xswatchEnableBlockEdits() {
        if (toolbar_block_edits) {
            $('.toolbar-block-edit').hide();
            toolbar_block_edits = false;
        } else {
            $('.toolbar-block-edit').show();
            toolbar_block_edits = true;
        }
    }
    function xswatchToolbarIndOn() {
        $('#xswatch-toolbar-ind').attr('class', 'fa fa-toggle-on');
        $('#xswatch-toolbar').show();
    }
    function xswatchToolbarIndOff() {
        $('#xswatch-toolbar-ind').attr('class', 'fa fa-toggle-off');
        $('#xswatch-toolbar').hide();
    }
    function xswatchToolbarToggle() {
        var toolbar_cookie = Cookies.get('xswatch-toolbar');
        if (toolbar_cookie == 'off') {
            toolbar_cookie = 'on';
            xswatchToolbarIndOn();
        } else {
            toolbar_cookie = 'off';
            xswatchToolbarIndOff();
        }
        Cookies.set('xswatch-toolbar', toolbar_cookie, { expires: 365, sameSite: 'strict' });
    }
    // set initial conditions based on cookie
    var toolbar_cookie = Cookies.get('xswatch-toolbar');
    if (toolbar_cookie == 'off') {
        xswatchToolbarIndOff();
    } else {
        xswatchToolbarIndOn();
    }
</script>
