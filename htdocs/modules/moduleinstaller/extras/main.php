<?php declare(strict_types=1);
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
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

// Check users rights
use Xmf\Request;

if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}

require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
require_once XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php';

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

// Get Action type
$op     = Request::getString('op', 'list');
$module = Request::getString('module', '');

if (in_array($op, ['confirm', 'submit', 'install_ok', 'update_ok', 'uninstall_ok'], true)) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $op = 'list';
    }
}
$myts = \MyTextSanitizer::getInstance();

switch ($op) {
    case 'list':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules.html';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        //        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . $GLOBALS['xoopsModuleConfig']['jquery_theme'] . '/ui.all.css');
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
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $criteria      = new \CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        // Get all installed modules
        $installed_mods = $moduleHandler->getObjects($criteria);
        $listed_mods    = [];
        $i              = 0;
        $install_mods   = [];
        foreach ($installed_mods as $module) {
            $listed_mods[$i]                  = $module->toArray();
            $listed_mods[$i]['image']         = $module->getInfo('image');
            $listed_mods[$i]['adminindex']    = $module->getInfo('adminindex');
            $listed_mods[$i]['version']       = round($module->getVar('version') / 100, 2);
            $listed_mods[$i]['module_status'] = $module->getInfo('module_status');
            $listed_mods[$i]['last_update']   = formatTimestamp($module->getVar('last_update'), 'm');
            $listed_mods[$i]['author']        = $module->getInfo('author');
            $listed_mods[$i]['credits']       = $module->getInfo('credits');
            $listed_mods[$i]['license']       = $module->getInfo('license');
            $listed_mods[$i]['description']   = $module->getInfo('description');
            if ($module->getInfo('version') != $listed_mods[$i]['version']) {
                $listed_mods[$i]['warning_update'] = true;
            } else {
                $listed_mods[$i]['warning_update'] = false;
            }
            $install_mods[] = $module->getInfo('dirname');
            unset($module);
            ++$i;
        }
        // Get module to install
        $dirlist        = \XoopsLists::getModulesList();
        $toinstall_mods = [];
        $i              = 0;
        foreach ($dirlist as $file) {
            if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $file . '/xoops_version.php')) {
                clearstatcache();
                $file = trim((string) $file);
                if (!in_array($file, $install_mods, true)) {
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
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules.html';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        //        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . $GLOBALS['xoopsModuleConfig']['jquery_theme'] . '/ui.all.css');
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
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        // Get all installed modules
        $installed_mods = $moduleHandler->getObjects();
        foreach ($installed_mods as $module) {
            $install_mods[] = $module->getInfo('dirname');
        }
        // Get module to install
        $dirlist        = \XoopsLists::getModulesList();
        $toinstall_mods = [];
        $i              = 0;
        foreach ($dirlist as $file) {
            if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $file . '/xoops_version.php')) {
                clearstatcache();
                $file = trim((string) $file);
                if (!in_array($file, $install_mods, true)) {
                    $module = $moduleHandler->create();
                    $module->loadInfo($file);
                    $toinstall_mods[$i]['name']          = $module->getInfo('name');
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
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        if (Request::hasVar('mod', 'POST')) {
            $i = 1;
            foreach ($_POST['mod'] as $order) {
                if ($order > 0) {
                    $module = $moduleHandler->get($order);
                    //Change order only for visible modules
                    if (0 != $module->getVar('weight')) {
                        $module->setVar('weight', $i);
                        if (!$moduleHandler->insert($module)) {
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
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules_confirm.html';
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
        $error = [];
        if (!is_writable(XOOPS_CACHE_PATH . '/')) {
            // attempt to chmod 666
            if (!chmod(XOOPS_CACHE_PATH . '/', 0777)) {
                $error[] = sprintf(_MUSTWABLE, '<strong>' . XOOPS_CACHE_PATH . '/</strong>');
            }
        }
        if (count($error) > 0) {
            // Display Error
            xoops_error($error);
            // Call Footer
            xoops_cp_footer();
            exit();
        }
        $i           = 0;
        $modifs_mods = [];
        $module      = Request::getArray('module', [], 'POST');
        foreach ($module as $mid) {
            $mid                          = (int)$mid;
            $modifs_mods[$i]['mid']       = $mid;
            $modifs_mods[$i]['oldname']   = htmlspecialchars(((string) $oldname[$mid]), ENT_QUOTES | ENT_HTML5);
            $modifs_mods[$i]['newname']   = htmlspecialchars(trim(((string) $newname[$mid])), ENT_QUOTES | ENT_HTML5);
            $modifs_mods[$i]['newstatus'] = isset($newstatus[$mid]) ? htmlspecialchars((string) $newstatus[$mid], ENT_QUOTES | ENT_HTML5) : 0;
            ++$i;
        }
        $xoopsTpl->assign('modifs_mods', $modifs_mods);
        $xoopsTpl->assign('input_security', $GLOBALS['xoopsSecurity']->getTokenHTML());
        // Call Footer
        xoops_cp_footer();
        break;
    case 'display':
        // Get module handler
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module_id     = Request::getInt('mid', 0, 'POST');
        if ($module_id > 0) {
            $module = $moduleHandler->get($module_id);
            $old    = $module->getVar('isactive');
            // Set value
            $module->setVar('isactive', !$old);
            if (!$moduleHandler->insert($module)) {
                $error = true;
            }
            $blocks = \XoopsBlock::getByModule($module_id);
            $bcount = is_countable($blocks) ? count($blocks) : 0;
            foreach ($blocks as $iValue) {
                $iValue->setVar('isactive', !$old);
                $iValue->store();
            }
            //Set active modules in cache folder
            xoops_setActiveModules();
        }
        break;
    case 'display_in_menu':
        // Get module handler
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module_id     = Request::getInt('mid', 0, 'POST');
        if ($module_id > 0) {
            $module = $moduleHandler->get($module_id);
            $old    = $module->getVar('weight');
            // Set value
            $module->setVar('weight', !$old);
            if (!$moduleHandler->insert($module)) {
                $error = true;
            }
        }
        break;
    case 'submit':
        $ret    = [];
        $write  = false;
        $module = Request::getArray('module', [], 'POST');
        foreach ($module as $mid) {
            if (isset($newstatus[$mid]) && 1 == $newstatus[$mid]) {
                if (0 == $oldstatus[$mid]) {
                    $ret[] = xoops_module_activate($mid);
                }
            } elseif (1 == $oldstatus[$mid]) {
                $ret[] = xoops_module_deactivate($mid);
            }
            $newname[$mid] = trim((string) $newname[$mid]);
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
        $GLOBALS['xoopsOption']['template_main'] = 'system_modules_confirm.html';
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
        $module = htmlspecialchars((string) $module, ENT_QUOTES | ENT_HTML5);
        // Get module handler
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $mod           = $moduleHandler->create();
        $mod->loadInfoAsVar($module);
        // Construct message
        if (false !== $mod->getInfo('image') && '' != trim((string) $mod->getInfo('image'))) {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim((string) $mod->getInfo('image')) . '" alt="">';
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
        xoops_confirm(['module' => $module, 'op' => 'install_ok', 'fct' => 'modulesadmin'], 'admin.php', $msgs, _AM_SYSTEM_MODULES_INSTALL);
        // Call Footer
        xoops_cp_footer();
        break;
    case 'install_ok':
        $ret   = [];
        $ret[] = xoops_module_install($module);
        // Flush cache files for cpanel GUIs
        xoops_load('cpanel', 'system');
        XoopsSystemCpanel::flush();
        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.html';
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
                if ('' != $msg) {
                    echo $msg;
                }
            }
        }
        // Call Footer
        xoops_cp_footer();
        break;
    case 'uninstall':
        $module = htmlspecialchars((string) $module, ENT_QUOTES | ENT_HTML5);
        // Get module handler
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $mod           = $moduleHandler->getByDirname($module);
        // Construct message
        if (false !== $mod->getInfo('image') && '' != trim((string) $mod->getInfo('image'))) {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim((string) $mod->getInfo('image')) . '" alt="">';
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
        xoops_confirm(['module' => $module, 'op' => 'uninstall_ok', 'fct' => 'modulesadmin'], 'admin.php', $msgs, _AM_SYSTEM_MODULES_UNINSTALL);
        // Call Footer
        xoops_cp_footer();
        break;
    case 'uninstall_ok':
        $ret   = [];
        $ret[] = xoops_module_uninstall($module);
        // Flush cache files for cpanel GUIs
        xoops_load('cpanel', 'system');
        XoopsSystemCpanel::flush();
        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.html';
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
                if ('' != $msg) {
                    echo $msg;
                }
            }
        }
        // Call Footer
        xoops_cp_footer();
        break;
    case 'update':
        $module = htmlspecialchars((string) $module, ENT_QUOTES | ENT_HTML5);
        // Get module handler
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $mod           = $moduleHandler->getByDirname($module);
        // Construct message
        if (false !== $mod->getInfo('image') && '' != trim((string) $mod->getInfo('image'))) {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim((string) $mod->getInfo('image')) . '" alt="">';
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
        xoops_confirm(['module' => $module, 'op' => 'update_ok', 'fct' => 'modulesadmin'], 'admin.php', $msgs, _AM_SYSTEM_MODULES_UPDATE);
        // Call Footer
        xoops_cp_footer();
        break;
    case 'update_ok':
        //--------------------------

        $ret   = [];
        $ret[] = xoops_module_update($module);
        // Flush cache files for cpanel GUIs
        xoops_load('cpanel', 'system');
        XoopsSystemCpanel::flush();
        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.html';
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
                if ('' != $msg) {
                    echo $msg;
                }
            }
        }
        // Call Footer
        xoops_cp_footer();
        break;
    //---------------------------------------
}
