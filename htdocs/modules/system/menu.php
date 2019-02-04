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

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$groups = $GLOBALS['xoopsUser']->getGroups();
$all_ok = false;
if (!in_array(XOOPS_GROUP_ADMIN, $groups)) {
    /* @var XoopsGroupPermHandler $sysperm_handler */
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
                if (false !== $all_ok || in_array($modversion['category'], $ok_syscats)) {
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
