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

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$groups = $GLOBALS['xoopsUser']->getGroups();
$all_ok = false;
if (!in_array(XOOPS_GROUP_ADMIN, $groups)) {
    $sysperm_handler = xoops_getHandler('groupperm');
    $ok_syscats      = $sysperm_handler->getItemIds('system_admin', $groups);
} else {
    $all_ok = true;
}
require_once $GLOBALS['xoops']->path('/class/xoopslists.php');
// include system category definitions
include_once $GLOBALS['xoops']->path('/modules/system/constants.php');

$admin_dir = $GLOBALS['xoops']->path('/modules/system/admin');
$dirlist   = XoopsLists::getDirListAsArray($admin_dir);
$index     = 0;
foreach ($dirlist as $file) {
    if (file_exists($admin_dir . '/' . $file . '/xoops_version.php')) {
        include $admin_dir . '/' . $file . '/xoops_version.php';
        if ($modversion['hasAdmin']) {
            if (xoops_getModuleOption('active_' . $file, 'system')) {
                $category = isset($modversion['category']) ? (int)$modversion['category'] : 0;
                if (false != $all_ok || in_array($modversion['category'], $ok_syscats)) {
                    $adminmenu[$index]['title'] = trim($modversion['name']);
                    $adminmenu[$index]['link']  = 'admin.php?fct=' . $file;
                    $adminmenu[$index]['image'] = $modversion['image'];
                }
            }
        }
        unset($modversion);
    }
    ++$index;
}
unset($dirlist);
