<?php
/**
 * Extended User Profile
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
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$path = dirname(dirname(dirname(__DIR__)));
include_once $path . '/mainfile.php';

$dirname         = basename(dirname(__DIR__));
/* @var $module_handler XoopsModuleHandler */
$module_handler  = xoops_getHandler('module');
$module          = $module_handler->getByDirname($dirname);
$pathIcon32      = $module->getInfo('icons32');
$pathModuleAdmin = $module->getInfo('dirmoduleadmin');
$pathLanguage    = $path . $pathModuleAdmin;

if (!file_exists($fileinc = $pathLanguage . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $pathLanguage . '/language/english/main.php';
}

include_once $fileinc;

$adminmenu = array();

$i                      = 1;
$adminmenu[$i]['title'] = _PROFILE_MI_HOME;
$adminmenu[$i]['link']  = 'admin/index.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/home.png';
++$i;
$adminmenu[$i]['title'] = _PROFILE_MI_USERS;
$adminmenu[$i]['link']  = 'admin/user.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/users.png';
++$i;
$adminmenu[$i]['title'] = _PROFILE_MI_CATEGORIES;
$adminmenu[$i]['link']  = 'admin/category.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/category.png';
++$i;
$adminmenu[$i]['title'] = _PROFILE_MI_FIELDS;
$adminmenu[$i]['link']  = 'admin/field.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/index.png';
++$i;
$adminmenu[$i]['title'] = _PROFILE_MI_STEPS;
$adminmenu[$i]['link']  = 'admin/step.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/stats.png';
++$i;
$adminmenu[$i]['title'] = _PROFILE_MI_PERMISSIONS;
$adminmenu[$i]['link']  = 'admin/permissions.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/permissions.png';
++$i;
$adminmenu[$i]['title'] = _PROFILE_MI_ABOUT;
$adminmenu[$i]['link']  = 'admin/about.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/about.png';
