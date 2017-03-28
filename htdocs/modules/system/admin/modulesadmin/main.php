<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}

include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
include_once XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php';
XoopsLoad::load('XoopsFilterInput');

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

// Get Action type
$op     = system_CleanVars($_REQUEST, 'op', 'list', 'string');
$module = system_CleanVars($_REQUEST, 'module', '', 'string');

if (in_array($op, array('confirm', 'submit', 'install_ok', 'update_ok', 'uninstall_ok'))) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $op = 'list';
    }
}
$myts = MyTextSanitizer::getInstance();

switch ($op) {
    case 'list':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        $xoTheme->addScript('modules/system/js/module.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_MODULES_TIPS);
        $xoBreadCrumb->render();
        // Get Module Handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $criteria       = new CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        // Get all installed modules
        $installed_mods = $module_handler->getObjects($criteria);
        $listed_mods    = array();
        $i              = 0;
        $install_mods   = array();
        foreach ($installed_mods as $module) {
            /* @var $module XoopsModule */
            $listed_mods[$i]                  = $module->toArray();
            $listed_mods[$i]['name']          = htmlspecialchars($module->getVar('name'), ENT_QUOTES);
            $listed_mods[$i]['image']         = $module->getInfo('image');
            $listed_mods[$i]['adminindex']    = $module->getInfo('adminindex');
            $listed_mods[$i]['version']       = round($module->getVar('version') / 100, 2);
            $listed_mods[$i]['module_status'] = $module->getInfo('module_status');
            $listed_mods[$i]['last_update']   = formatTimestamp($module->getVar('last_update'), 'm');
            $listed_mods[$i]['author']        = $module->getInfo('author');
            $listed_mods[$i]['credits']       = $module->getInfo('credits');
            $listed_mods[$i]['license']       = $module->getInfo('license');
            $listed_mods[$i]['description']   = $module->getInfo('description');
            if (round($module->getInfo('version'), 2) != $listed_mods[$i]['version']) {
                $listed_mods[$i]['warning_update'] = true;
            } else {
                $listed_mods[$i]['warning_update'] = false;
            }
            $install_mods[] = $module->getInfo('dirname');
            unset($module);
            ++$i;
        }
        // Get module to install
        $dirlist        = XoopsLists::getModulesList();
        $toinstall_mods = array();
        $i              = 0;
        foreach ($dirlist as $file) {
            if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $file . '/xoops_version.php')) {
                clearstatcache();
                $file = trim($file);
                if (!in_array($file, $install_mods)) {
                    ++$i;
                }
            }
        }
        $xoopsTpl->assign('toinstall_nb', $i);

        $xoopsTpl->assign('install_mods', $listed_mods);
        $xoopsTpl->assign('mods_popup', $listed_mods);

        // Call Footer
        xoops_cp_footer();
        break;

    case 'installlist':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_TOINSTALL);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#install');
        $xoBreadCrumb->addTips(_AM_SYSTEM_MODULES_TIPS);
        $xoBreadCrumb->render();
        // Get Module Handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        // Get all installed modules
        $installed_mods = $module_handler->getObjects();
        foreach ($installed_mods as $module) {
            /* @var $module XoopsModule */
            $install_mods[] = $module->getInfo('dirname');
        }
        // Get module to install
        $dirlist        = XoopsLists::getModulesList();
        $toinstall_mods = array();
        $i              = 0;
        foreach ($dirlist as $file) {
            if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $file . '/xoops_version.php')) {
                clearstatcache();
                $file = trim($file);
                if (!in_array($file, $install_mods)) {
                    $module = $module_handler->create();
                    $module->loadInfo($file);
                    $toinstall_mods[$i]['name']          = htmlspecialchars($module->getInfo('name'), ENT_QUOTES);
                    $toinstall_mods[$i]['dirname']       = $module->getInfo('dirname');
                    $toinstall_mods[$i]['image']         = $module->getInfo('image');
                    $toinstall_mods[$i]['version']       = round($module->getInfo('version'), 2);
                    $toinstall_mods[$i]['module_status'] = $module->getInfo('module_status');
                    $toinstall_mods[$i]['author']        = $module->getInfo('author');
                    $toinstall_mods[$i]['credits']       = $module->getInfo('credits');
                    $toinstall_mods[$i]['license']       = $module->getInfo('license');
                    $toinstall_mods[$i]['description']   = $module->getInfo('description');
                    $toinstall_mods[$i]['mid']           = $i; // Use only for display popup
                    unset($module);
                    ++$i;
                }
            }
        }
        $xoopsTpl->assign('toinstall_mods', $toinstall_mods);
        $xoopsTpl->assign('mods_popup', $toinstall_mods);
        // Call Footer
        xoops_cp_footer();
        //xoops_module_list();
        break;

    case 'order':
        // Get Module Handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        if (isset($_POST['mod'])) {
            $i = 1;
            foreach ($_POST['mod'] as $order) {
                if ($order > 0) {
                    $module = $module_handler->get($order);
                    //Change order only for visible modules
                    if ($module->getVar('weight') != 0) {
                        $module->setVar('weight', $i);
                        if (!$module_handler->insert($module)) {
                            $error = true;
                        }
                        ++$i;
                    }
                }
            }
        }
        exit;
        break;

    case 'confirm':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules_confirm.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_VALIDATE);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#confirm');
        $xoBreadCrumb->addTips(_AM_SYSTEM_MODULES_CONFIRM_TIPS);
        $xoBreadCrumb->render();
        $errorMessage = array();
        if (!is_writable(XOOPS_CACHE_PATH . '/')) {
            $errorMessage[] = sprintf(_MUSTWABLE, '<strong>' . XOOPS_CACHE_PATH . '/</strong>');
        }
        if (count($errorMessage) > 0) {
            // Display Error
            xoops_error($errorMessage);
            // Call Footer
            xoops_cp_footer();
            exit();
        }
        $i           = 0;
        $modifs_mods = array();
        $module      = empty($_POST['module']) ? array() : $_POST['module'];
        foreach ($module as $mid) {
            $mid                          = (int)$mid;
            $newname[$mid]                = trim(XoopsFilterInput::clean($newname[$mid], 'STRING'));
            $modifs_mods[$i]['mid']       = $mid;
            $modifs_mods[$i]['oldname']   = $myts->htmlspecialchars($myts->stripSlashesGPC($oldname[$mid]));
            $modifs_mods[$i]['newname']   = $myts->htmlspecialchars(trim($myts->stripslashesGPC($newname[$mid])));
            $modifs_mods[$i]['newstatus'] = isset($newstatus[$mid]) ? $myts->htmlspecialchars($newstatus[$mid]) : 0;
            ++$i;
        }
        $xoopsTpl->assign('modifs_mods', $modifs_mods);
        $xoopsTpl->assign('input_security', $GLOBALS['xoopsSecurity']->getTokenHTML());
        // Call Footer
        xoops_cp_footer();
        break;

    case 'display':
        // Get module handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $module_id      = system_CleanVars($_POST, 'mid', 0, 'int');
        if ($module_id > 0) {
            /* @var $module XoopsModule */
            $module = $module_handler->get($module_id);
            $old    = $module->getVar('isactive');
            // Set value
            $module->setVar('isactive', !$old);
            if (!$module_handler->insert($module)) {
                $error = true;
            }
            $blocks = XoopsBlock::getByModule($module_id);
            $bcount = count($blocks);
            for ($i = 0; $i < $bcount; ++$i) {
                $blocks[$i]->setVar('isactive', !$old);
                $blocks[$i]->store();
            }
            //Set active modules in cache folder
            xoops_setActiveModules();
        }
        break;

    case 'display_in_menu':
        // Get module handler

        $module_handler = xoops_getHandler('module');
        $module_id      = system_CleanVars($_POST, 'mid', 0, 'int');
        if ($module_id > 0) {
            $module = $module_handler->get($module_id);
            $old    = $module->getVar('weight');
            // Set value
            $module->setVar('weight', !$old);
            if (!$module_handler->insert($module)) {
                $error = true;
            }
        }
        break;

    case 'submit':
        $ret    = array();
        $write  = false;
        $module = empty($_POST['module']) ? array() : $_POST['module'];
        foreach ($module as $mid) {
            if (isset($newstatus[$mid]) && $newstatus[$mid] == 1) {
                if ($oldstatus[$mid] == 0) {
                    $ret[] = xoops_module_activate($mid);
                }
            } else {
                if ($oldstatus[$mid] == 1) {
                    $ret[] = xoops_module_deactivate($mid);
                }
            }
            $newname[$mid] = trim(XoopsFilterInput::clean($newname[$mid], 'STRING'));
            if ($oldname[$mid] != $newname[$mid]) {
                $ret[] = xoops_module_change($mid, $newname[$mid]);
                $write = true;
            }
        }
        if ($write) {
            // Flush cache files for cpanel GUIs
            xoops_load('cpanel', 'system');
            XoopsSystemCpanel::flush();
        }

        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules_confirm.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_SUBMITRES);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#submit');
        $xoBreadCrumb->render();
        if (count($ret) > 0) {
            $xoopsTpl->assign('result', $ret);
        }
        // Call Footer
        xoops_cp_footer();
        break;

    case 'install':
        $module = $myts->htmlspecialchars($module);
        // Get module handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $mod            = $module_handler->create();
        $mod->loadInfoAsVar($module);
        // Construct message
        if ($mod->getInfo('image') !== false && trim($mod->getInfo('image')) != '') {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim($mod->getInfo('image')) . '" alt="" />';
        }
        $msgs .= '<br><span style="font-size:smaller;">' . $mod->getVar('name', 's') . '</span><br><br>' . _AM_SYSTEM_MODULES_RUSUREINS;
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_INSTALL);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#install');
        $xoBreadCrumb->render();
        // Display question message
        xoops_confirm(array('module' => $module, 'op' => 'install_ok', 'fct' => 'modulesadmin'), 'admin.php', $msgs, _AM_SYSTEM_MODULES_INSTALL);
        // Call Footer
        xoops_cp_footer();
        break;

    case 'install_ok':
        $ret   = array();
        $ret[] = xoops_module_install($module);
        // Flush cache files for cpanel GUIs
        xoops_load('cpanel', 'system');
        XoopsSystemCpanel::flush();
        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_INSTALL);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#install');
        $xoBreadCrumb->render();
        if (count($ret) > 0) {
            foreach ($ret as $msg) {
                if ($msg != '') {
                    echo $msg;
                }
            }
        }
        xoops_module_delayed_clean_cache();
        // Call Footer
        xoops_cp_footer();
        break;

    case 'uninstall':
        $module = $myts->htmlspecialchars($module);
        // Get module handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $mod            = $module_handler->getByDirname($module);
        // Construct message
        if ($mod->getInfo('image') !== false && trim($mod->getInfo('image')) != '') {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim($mod->getInfo('image')) . '" alt="" />';
        }
        $msgs .= '<br><span style="font-size:smaller;">' . $mod->getVar('name') . '</span><br><br>' . _AM_SYSTEM_MODULES_RUSUREUNINS;
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_UNINSTALL);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#delete');
        $xoBreadCrumb->render();
        // Display Question
        xoops_confirm(array('module' => $module, 'op' => 'uninstall_ok', 'fct' => 'modulesadmin'), 'admin.php', $msgs, _AM_SYSTEM_MODULES_UNINSTALL);
        // Call Footer
        xoops_cp_footer();
        break;

    case 'uninstall_ok':
        $ret   = array();
        $ret[] = xoops_module_uninstall($module);
        // Flush cache files for cpanel GUIs
        xoops_load('cpanel', 'system');
        XoopsSystemCpanel::flush();
        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_UNINSTALL);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#delete');
        $xoBreadCrumb->render();
        if (count($ret) > 0) {
            foreach ($ret as $msg) {
                if ($msg != '') {
                    echo $msg;
                }
            }
        }
        xoops_module_delayed_clean_cache();
        // Call Footer
        xoops_cp_footer();
        break;

    case 'update':
        $module = $myts->htmlspecialchars($module);
        // Get module handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $mod            = $module_handler->getByDirname($module);
        // Construct message
        if ($mod->getInfo('image') !== false && trim($mod->getInfo('image')) != '') {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim($mod->getInfo('image')) . '" alt="" />';
        }
        $msgs .= '<br><span style="font-size:smaller;">' . $mod->getVar('name', 's') . '</span><br><br>' . _AM_SYSTEM_MODULES_RUSUREUPD;
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_UPDATE);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#update');
        $xoBreadCrumb->render();
        // Display message
        xoops_confirm(array('module' => $module, 'op' => 'update_ok', 'fct' => 'modulesadmin'), 'admin.php', $msgs, _AM_SYSTEM_MODULES_UPDATE);
        // Call Footer
        xoops_cp_footer();
        break;

    case 'update_ok':
        $ret   = array();
        $ret[] = xoops_module_update($module);
        // Flush cache files for cpanel GUIs
        xoops_load('cpanel', 'system');
        XoopsSystemCpanel::flush();
        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_UPDATE);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#update');
        $xoBreadCrumb->render();
        if (count($ret) > 0) {
            foreach ($ret as $msg) {
                if ($msg != '') {
                    echo $msg;
                }
            }
        }
        xoops_module_delayed_clean_cache();
        // Call Footer
        xoops_cp_footer();
        break;
}
