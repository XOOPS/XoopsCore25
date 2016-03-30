<?php
// 
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//          Copyright (c) 2000-2016 XOOPS Project (www.xoops.org)            //
//                         <http://xoops.org/>                               //
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

// Include header
include __DIR__ . '/header.php';

if (isset($fct) && $fct === 'users') {
    $xoopsOption['pagetype'] = 'user';
}

$error = false;
if ($admintest != 0) {
    if (isset($fct) && $fct !== '') {
        $fct = preg_replace("/[^a-z0-9_\-]/i", '', $fct);
        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/admin/' . $fct . '/xoops_version.php')) {
            // Load language file
            system_loadLanguage($fct, $xoopsModule->getVar('dirname', 'n'));
            // Include Configuration file
            require XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/admin/' . $fct . '/xoops_version.php';
            // Get System permission handler
            $sysperm_handler = xoops_getHandler('groupperm');

            $category = !empty($modversion['category']) ? (int)$modversion['category'] : 0;
            unset($modversion);

            if ($category > 0) {
                $group = $xoopsUser->getGroups();
                if (in_array(XOOPS_GROUP_ADMIN, $group) || false != $sysperm_handler->checkRight('system_admin', $category, $group, $xoopsModule->getVar('mid'))) {
                    if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/admin/' . $fct . '/main.php')) {
                        include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/admin/' . $fct . '/main.php';
                    } else {
                        $error = true;
                    }
                } else {
                    $error = true;
                }
            } elseif ($fct === 'version') {
                if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/admin/version/main.php')) {
                    include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/admin/version/main.php';
                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}

if (false != $error) {
    $op = system_CleanVars($_REQUEST, 'op', '', 'string');
    if ($op === 'system_activate') {
        $part           = system_CleanVars($_REQUEST, 'type', '', 'string');
        $config_handler = xoops_getHandler('config');

        $criteria = new Criteria('conf_name', 'active_' . $part);
        $configs  = $config_handler->getConfigs($criteria);
        foreach ($configs as $conf) {
            if ($conf->getVar('conf_name') === 'active_' . $part) {
                $conf->setVar('conf_value', !$conf->getVar('conf_value'));
                $config_handler->insertConfig($conf);
            }
        }
        exit;
    }
    // Define main template
    $xoopsOption['template_main'] = 'system_index.tpl';
    xoops_cp_header();
    // Define Stylesheet
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
    // Define scripts
    $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
    $xoTheme->addScript('modules/system/js/admin.js');
    // Define Breadcrumb and tips
    $xoBreadCrumb->addLink(_AM_SYSTEM_CONFIG);
    $xoBreadCrumb->addTips(_AM_SYSTEM_TIPS_MAIN);
    $xoBreadCrumb->render();
    $groups = $xoopsUser->getGroups();
    $all_ok = false;
    if (!in_array(XOOPS_GROUP_ADMIN, $groups)) {
        $sysperm_handler = xoops_getHandler('groupperm');
        $ok_syscats      = $sysperm_handler->getItemIds('system_admin', $groups);
    } else {
        $all_ok = true;
    }

    xoops_load('xoopslists');

    $admin_dir        = XOOPS_ROOT_PATH . '/modules/system/admin';
    $dirlist          = XoopsLists::getDirListAsArray($admin_dir);
    $inactive_section = array('blocksadmin', 'groups', 'modulesadmin', 'preferences', 'tplsets');
    foreach ($dirlist as $directory) {
        if (file_exists($admin_dir . '/' . $directory . '/xoops_version.php')) {
            require $admin_dir . '/' . $directory . '/xoops_version.php';

            if ($modversion['hasAdmin']) {
                if (xoops_getModuleOption('active_' . $directory, 'system')) {
                    $category = isset($modversion['category']) ? (int)$modversion['category'] : 0;
                    if (false != $all_ok || in_array($modversion['category'], $ok_syscats)) {
                        $menu['file']   = $directory;
                        $menu['title']  = trim($modversion['name']);
                        $menu['desc']   = str_replace('<br />', ' ', $modversion['description']);
                        $menu['icon']   = $modversion['image'];
                        $menu['status'] = true;
                    }
                } else {
                    $category = isset($modversion['category']) ? (int)$modversion['category'] : 0;
                    if (false != $all_ok || in_array($modversion['category'], $ok_syscats)) {
                        $menu['file']   = $directory;
                        $menu['title']  = trim($modversion['name']);
                        $menu['desc']   = str_replace('<br />', ' ', $modversion['description']);
                        $menu['icon']   = $modversion['image'];
                        $menu['status'] = false;
                    }
                }
                if (!in_array($directory, $inactive_section)) {
                    $menu['used'] = true;
                }
                if (false != $all_ok || in_array($modversion['category'], $ok_syscats)) {
                    switch ($directory) {
                        case 'avatars':
                            $avatar_handler = xoops_getHandler('avatar');
                            $avatar         = $avatar_handler->getCount();
                            $menu['infos']  = sprintf(_AM_SYSTEM_AVATAR_INFO, $avatar);
                            break;
                        case 'banners':
                            $banner_handler = xoops_getModuleHandler('banner', 'system');
                            $banner         = $banner_handler->getCount();
                            $menu['infos']  = sprintf(_AM_SYSTEM_BANNER_INFO, $banner);
                            break;
                        case 'comments':
                            $comment_handler = xoops_getHandler('comment');
                            $comment         = $comment_handler->getCount();
                            $menu['infos']   = sprintf(_AM_SYSTEM_COMMENT_INFO, $comment);
                            break;
                        case 'groups':
                            $groups_Handler = xoops_getModuleHandler('group', 'system');
                            $groups         = $groups_Handler->getCount();
                            $menu['infos']  = sprintf(_AM_SYSTEM_GROUP_INFO, $groups);
                            break;
                        case 'images':
                            $imgcat_handler = xoops_getHandler('image');
                            $img            = $imgcat_handler->getCount();
                            $menu['infos']  = sprintf(_AM_SYSTEM_IMG_INFO, $img);
                            break;
                        case 'smilies':
                            $smilies_Handler = xoops_getModuleHandler('smilies', 'system');
                            $smilies         = $smilies_Handler->getCount();
                            $menu['infos']   = sprintf(_AM_SYSTEM_SMILIES_INFO, $smilies);
                            break;
                        case 'userrank':
                            $userrank_Handler = xoops_getModuleHandler('userrank', 'system');
                            $userrank         = $userrank_Handler->getCount();
                            $menu['infos']    = sprintf(_AM_SYSTEM_RANKS_INFO, $userrank);
                            break;
                        case 'users':
                            $member_handler = xoops_getModuleHandler('users', 'system');
                            $member         = $member_handler->getCount();
                            $menu['infos']  = sprintf(_AM_SYSTEM_USERS_INFO, $member);
                            break;
                    }
                }
                $xoopsTpl->append_by_ref('menu', $menu);
                unset($menu);
            }
            unset($modversion);
        }
    }
    unset($dirlist);
    xoops_cp_footer();
}
