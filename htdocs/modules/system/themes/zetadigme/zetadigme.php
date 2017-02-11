<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
xoops_load('gui', 'system');

/**
 * Xoops Cpanel Paradigme GUI class
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             system
 * @usbpackage          GUI
 * @since               2.4.1
 * @author              Kris <kris@xoofoo.org>
 */
class XoopsGuiZetadigme extends XoopsSystemGui
{
    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public static function validate()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function generateMenu()
    {
        return true;
    }

    public function header()
    {
        parent::header();
        global $xoopsConfig, $xoopsUser, $xoopsModule, $xoTheme, $xoopsDB;
        $tpl =& $this->template;
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('', '', '
        startList = function() {
/* currentStyle restricts the Javascript to IE only */
    if (document.all &&
 document.getElementById(nav).currentStyle) {
        var navroot = document.getElementById(nav);
        /* Get all the list items within the menu */
        var lis=navroot.getElementsByTagName("li");
        for (i=0; i<lis.length; i++) {
           /* If the li has another menu level */
            if (lis[i].lastChild.tagName=="ul") {
                /* assign the function to the li */
                 lis[i].onmouseover=function() {
                   /* display the inner menu */
                   this.lastChild.style.display="block";
                }
                lis[i].onmouseout=function() {
                   this.lastChild.style.display="none";
                }
            }
        }
    }
}
window.onload= function(){
    /* pass the function the id of the top level il */
    /* remove one, when only using one menu */
    activateMenu("nav");
    /*activateMenu("vertnav"); */
}
    xoopsOnloadEvent(startList);');
        $tpl->assign('lang_cp', _CPHOME);
        $tpl->assign('system_options', _AD_SYSOPTIONS);
        $tpl->assign('lang_banners', _AM_SYSTEM_BANS);
        $tpl->assign('lang_blocks', _AM_SYSTEM_BLOCKS);
        $tpl->assign('lang_groups', _AM_SYSTEM_ADGS);
        $tpl->assign('lang_images', _AM_SYSTEM_IMAGES);
        $tpl->assign('lang_modules', _AM_SYSTEM_MODULES);
        $tpl->assign('lang_preferences', _AM_SYSTEM_PREF);
        $tpl->assign('lang_smilies', _AM_SYSTEM_SMLS);
        $tpl->assign('lang_ranks', _AM_SYSTEM_RANK);
        $tpl->assign('lang_edituser', _AM_SYSTEM_USER);
        $tpl->assign('lang_mailuser', _AM_SYSTEM_MLUS);
        $tpl->assign('lang_avatars', _AM_SYSTEM_AVATARS);
        $tpl->assign('lang_tpls', _AM_SYSTEM_TPLSETS);
        $tpl->assign('lang_comments', _AM_SYSTEM_COMMENTS);
        $tpl->assign('lang_insmodules', _AD_INSTALLEDMODULES);
        $tpl->assign('xoops_sitename', $xoopsConfig['sitename']);
        //for system overview
        $tpl->assign('lang_overview', _MD_CPANEL_OVERVIEW);
        $tpl->assign('lang_phpextensions', _MD_CPANEL_PHPEXTENSIONS);
        $tpl->assign('lang_about_xoops', _MD_ABOUT);
        $tpl->assign('lang_about_xoops_text', _MD_ABOUT_TEXT);
        $tpl->assign('lang_version', _MD_VERSION);
        $tpl->assign('lang_version_xoops', _MD_VERSION_XOOPS);
        $tpl->assign('lang_version_php', _MD_VERSION_PHP);
        $tpl->assign('lang_version_mysql', _MD_VERSION_MYSQL);
        $tpl->assign('lang_server_api_name', _MD_Server_API);
        $tpl->assign('lang_os', _MD_OS);
        $tpl->assign('lang_xoops_links', _MD_XOOPS_LINKS);
        //start system overview
        $tpl->assign('lang_xoops_version', XOOPS_VERSION);
        $tpl->assign('lang_php_vesion', PHP_VERSION);
        $tpl->assign('lang_mysql_version', mysqli_get_server_info($xoopsDB->conn));
        $tpl->assign('lang_server_api', PHP_SAPI);
        $tpl->assign('lang_os_name', PHP_OS);
//        $tpl->assign('safe_mode', ini_get('safe_mode') ? 'On' : 'Off');
//        $tpl->assign('register_globals', ini_get('register_globals') ? 'On' : 'Off');
//        $tpl->assign('magic_quotes_gpc', ini_get('magic_quotes_gpc') ? 'On' : 'Off');
        $tpl->assign('allow_url_fopen', ini_get('allow_url_fopen') ? 'On' : 'Off');
        $tpl->assign('fsockopen', function_exists('fsockopen') ? 'On' : 'Off');
//        $tpl->assign('allow_call_time_pass_reference', ini_get('allow_call_time_pass_reference') ? 'On' : 'Off');
        $tpl->assign('post_max_size', ini_get('post_max_size'));
        $tpl->assign('max_input_time', ini_get('max_input_time'));
        $tpl->assign('output_buffering', ini_get('output_buffering'));
        $tpl->assign('max_execution_time', ini_get('max_execution_time'));
        $tpl->assign('memory_limit', ini_get('memory_limit'));
        $tpl->assign('file_uploads', ini_get('file_uploads') ? 'On' : 'Off');
        $tpl->assign('upload_max_filesize', ini_get('upload_max_filesize'));
        $tpl->assign('xoops_sitename', $xoopsConfig['sitename']);
        //for xoops links
        $tpl->assign('lang_xoops_xoopsproject', _MD_XOOPSPROJECT);
        $tpl->assign('lang_xoops_localsupport', _MD_LOCALSUPPORT);
        $tpl->assign('lang_xoops_xoopscore', _MD_XOOPSCORE);
        $tpl->assign('lang_xoops_xoopsthems', _MD_XOOPSTHEME);
        $tpl->assign('lang_xoops_xoopswiki', _MD_XOOPSWIKI);
        $tpl->assign('lang_xoops_codesvn', _MD_CODESVN);
        $tpl->assign('lang_xoops_reportbug', _MD_REPORTBUG);
        $tpl->assign('lang_xoops_movetoblue', _MD_MOVETOBLUE);
        $tpl->assign('lang_xoops_movetobluelink', _MD_MOVETOBLUE_LINK);
        // ADD MENU *****************************************
        //Add  CONTROL PANEL  Menu  items
        $menu                = array();
        $menu[0]['link']     = XOOPS_URL;
        $menu[0]['title']    = _YOURHOME;
        $menu[0]['absolute'] = 1;
        $menu[1]['link']     = XOOPS_URL . '/admin.php?xoopsorgnews=1';
        $menu[1]['title']    = 'XOOPS News';
        $menu[1]['absolute'] = 1;
        $menu[1]['icon']     = XOOPS_ADMINTHEME_URL . '/zetadigme/img/xoops.png';
        $menu[2]['link']     = XOOPS_URL . '/user.php?op=logout';
        $menu[2]['title']    = _LOGOUT;
        $menu[2]['absolute'] = 1;
        $menu[2]['icon']     = XOOPS_ADMINTHEME_URL . '/zetadigme/img/logout.png';
        $tpl->append('navitems', array('link' => XOOPS_URL . '/admin.php', 'text' => _CPHOME, 'menu' => $menu));
        //add SYSTEM  Menu items
        include __DIR__ . '/menu.php';
        $system_options = $adminmenu;
        foreach (array_keys($adminmenu) as $item) {
            $system_options[$item]['link'] = empty($adminmenu[$item]['absolute']) ? XOOPS_URL . '/modules/system/' . $adminmenu[$item]['link'] : $adminmenu[$item]['link'];
            $system_options[$item]['icon'] = empty($adminmenu[$item]['icon_small']) ? '' : XOOPS_ADMINTHEME_URL . '/zetadigme/' . $adminmenu[$item]['icon_small'];
            unset($system_options[$item]['icon_small']);
        }
        $tpl->append('navitems', array('link' => XOOPS_URL . '/modules/system/admin.php', 'text' => _AD_SYSOPTIONS, 'menu' => $system_options));
        if (empty($xoopsModule) || 'system' === $xoopsModule->getVar('dirname', 'n')) {
            $modpath     = XOOPS_URL . '/admin.php';
            $modname     = _AD_SYSOPTIONS;
            $modid       = 1;
            $moddir      = 'system';
            $mod_options = $adminmenu;
            foreach (array_keys($mod_options) as $item) {
                $mod_options[$item]['link'] = empty($mod_options[$item]['absolute']) ? XOOPS_URL . '/modules/' . $moddir . '/' . $mod_options[$item]['link'] : $mod_options[$item]['link'];
                $mod_options[$item]['icon'] = empty($mod_options[$item]['icon']) ? '' : XOOPS_ADMINTHEME_URL . '/zetadigme/' . $mod_options[$item]['icon'];
                unset($mod_options[$item]['icon_small']);
            }
        } else {
            $moddir      = $xoopsModule->getVar('dirname', 'n');
            $modpath     = XOOPS_URL . '/modules/' . $moddir;
            $modname     = $xoopsModule->getVar('name');
            $modid       = $xoopsModule->getVar('mid');
            $mod_options = $xoopsModule->getAdminMenu();
            foreach (array_keys($mod_options) as $item) {
                $mod_options[$item]['link'] = empty($mod_options[$item]['absolute']) ? XOOPS_URL . "/modules/{$moddir}/" . $mod_options[$item]['link'] : $mod_options[$item]['link'];
                //$mod_options[$item]['icon'] = empty($mod_options[$item]['icon']) ? '' : XOOPS_URL . "/modules/{$moddir}/" . $mod_options[$item]['icon'];
                //mb for direct URL access to icons in modules Admin
                $mod_options[$item]['icon'] = empty($mod_options[$item]['icon']) ? '' : (filter_var($mod_options[$item]['icon'], FILTER_VALIDATE_URL) ? $mod_options[$item]['icon'] : (XOOPS_URL . "/modules/{$moddir}/" . $mod_options[$item]['icon']));
            }
        }
        $tpl->assign('mod_options', $mod_options);
        $tpl->assign('modpath', $modpath);
        $tpl->assign('modname', $modname);
        $tpl->assign('modid', $modid);
        $tpl->assign('moddir', $moddir);
        // add MODULES  Menu items
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $criteria       = new CriteriaCompo();
        $criteria->add(new Criteria('hasadmin', 1));
        $criteria->add(new Criteria('isactive', 1));
        $criteria->setSort('mid');
        $mods               = $module_handler->getObjects($criteria);
        $menu               = array();
        /* @var $moduleperm_handler XoopsGroupPermHandler  */
        $moduleperm_handler = xoops_getHandler('groupperm');
        foreach ($mods as $mod) {
            $rtn        = array();
            $modOptions = array();                                                         //add for sub menus
            $sadmin     = $moduleperm_handler->checkRight('module_admin', $mod->getVar('mid'), $xoopsUser->getGroups());
            if ($sadmin) {
                $info = $mod->getInfo();
                if (!empty($info['adminindex'])) {
                    $rtn['link'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['adminindex'];
                } else {
                    $rtn['link'] = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->getVar('mid');
                }
                $rtn['title']    = $mod->name();
                $rtn['absolute'] = 1;
                $rtn['url']      = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/'; //add for sub menus
                $modOptions      = $mod->getAdminMenu();                                        //add for sub menus
                $rtn['options']  = $modOptions;                                             //add for sub menus

                if (isset($info['icon']) && $info['icon'] !== '') {
                    $rtn['icon'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['icon'];
                }
                $menu[] = $rtn;
            }
        }
        $tpl->append('navitems', array(
            'link' => XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin',
            'text' => _AM_SYSTEM_MODULES,
            'dir'  => $mod->getVar('dirname', 'n'),
            'menu' => $menu));

        // add preferences menu
        $menu   = array();
        $OPT    = array();
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=1',
            'title'    => _THEME_GENERAL,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=2',
            'title'    => _THEME_USERSETTINGS,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=3',
            'title'    => _THEME_METAFOOTER,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=4',
            'title'    => _THEME_CENSOR,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=5',
            'title'    => _THEME_SEARCH,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=6',
            'title'    => _THEME_MAILER,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=7',
            'title'    => _THEME_AUTHENTICATION,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $OPT[]  = array(
            'link'     => 'admin.php?fct=preferences&amp;op=showmod&amp;mod=1',
            'title'    => _THEME_MODULESETTINGS,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/prefs_small.png');
        $menu[] = array(
            'link'     => XOOPS_URL . '/modules/system/admin.php?fct=preferences',
            'title'    => _AD_SYSOPTIONS,
            'absolute' => 1,
            'url'      => XOOPS_URL . '/modules/system/',
            'options'  => $OPT);

        foreach ($mods as $mod) {
            $rtn    = array();
            $sadmin = $moduleperm_handler->checkRight('module_admin', $mod->getVar('mid'), $xoopsUser->getGroups());
            if ($sadmin && ($mod->getVar('hasnotification') || is_array($mod->getInfo('config')) || is_array($mod->getInfo('comments')))) {
                $rtn['link']     = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->getVar('mid');
                $rtn['title']    = $mod->name();
                $rtn['absolute'] = 1;
                $menu[]          = $rtn;
            }
            //$menu[] = $rtn;
        }
        $tpl->append('navitems', array(
            'link' => XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin',
            'text' => _THEME_SITEPREF,
            'dir'  => $mod->getVar('dirname', 'n'),
            'menu' => $menu));
        //add OPTIONS/Links Menu Items
        $menu   = array();
        $menu[] = array(
            'link'     => 'http://www.xoops.org',
            'title'    => 'XOOPS',
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/xoops.png');
        $menu[] = array(
            'link'     => 'http://www.xoops.org/modules/library/',
            'title'    => _AD_XOOPSTHEMES,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/tweb.png');
        $menu[] = array(
            'link'     => 'http://www.xoops.org/modules/repository/',
            'title'    => _AD_XOOPSMODULES,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/zetadigme/icons/xoops.png');
        $menu[] = array(
            'link'     => 'http://xoops.org',
            'title'    => 'XOOPS',
            'absolute' => 1);
        $tpl->append('navitems', array('link' => XOOPS_URL . '/admin.php', 'text' => _AD_INTERESTSITES, 'menu' => $menu));
        //add OPTIONS/links for local support
        if (file_exists($file = XOOPS_ADMINTHEME_PATH . '/zetadigme/language/' . $xoopsConfig['language'] . '/localsupport.php')) {
            $links = include XOOPS_ADMINTHEME_PATH . '/zetadigme/language/' . $xoopsConfig['language'] . '/localsupport.php';
            if (count($links) > 0) {
                $tpl->append('navitems', array('link' => XOOPS_URL . '/admin.php', 'text' => _AD_LOCALSUPPORT, 'menu' => $links));
            }
        }
        if (is_object($xoopsModule) || !empty($_GET['xoopsorgnews'])) {
            return null;
        }
        foreach ($mods as $mod) {
            $rtn                = array();
            $moduleperm_handler = xoops_getHandler('groupperm');
            $sadmin             = $moduleperm_handler->checkRight('module_admin', $mod->getVar('mid'), $xoopsUser->getGroups());
            if ($sadmin) {
                $info = $mod->getInfo();
                if (!empty($info['adminindex'])) {
                    $rtn['link'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['adminindex'];
                } else {
                    $rtn['link'] = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->getVar('mid');
                }
                $rtn['title']        = $mod->getVar('name');
                $rtn ['description'] = $mod->getInfo('description');
                $rtn['absolute']     = 1;
                if (isset($info['icon_big'])) {
                    $rtn['icon'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['icon_big'];
                } elseif (isset($info['image'])) {
                    $rtn['icon'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['image'];
                }
            }
            $tpl->append('modules', $rtn);
        }
    }
}
