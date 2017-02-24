<div id="xswatch-toolbar" class="collapse">
    <div class="navbar navbar-inverse navbar-fixed-bottom">
        <div class="container">
            <div class="navbar-header">
                <button data-target="#admin-navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                    <img src="<{$xoops_imageurl}>images/toolsicon.png" alt="<{$xoops_sitename}>">
                </a>
            </div>
            <div class="navbar-collapse collapse" id="admin-navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="<{xoAppUrl /}>"><span class="glyphicon glyphicon-home"></span> <{$smarty.const.THEME_TOOLBAR_HOME}></a></li>
                    <li><a href="javascript:xswatchEnableBlockEdits();"><span class="glyphicon glyphicon-edit"></span> <{$smarty.const.THEME_TOOLBAR_SHOW_BLOCK_EDIT}></a></li>
                    <li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><{$smarty.const.THEME_TOOLBAR_SETTINGS}><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<{xoAppUrl admin.php}>"><span class="glyphicon glyphicon-dashboard"></span> <{$smarty.const.THEME_TOOLBAR_CONTROL_PANEL}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=preference}>"><span class="glyphicon glyphicon-tasks"></span> <{$smarty.const.THEME_TOOLBAR_SYSTEM_CONFIG}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=preferences}>"><span class="glyphicon glyphicon-wrench"></span> <{$smarty.const.THEME_TOOLBAR_PREFERENCES}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=preferences&op=showmod&mod=1}>"><span class="glyphicon glyphicon-cog"></span> <{$smarty.const.THEME_TOOLBAR_SYSTEM_MODULE}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=preferences&op=show&confcat_id=1}>"><span class="glyphicon glyphicon-list-alt"></span> <{$smarty.const.THEME_TOOLBAR_GENERAL_SETTINGS}></a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><{$smarty.const.THEME_TOOLBAR_TOOLS}><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<{xoAppUrl modules/system/admin.php}>"><span class="glyphicon glyphicon-tasks"></span> <{$smarty.const.THEME_TOOLBAR_SYSTEM_OPTIONS}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=modulesadmin}>"><span class="glyphicon glyphicon-list-alt"></span> <{$smarty.const.THEME_TOOLBAR_MODULES}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=blocksadmin}>"><span class="glyphicon glyphicon-object-align-top"></span> <{$smarty.const.THEME_TOOLBAR_BLOCKS}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=tplsets}>"><span class="glyphicon glyphicon-file"></span> <{$smarty.const.THEME_TOOLBAR_TEMPLATES}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=maintenance}>"><span class="glyphicon glyphicon-wrench"></span> <{$smarty.const.THEME_TOOLBAR_MAINTENANCE}></a></li>
                        </ul>
                    </li>

                    <li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><{$smarty.const.THEME_TOOLBAR_USER_TOOLS}><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=users}>"><span class="glyphicon glyphicon-pencil"></span> <{$smarty.const.THEME_TOOLBAR_USERS}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=groups}>"><span class="glyphicon glyphicon-tags"></span> <{$smarty.const.THEME_TOOLBAR_GROUPS}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=userrank}>"><span class="glyphicon glyphicon-sort-by-attributes-alt"></span> <{$smarty.const.THEME_TOOLBAR_RANKS}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=findusers}>"><span class="glyphicon glyphicon-search"></span> <{$smarty.const.THEME_TOOLBAR_FIND}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=mailusers}>"><span class="glyphicon glyphicon-envelope"></span> <{$smarty.const.THEME_TOOLBAR_MAIL}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=comments}>"><span class="glyphicon glyphicon-comment"></span> <{$smarty.const.THEME_TOOLBAR_COMMENTS}></a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><{$smarty.const.THEME_TOOLBAR_IMAGE_TOOLS}><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=images}>"><span class="glyphicon glyphicon-picture"></span> <{$smarty.const.THEME_TOOLBAR_IMAGES}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=avatars}>"><span class="glyphicon glyphicon-user"></span> <{$smarty.const.THEME_TOOLBAR_AVATARS}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=smilies}>"><span class="glyphicon glyphicon-thumbs-up"></span> <{$smarty.const.THEME_TOOLBAR_SMILIES}></a></li>
                            <li><a href="<{xoAppUrl modules/system/admin.php?fct=banners}>"><span class="glyphicon glyphicon-link"></span> <{$smarty.const.THEME_TOOLBAR_BANNERS}></a></li>
                        </ul>
                    </li>
                    <li><a href="javascript:xswatchToolbarToggle();"><span class="glyphicon glyphicon-remove"></span> <{$smarty.const.THEME_TOOLBAR_CLOSE}></a></li>
                </ul>
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
        $('#xswatch-toolbar-ind').attr('class', 'glyphicon glyphicon-remove');
        $('#xswatch-toolbar').show();
    }
    function xswatchToolbarIndOff() {
        $('#xswatch-toolbar-ind').attr('class', 'glyphicon glyphicon-expand');
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
        Cookies.set('xswatch-toolbar', toolbar_cookie, { expires: 365 });
    }
    // set initial conditions based on cookie
    var toolbar_cookie = Cookies.get('xswatch-toolbar');
    if (toolbar_cookie == 'off') {
        xswatchToolbarIndOff();
    } else {
        xswatchToolbarIndOn();
    }
</script>
