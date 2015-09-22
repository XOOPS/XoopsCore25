<?php
// $Id: main.php 13082 2015-06-06 21:59:41Z beckmi $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//          Copyright (c) 2000-2015 XOOPS Project (www.xoops.org)            //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

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
$myts =& MyTextsanitizer::getInstance();

switch ($op) {

    case 'list':
        // Define main template
        $xoopsOption['template_main'] = 'system_modules.html';
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
        $module_handler =& xoops_getHandler('module');
        $criteria       = new CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        // Get all installed modules
        $installed_mods = $module_handler->getObjects($criteria);
        $listed_mods    = array();
        $i              = 0;
        $install_mods   = array();
        foreach ($installed_mods as $module) {
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
        $xoopsOption['template_main'] = 'system_modules.html';
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
        $module_handler =& xoops_getHandler('module');
        // Get all installed modules
        $installed_mods = $module_handler->getObjects();
        foreach ($installed_mods as $module) {
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
                    $module =& $module_handler->create();
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
        $module_handler =& xoops_getHandler('module');
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
        $xoopsOption['template_main'] = 'system_modules_confirm.html';
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
        $error = array();
        if (!is_writable(XOOPS_CACHE_PATH . '/')) {
            // attempt to chmod 666
            if (!chmod(XOOPS_CACHE_PATH . '/', 0777)) {
                $error[] = sprintf(_MUSTWABLE, "<strong>" . XOOPS_CACHE_PATH . '/</strong>');
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
        $modifs_mods = array();
        $module      = empty($_POST['module']) ? array() : $_POST['module'];
        foreach ($module as $mid) {
            $mid                          = (int)$mid;
            $newname[$mid]                = trim(XoopsFilterInput::clean($newname[$mid], 'STRING'));
            $modifs_mods[$i]['mid']       = $mid;
            $modifs_mods[$i]['oldname']   = $myts->htmlspecialchars($myts->stripSlashesGPC($oldname[$mid]));
            $modifs_mods[$i]['newname']   = $myts->htmlspecialchars(trim($myts->stripslashesGPC($newname[$mid])));
            $modifs_mods[$i]['newstatus'] = (isset($newstatus[$mid])) ? $myts->htmlspecialchars($newstatus[$mid]) : 0;
            ++$i;
        }
        $xoopsTpl->assign('modifs_mods', $modifs_mods);
        $xoopsTpl->assign('input_security', $GLOBALS['xoopsSecurity']->getTokenHTML());
        // Call Footer
        xoops_cp_footer();
        break;

    case 'display':
        // Get module handler
        $module_handler =& xoops_getHandler('module');
        $module_id      = system_CleanVars($_POST, 'mid', 0, 'int');
        if ($module_id > 0) {
            $module =& $module_handler->get($module_id);
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
        $module_handler =& xoops_getHandler('module');
        $module_id      = system_CleanVars($_POST, 'mid', 0, 'int');
        if ($module_id > 0) {
            $module =& $module_handler->get($module_id);
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
        $xoopsOption['template_main'] = 'system_modules_confirm.html';
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
        $module_handler =& xoops_getHandler('module');
        $mod            =& $module_handler->create();
        $mod->loadInfoAsVar($module);
        // Construct message
        if ($mod->getInfo('image') != false && trim($mod->getInfo('image')) != '') {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim($mod->getInfo('image')) . '" alt="" />';
        }
        $msgs .= '<br /><span style="font-size:smaller;">' . $mod->getVar('name', 's') . '</span><br /><br />' . _AM_SYSTEM_MODULES_RUSUREINS;
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
        $xoopsOption['template_main'] = 'system_header.html';
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
        // Call Footer
        xoops_cp_footer();
        break;

    case 'uninstall':
        $module = $myts->htmlspecialchars($module);
        // Get module handler
        $module_handler =& xoops_getHandler('module');
        $mod            =& $module_handler->getByDirname($module);
        // Construct message
        if ($mod->getInfo('image') != false && trim($mod->getInfo('image')) != '') {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim($mod->getInfo('image')) . '" alt="" />';
        }
        $msgs .= '<br /><span style="font-size:smaller;">' . $mod->getVar('name') . '</span><br /><br />' . _AM_SYSTEM_MODULES_RUSUREUNINS;
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
        xoops_load("cpanel", "system");
        XoopsSystemCpanel::flush();
        //Set active modules in cache folder
        xoops_setActiveModules();
        // Define main template
        $xoopsOption['template_main'] = 'system_header.html';
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
        // Call Footer
        xoops_cp_footer();
        break;

    case 'update':
        $module = $myts->htmlspecialchars($module);
        // Get module handler
        $module_handler =& xoops_getHandler('module');
        $mod            =& $module_handler->getByDirname($module);
        // Construct message
        if ($mod->getInfo('image') != false && trim($mod->getInfo('image')) != '') {
            $msgs = '<img src="' . XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . trim($mod->getInfo('image')) . '" alt="" />';
        }
        $msgs .= '<br /><span style="font-size:smaller;">' . $mod->getVar('name', 's') . '</span><br /><br />' . _AM_SYSTEM_MODULES_RUSUREUPD;
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
        xoops_confirm(array('dirname' => $module, 'op' => 'update_ok', 'fct' => 'modulesadmin'), 'admin.php', $msgs, _AM_SYSTEM_MODULES_UPDATE);
        // Call Footer
        xoops_cp_footer();
        break;

    case 'update_ok':
        $dirname        = $myts->htmlspecialchars(trim($dirname));
        $module_handler =& xoops_getHandler('module');
        $module         =& $module_handler->getByDirname($dirname);
        // Save current version for use in the update function
        $prev_version = $module->getVar('version');
        $clearTpl     = new XoopsTpl();
        $clearTpl->clearCache($dirname);
        // we don't want to change the module name set by admin
        $temp_name = $module->getVar('name');
        $module->loadInfoAsVar($dirname);
        $module->setVar('name', $temp_name);
        $module->setVar('last_update', time());
        // Call Header
        // Define main template
        $xoopsOption['template_main'] = 'system_header.html';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_UPDATE);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#update');
        $xoBreadCrumb->render();
        if (!$module_handler->insert($module)) {
            echo '<p>Could not update ' . $module->getVar('name') . '</p>';
            echo "<br /><div class='center'><a href='admin.php?fct=modulesadmin'>" . _AM_SYSTEM_MODULES_BTOMADMIN . "</a></div>";
        } else {
            $newmid = $module->getVar('mid');
            $msgs   = array();
            $msgs[] = '<div id="xo-module-log"><div class="header">';
            $msgs[] = $errs[] = '<h4>' . _AM_SYSTEM_MODULES_UPDATING . $module->getInfo('name', 's') . '</h4>';
            if ($module->getInfo('image') != false && trim($module->getInfo('image')) != '') {
                $msgs[] = '<img src="' . XOOPS_URL . '/modules/' . $dirname . '/' . trim($module->getInfo('image')) . '" alt="" />';
            }
            $msgs[] = '<strong>' . _VERSION . ':</strong> ' . $module->getInfo('version') . '&nbsp;' . $module->getInfo('module_status');
            if ($module->getInfo('author') != false && trim($module->getInfo('author')) != '') {
                $msgs[] = '<strong>' . _AUTHOR . ':</strong> ' . $myts->htmlspecialchars(trim($module->getInfo('author')));
            }
            $msgs[]          = '</div><div class="logger">';
            $msgs[]          = _AM_SYSTEM_MODULES_MODULE_DATA_UPDATE;
            $tplfile_handler =& xoops_getHandler('tplfile');
            // irmtfan bug fix: remove codes for delete templates
            /*
            $deltpl          = $tplfile_handler->find('default', 'module', $module->getVar('mid'));
            $delng           = array();
            if (is_array($deltpl)) {
                // delete template file entry in db
                $dcount = count($deltpl);
                for ($i = 0; $i < $dcount; ++$i) {
                    if (!$tplfile_handler->delete($deltpl[$i])) {
                        $delng[] = $deltpl[$i]->getVar('tpl_file');
                    }
                }
            }
            */
            // irmtfan bug fix: remove codes for delete templates
            $templates = $module->getInfo('templates');
            if ($templates != false) {
                $msgs[] = _AM_SYSTEM_MODULES_TEMPLATES_UPDATE;
                foreach ($templates as $tpl) {
                    $tpl['file'] = trim($tpl['file']);
                    // START irmtfan solve templates duplicate issue
                    // if (!in_array($tpl['file'], $delng)) { // irmtfan bug fix: remove codes for delete templates
                    $type = (isset($tpl['type']) ? $tpl['type'] : 'module');
                    if (preg_match("/\.css$/i", $tpl['file'])) {
                        $type = 'css';
                    }
                    $criteria = new CriteriaCompo();
                    $criteria->add(new Criteria("tpl_refid", $newmid), "AND");
                    $criteria->add(new Criteria("tpl_module", $dirname), "AND");
                    $criteria->add(new Criteria("tpl_tplset", 'default'), "AND");
                    $criteria->add(new Criteria("tpl_file", $tpl['file']), "AND");
                    $criteria->add(new Criteria("tpl_type", $type), "AND");
                    $tplfiles = $tplfile_handler->getObjects($criteria);

                    $tpldata =& xoops_module_gettemplate($dirname, $tpl['file'], $type);
                    $tplfile = empty($tplfiles) ? $tplfile_handler->create() : $tplfiles[0];
                    // END irmtfan solve templates duplicate issue
                    $tplfile->setVar('tpl_refid', $newmid);
                    //                        $tplfile->setVar('tpl_lastimported', 0);
                    $tplfile->setVar('tpl_lastmodified', time());
                    $tplfile->setVar('tpl_type', $type);
                    $tplfile->setVar('tpl_source', $tpldata, true);
                    $tplfile->setVar('tpl_module', $dirname);
                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $tpl['file'], true);
                    $tplfile->setVar('tpl_desc', $tpl['description'], true);
                    if (!$tplfile_handler->insert($tplfile)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, "<strong>" . $tpl['file'] . "</strong>") . '</span>';
                    } else {
                        $newid  = $tplfile->getVar('tpl_id');
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_INSERT_DATA, "<strong>" . $tpl['file'] . "</strong>");
                        if ($xoopsConfig['template_set'] === 'default') {
                            if (!xoops_template_touch($newid)) {
                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE_ERROR, "<strong>" . $tpl['file'] . "</strong>") . '</span>';
                            } else {
                                $msgs[] = '&nbsp;&nbsp;<span>' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE, "<strong>" . $tpl['file'] . "</strong>") . '</span>';
                            }
                        }
                    }
                    unset($tpldata);
                    // irmtfan bug fix: remove codes for delete templates
                    /*
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_AM_SYSTEM_MODULES_TEMPLATE_DELETE_OLD_ERROR, "<strong>".$tpl['file']."</strong>").'</span>';
                    }
                    */
                    // irmtfan bug fix: remove codes for delete templates
                }
            }
            $blocks = $module->getInfo('blocks');
            $msgs[] = _AM_SYSTEM_MODULES_BLOCKS_REBUILD;
            if ($blocks != false) {
                $showfuncs = array();
                $funcfiles = array();
                foreach ($blocks as $i => $block) {
                    if (isset($block['show_func']) && $block['show_func'] != '' && isset($block['file']) && $block['file'] != '') {
                        $editfunc    = isset($block['edit_func']) ? $block['edit_func'] : '';
                        $showfuncs[] = $block['show_func'];
                        $funcfiles[] = $block['file'];
                        $template    = '';
                        if ((isset($block['template']) && trim($block['template']) != '')) {
                            $content = xoops_module_gettemplate($dirname, $block['template'], 'blocks');
                        }
                        if (!$content) {
                            $content = '';
                        } else {
                            $template = $block['template'];
                        }
                        $options = '';
                        if (!empty($block['options'])) {
                            $options = $block['options'];
                        }
                        $sql     = "SELECT bid, name FROM " . $xoopsDB->prefix('newblocks') . " WHERE mid=" . $module->getVar('mid') . " AND func_num=" . $i . " AND show_func='" . addslashes($block['show_func']) . "' AND func_file='" . addslashes($block['file']) . "'";
                        $fresult = $xoopsDB->query($sql);
                        $fcount  = 0;
                        while ($fblock = $xoopsDB->fetchArray($fresult)) {
                            ++$fcount;
                            $sql    = "UPDATE " . $xoopsDB->prefix("newblocks") . " SET name='" . addslashes($block['name']) . "', edit_func='" . addslashes($editfunc) . "', content='', template='" . $template . "', last_modified=" . time() . " WHERE bid=" . $fblock['bid'];
                            $result = $xoopsDB->query($sql);
                            if (!$result) {
                                $msgs[] = "&nbsp;&nbsp;" . sprintf(_AM_SYSTEM_MODULES_UPDATE_ERROR, $fblock['name']);
                            } else {
                                $msgs[] = "&nbsp;&nbsp;" . sprintf(_AM_SYSTEM_MODULES_BLOCK_UPDATE, $fblock['name']) . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, "<strong>" . $fblock['bid'] . "</strong>");
                                if ($template != '') {
                                    $tplfile = $tplfile_handler->find('default', 'block', $fblock['bid']);
                                    if (count($tplfile) == 0) {
                                        $tplfile_new =& $tplfile_handler->create();
                                        $tplfile_new->setVar('tpl_module', $dirname);
                                        $tplfile_new->setVar('tpl_refid', $fblock['bid']);
                                        $tplfile_new->setVar('tpl_tplset', 'default');
                                        $tplfile_new->setVar('tpl_file', $block['template'], true);
                                        $tplfile_new->setVar('tpl_type', 'block');
                                    } else {
                                        $tplfile_new = $tplfile[0];
                                    }
                                    $tplfile_new->setVar('tpl_source', $content, true);
                                    $tplfile_new->setVar('tpl_desc', $block['description'], true);
                                    $tplfile_new->setVar('tpl_lastmodified', time());
                                    //                                    $tplfile_new->setVar('tpl_lastimported', 0);
                                    $tplfile_new->setVar('tpl_file', $block['template'], true); // irmtfan bug fix:  block template file will not updated after update the module
                                    if (!$tplfile_handler->insert($tplfile_new)) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_UPDATE_ERROR, "<strong>" . $block['template'] . "</strong>") . '</span>';
                                    } else {
                                        $msgs[] = "&nbsp;&nbsp;" . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_UPDATE, "<strong>" . $block['template'] . "</strong>");
                                        if ($xoopsConfig['template_set'] === 'default') {
                                            if (!xoops_template_touch($tplfile_new->getVar('tpl_id'))) {
                                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE_ERROR, "<strong>" . $block['template'] . "</strong>") . '</span>';
                                            } else {
                                                $msgs[] = "&nbsp;&nbsp;" . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE, "<strong>" . $block['template'] . "</strong>");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($fcount == 0) {
                            $newbid     = $xoopsDB->genId($xoopsDB->prefix('newblocks') . '_bid_seq');
                            $block_name = addslashes($block['name']);
                            $block_type = ($module->getVar('dirname') === 'system') ? 'S' : 'M';
                            $sql        = "INSERT INTO " . $xoopsDB->prefix("newblocks") . " (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, isactive, dirname, func_file, show_func, edit_func, template, last_modified) VALUES (" . $newbid . ", " . $module->getVar('mid') . ", " . $i . ",'" . addslashes($options) . "','" . $block_name . "', '" . $block_name . "', '', 0, 0, 0, '{$block_type}', 1, '" . addslashes($dirname) . "', '" . addslashes($block['file']) . "', '" . addslashes($block['show_func']) . "', '" . addslashes($editfunc) . "', '" . $template . "', " . time() . ")";
                            $result     = $xoopsDB->query($sql);
                            if (!$result) {
                                $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_SQL_NOT_CREATE, $block['name']);
                                echo $sql;
                            } else {
                                if (empty($newbid)) {
                                    $newbid = $xoopsDB->getInsertId();
                                }
                                $groups = array(XOOPS_GROUP_ADMIN);
                                if ($module->getInfo('hasMain')) {
                                    $groups = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
                                }
                                $gperm_handler =& xoops_getHandler('groupperm');
                                foreach ($groups as $mygroup) {
                                    $bperm =& $gperm_handler->create();
                                    $bperm->setVar('gperm_groupid', $mygroup);
                                    $bperm->setVar('gperm_itemid', $newbid);
                                    $bperm->setVar('gperm_name', 'block_read');
                                    $bperm->setVar('gperm_modid', 1);
                                    if (!$gperm_handler->insert($bperm)) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_BLOCK_ACCESS_ERROR . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, "<strong>" . $newbid . "</strong>") . sprintf(_AM_SYSTEM_MODULES_GROUP_ID, "<strong>" . $mygroup . "</strong>") . '</span>';
                                    } else {
                                        $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_BLOCK_ACCESS . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, "<strong>" . $newbid . "</strong>") . sprintf(_AM_SYSTEM_MODULES_GROUP_ID, "<strong>" . $mygroup . "</strong>");
                                    }
                                }

                                if ($template != '') {
                                    $tplfile =& $tplfile_handler->create();
                                    $tplfile->setVar('tpl_module', $dirname);
                                    $tplfile->setVar('tpl_refid', $newbid);
                                    $tplfile->setVar('tpl_source', $content, true);
                                    $tplfile->setVar('tpl_tplset', 'default');
                                    $tplfile->setVar('tpl_file', $block['template'], true);
                                    $tplfile->setVar('tpl_type', 'block');
                                    $tplfile->setVar('tpl_lastimported', time());
                                    $tplfile->setVar('tpl_lastmodified', time());
                                    $tplfile->setVar('tpl_desc', $block['description'], true);
                                    if (!$tplfile_handler->insert($tplfile)) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, "<strong>" . $block['template'] . "</strong>") . '</span>';
                                    } else {
                                        $newid  = $tplfile->getVar('tpl_id');
                                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_DATA, "<strong>" . $block['template'] . "</strong>");
                                        if ($xoopsConfig['template_set'] === 'default') {
                                            if (!xoops_template_touch($newid)) {
                                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE_FAILD, "<strong>" . $block['template'] . "</strong>") . '</span>';
                                            } else {
                                                $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE, "<strong>" . $block['template'] . "</strong>");
                                            }
                                        }
                                    }
                                }
                                $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_CREATED, "<strong>" . $block['name'] . "</strong>") . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, "<strong>" . $newbid . "</strong>");
                                $sql    = 'INSERT INTO ' . $xoopsDB->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newbid . ', -1)';
                                $xoopsDB->query($sql);
                            }
                        }
                    }
                }
                $block_arr = XoopsBlock::getByModule($module->getVar('mid'));
                foreach ($block_arr as $block) {
                    if (!in_array($block->getVar('show_func'), $showfuncs) || !in_array($block->getVar('func_file'), $funcfiles)) {
                        $sql = sprintf("DELETE FROM %s WHERE bid = %u", $xoopsDB->prefix('newblocks'), $block->getVar('bid'));
                        if (!$xoopsDB->query($sql)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DELETE_ERROR, "<strong>" . $block->getVar('name') . "</strong>") . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, "<strong>" . $block->getVar('bid') . "</strong>") . '</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Block <strong>' . $block->getVar('name') . ' deleted. Block ID: <strong>' . $block->getVar('bid') . '</strong>';
                            if ($block->getVar('template') != '') {
                                $tplfiles = $tplfile_handler->find(null, 'block', $block->getVar('bid'));
                                if (is_array($tplfiles)) {
                                    $btcount = count($tplfiles);
                                    for ($k = 0; $k < $btcount; ++$k) {
                                        if (!$tplfile_handler->delete($tplfiles[$k])) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_BLOCK_DEPRECATED_ERROR . '(ID: <strong>' . $tplfiles[$k]->getVar('tpl_id') . '</strong>)</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DEPRECATED, "<strong>" . $tplfiles[$k]->getVar('tpl_file') . "</strong>");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // reset compile_id
            $xoopsTpl->setCompileId();

            // first delete all config entries
            $config_handler =& xoops_getHandler('config');
            $configs        = $config_handler->getConfigs(new Criteria('conf_modid', $module->getVar('mid')));
            $confcount      = count($configs);
            $config_delng   = array();
            if ($confcount > 0) {
                $msgs[] = _AM_SYSTEM_MODULES_MODULE_DATA_DELETE;
                for ($i = 0; $i < $confcount; ++$i) {
                    if (!$config_handler->deleteConfig($configs[$i])) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_GONFIG_DATA_DELETE_ERROR . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, "<strong>" . $configs[$i]->getvar('conf_id') . "</strong>") . '</span>';
                        // save the name of config failed to delete for later use
                        $config_delng[] = $configs[$i]->getvar('conf_name');
                    } else {
                        $config_old[$configs[$i]->getvar('conf_name')]['value']     = $configs[$i]->getvar('conf_value', 'N');
                        $config_old[$configs[$i]->getvar('conf_name')]['formtype']  = $configs[$i]->getvar('conf_formtype');
                        $config_old[$configs[$i]->getvar('conf_name')]['valuetype'] = $configs[$i]->getvar('conf_valuetype');
                        $msgs[]                                                     = "&nbsp;&nbsp;" . _AM_SYSTEM_MODULES_GONFIG_DATA_DELETE . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, "<strong>" . $configs[$i]->getVar('conf_id') . "</strong>");
                    }
                }
            }

            // now reinsert them with the new settings
            $configs = $module->getInfo('config');
            if ($configs != false) {
                if ($module->getVar('hascomments') != 0) {
                    include_once(XOOPS_ROOT_PATH . '/include/comment_constants.php');
                    ($configs[] =  array(
                                           'name'        => 'com_rule',
                                           'title'       => '_CM_COMRULES',
                                           'description' => '',
                                           'formtype'    => 'select',
                                           'valuetype'   => 'int',
                                           'default'     => 1,
                                           'options'     => array(
                                               '_CM_COMNOCOM'        => XOOPS_COMMENT_APPROVENONE,
                                               '_CM_COMAPPROVEALL'   => XOOPS_COMMENT_APPROVEALL,
                                               '_CM_COMAPPROVEUSER'  => XOOPS_COMMENT_APPROVEUSER,
                                               '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)));
                    ($configs[] =  array(
                                           'name'        => 'com_anonpost',
                                           'title'       => '_CM_COMANONPOST',
                                           'description' => '',
                                           'formtype'    => 'yesno',
                                           'valuetype'   => 'int',
                                           'default'     => 0));
                }
            } else {
                if ($module->getVar('hascomments') != 0) {
                    $configs = array();
                    include_once(XOOPS_ROOT_PATH . '/include/comment_constants.php');
                    $configs[] = array(
                        'name'        => 'com_rule',
                        'title'       => '_CM_COMRULES',
                        'description' => '',
                        'formtype'    => 'select',
                        'valuetype'   => 'int',
                        'default'     => 1,
                        'options'     => array(
                            '_CM_COMNOCOM'        => XOOPS_COMMENT_APPROVENONE,
                            '_CM_COMAPPROVEALL'   => XOOPS_COMMENT_APPROVEALL,
                            '_CM_COMAPPROVEUSER'  => XOOPS_COMMENT_APPROVEUSER,
                            '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN));
                    $configs[] = array(
                        'name'        => 'com_anonpost',
                        'title'       => '_CM_COMANONPOST',
                        'description' => '',
                        'formtype'    => 'yesno',
                        'valuetype'   => 'int',
                        'default'     => 0);
                }
            }
            // RMV-NOTIFY
            if ($module->getVar('hasnotification') != 0) {
                if (empty($configs)) {
                    $configs = array();
                }
                // Main notification options
                include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
                $options                             = array();
                $options['_NOT_CONFIG_DISABLE']      = XOOPS_NOTIFICATION_DISABLE;
                $options['_NOT_CONFIG_ENABLEBLOCK']  = XOOPS_NOTIFICATION_ENABLEBLOCK;
                $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
                $options['_NOT_CONFIG_ENABLEBOTH']   = XOOPS_NOTIFICATION_ENABLEBOTH;

                //$configs[] = array ('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLED', 'description' => '_NOT_CONFIG_ENABLEDDSC', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
                $configs[] = array(
                    'name'        => 'notification_enabled',
                    'title'       => '_NOT_CONFIG_ENABLE',
                    'description' => '_NOT_CONFIG_ENABLEDSC',
                    'formtype'    => 'select',
                    'valuetype'   => 'int',
                    'default'     => XOOPS_NOTIFICATION_ENABLEBOTH,
                    'options'     => $options);
                // Event specific notification options
                // FIXME: for some reason the default doesn't come up properly
                //  initially is ok, but not when 'update' module..
                $options    = array();
                $categories =& notificationCategoryInfo('', $module->getVar('mid'));
                foreach ($categories as $category) {
                    $events =& notificationEvents($category['name'], false, $module->getVar('mid'));
                    foreach ($events as $event) {
                        if (!empty($event['invisible'])) {
                            continue;
                        }
                        $option_name           = $category['title'] . ' : ' . $event['title'];
                        $option_value          = $category['name'] . '-' . $event['name'];
                        $options[$option_name] = $option_value;
                        //$configs[] = array ('name' => notificationGenerateConfig($category,$event,'name'), 'title' => notificationGenerateConfig($category,$event,'title_constant'), 'description' => notificationGenerateConfig($category,$event,'description_constant'), 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
                    }
                }
                $configs[] = array(
                    'name'        => 'notification_events',
                    'title'       => '_NOT_CONFIG_EVENTS',
                    'description' => '_NOT_CONFIG_EVENTSDSC',
                    'formtype'    => 'select_multi',
                    'valuetype'   => 'array',
                    'default'     => array_values($options),
                    'options'     => $options);
            }

            if ($configs != false) {
                $msgs[]         = 'Adding module config data...';
                $config_handler =& xoops_getHandler('config');
                $order          = 0;
                foreach ($configs as $config) {
                    // only insert ones that have been deleted previously with success
                    if (!in_array($config['name'], $config_delng)) {
                        $confobj =& $config_handler->createConfig();
                        $confobj->setVar('conf_modid', $newmid);
                        $confobj->setVar('conf_catid', 0);
                        $confobj->setVar('conf_name', $config['name']);
                        $confobj->setVar('conf_title', $config['title'], true);
                        $confobj->setVar('conf_desc', $config['description'], true);
                        $confobj->setVar('conf_formtype', $config['formtype']);
                        if (isset($config['valuetype'])) {
                            $confobj->setVar('conf_valuetype', $config['valuetype']);
                        }
                        if (isset($config_old[$config['name']]['value']) && $config_old[$config['name']]['formtype'] == $config['formtype'] && $config_old[$config['name']]['valuetype'] == $config['valuetype']) {
                            // preserver the old value if any
                            // form type and value type must be the same
                            $confobj->setVar('conf_value', $config_old[$config['name']]['value'], true);
                        } else {
                            $confobj->setConfValueForInput($config['default'], true);

                            //$confobj->setVar('conf_value', $config['default'], true);
                        }
                        $confobj->setVar('conf_order', $order);
                        $confop_msgs = '';
                        if (isset($config['options']) && is_array($config['options'])) {
                            foreach ($config['options'] as $key => $value) {
                                $confop =& $config_handler->createConfigOption();
                                $confop->setVar('confop_name', $key, true);
                                $confop->setVar('confop_value', $value, true);
                                $confobj->setConfOptions($confop);
                                $confop_msgs .= '<br />&nbsp;&nbsp;&nbsp;&nbsp; ' . _AM_SYSTEM_MODULES_CONFIG_ADD . _AM_SYSTEM_MODULES_NAME . ' <strong>' . (defined($key) ? constant($key) : $key) . '</strong> ' . _AM_SYSTEM_MODULES_VALUE . ' <strong>' . $value . '</strong> ';
                                unset($confop);
                            }
                        }
                        ++$order;
                        if (false != $config_handler->insertConfig($confobj)) {
                            //$msgs[] = '&nbsp;&nbsp;Config <strong>'.$config['name'].'</strong> added to the database.'.$confop_msgs;
                            $msgs[] = "&nbsp;&nbsp;" . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD, "<strong>" . $config['name'] . "</strong>") . $confop_msgs;
                        } else {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD_ERROR, "<strong>" . $config['name'] . "</strong>") . '</span>';
                        }
                        unset($confobj);
                    }
                }
                unset($configs);
            }

            // execute module specific update script if any
            $update_script = $module->getInfo('onUpdate');
            if (false != $update_script && trim($update_script) != '') {
                include_once XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . trim($update_script);
                if (function_exists('xoops_module_update_' . $dirname)) {
                    $func = 'xoops_module_update_' . $dirname;
                    if (!$func($module, $prev_version)) {
                        $msgs[] = "<p>" . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . "</p>";
                        $msgs   = array_merge($msgs, $module->getErrors());
                    } else {
                        $msgs[] = "<p>" . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, "<strong>" . $func . "</strong>") . "</p>";
                        $msgs += $module->getErrors();
                    }
                }
            }
            $msgs[] = sprintf(_AM_SYSTEM_MODULES_OKUPD, '<strong>' . $module->getVar('name', 's') . '</strong>');
            $msgs[] = '</div></div>';
            $msgs[] = '<div class="center"><a href="admin.php?fct=modulesadmin">' . _AM_SYSTEM_MODULES_BTOMADMIN . '</a>  | <a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/' . $module->getInfo('adminindex') . '">' . _AM_SYSTEM_MODULES_ADMIN . '</a></div>';
            foreach ($msgs as $msg) {
                echo $msg . '<br />';
            }
        }
        // Call Footer
        xoops_cp_footer();
        // Flush cache files for cpanel GUIs
        xoops_load("cpanel", "system");
        XoopsSystemCpanel::flush();

        require_once XOOPS_ROOT_PATH . '/modules/system/class/maintenance.php';
        $maintenance = new SystemMaintenance();
        $folder      = array(1, 3);
        $maintenance->CleanCache($folder);
        //Set active modules in cache folder
        xoops_setActiveModules();
        break;
}
