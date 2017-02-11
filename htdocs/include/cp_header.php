<?php
/**
 * XOOPS control panel header
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
 * @package             kernel
 * @since               2.0.0
 */

/**
 * module files can include this file for admin authorization
 * the file that will include this file must be located under xoops_url/modules/module_directory_name/admin_directory_name/
 */
$xoopsOption['pagetype'] = 'admin';
include_once dirname(__DIR__) . '/mainfile.php';
include_once $GLOBALS['xoops']->path('include/cp_functions.php');

/* @var $moduleperm_handler XoopsGroupPermHandler  */
$moduleperm_handler = xoops_getHandler('groupperm');
if ($xoopsUser) {
    $url_arr        = explode('/', strstr($_SERVER['REQUEST_URI'], '/modules/'));
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $xoopsModule    = $module_handler->getByDirname($url_arr[2]);
    unset($url_arr);
    if (!$moduleperm_handler->checkRight('module_admin', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
        redirect_header(XOOPS_URL, 1, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

// set config values for this module
if ($xoopsModule->getVar('hasconfig') == 1 || $xoopsModule->getVar('hascomments') == 1) {
    /* @var $config_handler XoopsConfigHandler  */
    $config_handler    = xoops_getHandler('config');
    $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
}

// include the default language file for the admin interface
if (file_exists($file = $GLOBALS['xoops']->path('modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/admin.php'))) {
    include_once $file;
} elseif (file_exists($file = $GLOBALS['xoops']->path('modules/' . $xoopsModule->getVar('dirname') . '/language/english/admin.php'))) {
    include_once $file;
}
// I will disable this because module developer should nod be forced to have a admin.php
// xoops_loadLanguage('admin', $xoopsModule->getVar('dirname'));

