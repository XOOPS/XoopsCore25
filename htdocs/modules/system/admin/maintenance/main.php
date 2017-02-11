<?php
/**
 * Mail user main page
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Cointin Maxime (AKA Kraven30)
 * @package             system
 */

require_once XOOPS_ROOT_PATH . '/modules/system/class/maintenance.php';

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
//  Check is active
if (!xoops_getModuleOption('active_maintenance', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_maintenance.tpl';
// Call Header
xoops_cp_header();
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
// Define scripts
$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript('modules/system/js/admin.js');
switch ($op) {

    case 'list':
    default:
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MAINTENANCE_NAV_MANAGER, system_adminVersion('maintenance', 'adminpath'));
        $xoBreadCrumb->addHelp(system_adminVersion('maintenance', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_MAINTENANCE_TIPS);
        $xoBreadCrumb->render();

        $maintenance = new SystemMaintenance();

        //Form Maintenance
        $form_maintenance = new XoopsThemeForm(_AM_SYSTEM_MAINTENANCE, 'maintenance_save', 'admin.php?fct=maintenance', 'post', true);

        $cache = new XoopsFormSelect(_AM_SYSTEM_MAINTENANCE_CACHE, 'cache', '', 3, true);
        $cache->setDescription(XOOPS_VAR_PATH . '/cache/smarty_cache/<br>' . XOOPS_VAR_PATH . '/cache/smarty_compile/<br>' . XOOPS_VAR_PATH . '/cache/xoops_cache/');
        $cache_arr = array(
            1 => 'smarty_cache',
            2 => 'smarty_compile',
            3 => 'xoops_cache');
        $cache->addOptionArray($cache_arr);
        $form_maintenance->addElement($cache);

        $form_maintenance->addElement(new XoopsFormRadioYN(_AM_SYSTEM_MAINTENANCE_SESSION, 'session', '', _YES, _NO));

        $tables_tray = new XoopsFormElementTray(_AM_SYSTEM_MAINTENANCE_TABLES, '');
        $tables_tray->setDescription(_AM_SYSTEM_MAINTENANCE_TABLES_DESC);
        $select_tables = new XoopsFormSelect('', 'tables', '', 7, true);
        $select_tables->addOptionArray($maintenance->displayTables(true));
        $tables_tray->addElement($select_tables, false);
        $tables_tray->addElement(new xoopsFormLabel('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _AM_SYSTEM_MAINTENANCE_DUMP_AND . '&nbsp;'));
        $choice  = new XoopsFormSelect('&nbsp;&nbsp;', 'maintenance', '', 4, true);
        $options = array(
            '1' => _AM_SYSTEM_MAINTENANCE_CHOICE1,
            '2' => _AM_SYSTEM_MAINTENANCE_CHOICE2,
            '3' => _AM_SYSTEM_MAINTENANCE_CHOICE3,
            '4' => _AM_SYSTEM_MAINTENANCE_CHOICE4);
        $choice->addOptionArray($options);
        $tables_tray->addElement($choice, false);
        $form_maintenance->addElement($tables_tray);

        $form_maintenance->addElement(new XoopsFormRadioYN(_AM_SYSTEM_MAINTENANCE_AVATAR, 'avatar', '', _YES, _NO));

        $form_maintenance->addElement(new XoopsFormHidden('op', 'maintenance_save'));
        $form_maintenance->addElement(new XoopsFormButton('', 'maintenance_save', _SUBMIT, 'submit'));

        //Form Dump
        $form_dump = new XoopsThemeForm(_AM_SYSTEM_MAINTENANCE_DUMP, 'dump_save', 'admin.php?fct=maintenance', 'post', true);

        $dump_tray      = new XoopsFormElementTray(_AM_SYSTEM_MAINTENANCE_DUMP_TABLES_OR_MODULES, '');
        $select_tables1 = new XoopsFormSelect('', 'dump_tables', '', 7, true);
        $select_tables1->addOptionArray($maintenance->displayTables(true));
        $dump_tray->addElement($select_tables1, false);

        $dump_tray->addElement(new xoopsFormLabel('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _AM_SYSTEM_MAINTENANCE_DUMP_OR . '&nbsp;'));
        $ele            = new XoopsFormSelect('&nbsp;&nbsp;', 'dump_modules', '', 7, true);
    /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $criteria       = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        $moduleslist = $module_handler->getList($criteria, true);
        $ele->addOptionArray($moduleslist);
        $dump_tray->addElement($ele);
        $form_dump->addElement($dump_tray);

        $form_dump->addElement(new XoopsFormRadioYN(_AM_SYSTEM_MAINTENANCE_DUMP_DROP, 'drop', 1, _YES, _NO));

        $form_dump->addElement(new XoopsFormHidden('op', 'dump_save'));
        $form_dump->addElement(new XoopsFormButton('', 'dump_save', _SUBMIT, 'submit'));

        // Assign form
        $xoopsTpl->assign('form_maintenance', $form_maintenance->render());
        $xoopsTpl->assign('form_dump', $form_dump->render());
        break;

    case 'maintenance_save':
        // Check security
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=maintenance', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        //Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MAINTENANCE_NAV_MANAGER, system_adminVersion('maintenance', 'adminpath'));
        //$xoBreadCrumb->addLink(_AM_SYSTEM_MAINTENANCE_NAV_MAINTENANCE);
        $xoBreadCrumb->render();

        $session            = system_CleanVars($_REQUEST, 'session', 1, 'int');
        $cache              = system_CleanVars($_REQUEST, 'cache', array(), 'array');
        $tables             = system_CleanVars($_REQUEST, 'tables', array(), 'array');
        $avatar             = system_CleanVars($_REQUEST, 'avatar', 1, 'int');
        $tables_op          = system_CleanVars($_REQUEST, 'maintenance', array(), 'array');
        $verif_cache        = false;
        $verif_session      = false;
        $verif_avatar       = false;
        $verif_maintenance  = false;
        $result_cache       = false;
        $result_session     = false;
        $result_avatar      = false;
        $result_maintenance = false;

        $maintenance = new SystemMaintenance();
        //Cache
        if (!empty($cache)) {
            $verif_cache = true;
            if ($maintenance->CleanCache($_REQUEST['cache'])) {
                $result_cache = true;
                //Set active modules in cache folder
                xoops_setActiveModules();
            }
        }
        //Session
        if ($session == 1) {
            $verif_session = true;
            if ($maintenance->CleanSession()) {
                $result_session = true;
            }
        }
        //Maintenance tables
        if (!empty($tables)) {
            $verif_maintenance = true;
            if (!empty($tables_op)) {
                $result_maintenance = $maintenance->CheckRepairAnalyzeOptimizeQueries($tables, $tables_op);
            }
        }

        // Purge unused avatars
        if ($avatar == 1) {
            $verif_avatar = true;
            if ($maintenance->CleanAvatar()) {
                $result_avatar = true;
            }
        }

        if ($result_cache === false && $result_session === false && $result_maintenance === false && $result_avatar === false) {
            redirect_header('admin.php?fct=maintenance', 2, _AM_SYSTEM_MAINTENANCE_ERROR_MAINTENANCE);
        }

        $xoopsTpl->assign('verif_cache', $verif_cache);
        $xoopsTpl->assign('verif_session', $verif_session);
        $xoopsTpl->assign('verif_avatar', $verif_avatar);
        $xoopsTpl->assign('verif_maintenance', $verif_maintenance);
        $xoopsTpl->assign('result_cache', $result_cache);
        $xoopsTpl->assign('result_session', $result_session);
        $xoopsTpl->assign('result_avatar', $result_avatar);
        $xoopsTpl->assign('result_maintenance', $result_maintenance);
        $xoopsTpl->assign('maintenance', true);
        break;

    case 'dump_save':
        // Check security
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=maintenance', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        //Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MAINTENANCE_NAV_MANAGER, system_adminVersion('maintenance', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MAINTENANCE_NAV_DUMP);
        $xoBreadCrumb->render();

        $dump         = new SystemMaintenance();
        $dump_modules = isset($_REQUEST['dump_modules']) ? $_REQUEST['dump_modules'] : false;
        $dump_tables  = isset($_REQUEST['dump_tables']) ? $_REQUEST['dump_tables'] : false;
        $drop         = system_CleanVars($_REQUEST, 'drop', 1, 'int');

        if (($dump_tables === true && $dump_modules === true) || ($dump_tables === false && $dump_modules === false)) {
            redirect_header('admin.php?fct=maintenance', 2, _AM_SYSTEM_MAINTENANCE_DUMP_ERROR_TABLES_OR_MODULES);
        }

        if ($dump_tables !== false) {
            $result = $dump->dump_tables($dump_tables, $drop);
        } elseif ($dump_modules !== false) {
            $result = $dump->dump_modules($dump_modules, $drop);
        }
        $xoopsTpl->assign('result_dump', $result[1]);
        break;
}

xoops_cp_footer();
