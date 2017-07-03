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

/*
 * Xoops Cpanel default GUI class
 *
 * @copyright   (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license     GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package     system
 * @usbpackage  GUI
 * @since       2.4
 * @author      Mamba       <mambax7@gmail.com>
 * @author      Mojtabajml  <jamali.mojtaba@gmail.com>
 * @author      Voltan      <djvoltan@gmail.com>
 * @author      BitC3R0     <BitC3R0@gmail.com>
 * @author      trabis      <lusopoemas@gmail.com>
 */

/**
 * Class XoopsGuiTransition
 */
class XoopsGuiTransition extends XoopsSystemGui
{
    /**
     *
     */
    /*
    public function __construct()
    {
        // Check cookie
        $used = isset($_COOKIE['transition_theme']) ? $_COOKIE['transition_theme'] : 0;

        if(0 == $used){

            setcookie('transition_theme', 1, time() + (86400*365), '/', null, null, true);

            header('location: ' . XOOPS_URL . '/admin.php?show=info');
            die();
        }
    }
    */
    /**
     * @return bool
     */
    public static function validate()
    {
        return true;
    }

    public function header()
    {
        parent::header();

        global $xoopsConfig, $xoopsUser, $xoopsModule, $xoTheme, $xoopsTpl, $xoopsDB;
        $tpl =& $this->template;

        // Determine if information box must be shown
        $currentScript = str_replace(XOOPS_ROOT_PATH . '/', '', $_SERVER['SCRIPT_FILENAME']);

        if('admin.php' == $currentScript){
            $show = isset($_GET['show']) ? $_GET['show'] : '';
            if('info' == $show){
                $tpl->assign('showTransitionInfo', true);
            }
        }

        $iconsSet = xoops_getModuleOption('typeicons', 'system');

        if ($iconsSet == '') {
            $icons = 'transition';
        }

        $tpl->assign('theme_icons', XOOPS_URL . '/modules/system/images/icons/' . $iconsSet);

        // language
        $tpl->assign('xoops_language', $xoopsConfig['language']);

        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/transition/js/styleswitch.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/transition/js/formenu.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/transition/js/menu.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/transition/js/tooltip.js');
//        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/transition/js/tabs.jquery.tools.min.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/transition/js/tabs.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/transition/js/tabs.slideshow.js');

        $xoTheme->addStylesheet('https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:300,300i,400,400i,700,700i');
//        $xoTheme->addStylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/transition/css/style.css');
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/transition/css/dark.css', array('title' => 'dark', 'media' => 'screen'));
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/transition/css/silver.css', array('title' => 'silver', 'media' => 'screen'));
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/transition/css/orange.css', array('title' => 'orange', 'media' => 'screen'));

        $tpl->assign('lang_cp', _CPHOME);
        //start system overview
        //$tpl->assign('lang_xoops_version', XOOPS_VERSION);
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

        // ADD MENU *****************************************

        //Add  CONTROL PANEL  Menu  items
        $menu                = array();
        $menu[0]['link']     = XOOPS_URL;
        $menu[0]['title']    = "<span class='fa fa-home'></span> " . _YOURHOME;
        $menu[0]['absolute'] = 1;
        $menu[1]['link']     = XOOPS_URL . '/admin.php?xoopsorgnews=1';
        $menu[1]['title']    = "<span class='fa fa-newspaper-o'></span> " . _OXYGEN_NEWS;
        $menu[1]['absolute'] = 1;
        $menu[1]['icon']     = XOOPS_ADMINTHEME_URL . '/transition/images/xoops.png';
        $menu[2]['link']     = XOOPS_URL . '/user.php?op=logout';
        $menu[2]['title']    = "<span class='fa fa-sign-out'></span> " . _LOGOUT;
        $menu[2]['absolute'] = 1;
        $menu[2]['icon']     = XOOPS_ADMINTHEME_URL . '/transition/images/logout.png';
        $tpl->append('navitems', array('link' => XOOPS_URL . '/admin.php', 'text' => '<span class="fa fa-cog"></span> ' . _CPHOME, 'menu' => $menu));

        //add SYSTEM  Menu items
        include __DIR__ . '/menu.php';
        if (empty($xoopsModule) || 'system' === $xoopsModule->getVar('dirname', 'n')) {
            $modpath = XOOPS_URL . '/admin.php';
            $modname = _OXYGEN_SYSOPTIONS;
            $modid   = 1;
            $moddir  = 'system';

            $mod_options = $adminmenu;
            foreach (array_keys($mod_options) as $item) {
                $mod_options[$item]['link'] = empty($mod_options[$item]['absolute']) ? XOOPS_URL . '/modules/' . $moddir . '/' . $mod_options[$item]['link'] : $mod_options[$item]['link'];
                $mod_options[$item]['icon'] = empty($mod_options[$item]['icon']) ? '' : XOOPS_URL . '/modules/system/images/' . $mod_options[$item]['icon'];
                unset($mod_options[$item]['icon_small']);
            }
        } else {
            $moddir  = $xoopsModule->getVar('dirname', 'n');
            $modpath = XOOPS_URL . '/modules/' . $moddir;
            $modname = $xoopsModule->getVar('name');
            $modid   = $xoopsModule->getVar('mid');

            $mod_options = $xoopsModule->getAdminMenu();
            foreach (array_keys($mod_options) as $item) {
                $mod_options[$item]['link'] = empty($mod_options[$item]['absolute']) ? XOOPS_URL . "/modules/{$moddir}/" . $mod_options[$item]['link'] : $mod_options[$item]['link'];
                //                $mod_options[$item]['icon'] = empty($mod_options[$item]['icon']) ? '' : XOOPS_URL . "/modules/{$moddir}/" . $mod_options[$item]['icon'];
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
        $mods = $module_handler->getObjects($criteria);

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
                $rtn['title']    = htmlspecialchars($mod->name(), ENT_QUOTES);
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
            'text' => '<span class="fa fa-puzzle-piece"></span> ' . _AM_SYSTEM_MODULES,
            'dir'  => $mod->getVar('dirname', 'n'),
            'menu' => $menu));

        // add preferences menu
        $menu = array();

        $OPT   = array();
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=1',
            'title'    => _OXYGEN_GENERAL,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=2',
            'title'    => _OXYGEN_USERSETTINGS,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=3',
            'title'    => _OXYGEN_METAFOOTER,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=4',
            'title'    => _OXYGEN_CENSOR,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=5',
            'title'    => _OXYGEN_SEARCH,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=6',
            'title'    => _OXYGEN_MAILER,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=show&amp;confcat_id=7',
            'title'    => _OXYGEN_AUTHENTICATION,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');
        $OPT[] = array(
            'link'     => 'admin.php?fct=preferences&amp;op=showmod&amp;mod=1',
            'title'    => _OXYGEN_MODULESETTINGS,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/icons/prefs_small.png');

        $menu[] = array(
            'link'     => XOOPS_URL . '/modules/system/admin.php?fct=preferences',
            'title'    => _OXYGEN_SYSOPTIONS,
            'absolute' => 1,
            'url'      => XOOPS_URL . '/modules/system/',
            'options'  => $OPT);

        foreach ($mods as $mod) {
            $rtn    = array();
            $sadmin = $moduleperm_handler->checkRight('module_admin', $mod->getVar('mid'), $xoopsUser->getGroups());
            if ($sadmin && ($mod->getVar('hasnotification') || is_array($mod->getInfo('config')) || is_array($mod->getInfo('comments')))) {
                $rtn['link']     = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->getVar('mid');
                $rtn['title']    = htmlspecialchars($mod->name(), ENT_QUOTES);
                $rtn['absolute'] = 1;
                $rtn['icon']     = XOOPS_ADMINTHEME_URL . '/gui/oxygen/icons/prefs_small.png';
                $menu[]          = $rtn;
            }
        }
        $tpl->append('navitems', array(
            'link' => XOOPS_URL . '/modules/system/admin.php?fct=preferences',
            'text' => '<span class="fa fa-wrench"></span> ' . _OXYGEN_SITEPREF,
            'dir'  => $mod->getVar('dirname', 'n'),
            'menu' => $menu));

        //add OPTIONS/Links Menu Items
        $menu   = array();
        $menu[] = array(
            'link'     => 'http://xoops.org',
            'title'    => _OXYGEN_XOOPSPROJECT,
            'absolute' => 1);
        $menu[] = array(
            'link'     => 'http://xoops.org',
            'title'    => _OXYGEN_WEBSITE,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/images/xoops.png');
        $menu[] = array(
            'link'     => 'http://www.xoops.org/modules/repository/',
            'title'    => _OXYGEN_XOOPSMODULES,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/images/xoops.png');
        $menu[] = array(
            'link'     => 'http://www.xoops.org/modules/extgallery/',
            'title'    => _OXYGEN_XOOPSTHEMES,
            'absolute' => 1,
            'icon'     => XOOPS_ADMINTHEME_URL . '/transition/images/tweb.png');

        $tpl->append('navitems', array('link' => XOOPS_URL . '/admin.php', 'text' => '<span class="fa fa-link"></span> ' . _OXYGEN_INTERESTSITES, 'menu' => $menu));

        //add OPTIONS/links for local support
        if (file_exists($file = XOOPS_ADMINTHEME_PATH . '/transition/language/' . $xoopsConfig['language'] . '/localsupport.php')) {
            $links = include XOOPS_ADMINTHEME_PATH . '/transition/language/' . $xoopsConfig['language'] . '/localsupport.php';
            if (count($links) > 0) {
                $tpl->append('navitems', array('link' => XOOPS_URL . '/admin.php', 'text' => '<span class="fa fa-link"></span> ' . _OXYGEN_LOCALSUPPORT, 'menu' => $links));
            }
        }

        if (is_object($xoopsModule) || !empty($_GET['xoopsorgnews'])) {
            if (is_object($xoopsModule) && file_exists($file = XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/' . $xoopsModule->getInfo('adminmenu'))) {
                include $file;
            }

            return null;
        }

        foreach ($mods as $mod) {
            $sadmin = $moduleperm_handler->checkRight('module_admin', $mod->getVar('mid'), $xoopsUser->getGroups());
            if ($sadmin) {
                $rtn  = array();
                $info = $mod->getInfo();
                if (!empty($info ['adminindex'])) {
                    $rtn ['link'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info ['adminindex'];
                } else {
                    $rtn ['link'] = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->getVar('mid');
                }
                $rtn ['title']       = htmlspecialchars($mod->getVar('name'), ENT_QUOTES);
                $rtn ['description'] = $mod->getInfo('description');
                $rtn ['absolute']    = 1;
                if (isset($info ['icon_big'])) {
                    $rtn ['icon'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info ['icon_big'];
                } elseif (isset($info ['image'])) {
                    $rtn ['icon'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info ['image'];
                }

                $tpl->append('modules', $rtn);
            }
        }
    }
}
