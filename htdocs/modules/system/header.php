<?php
/**
 * System header file
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
 */
/* @var  $xoopsUser XoopsUser */

// Include XOOPS control panel header
include_once dirname(dirname(__DIR__)) . '/include/cp_header.php';
// Check user rights
if (is_object($xoopsUser)) {
    $admintest   = 0;
    $xoopsModule = XoopsModule::getByDirname('system');
    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header(XOOPS_URL, 3, _NOPERM);
    }
    $admintest = 1;
} else {
    redirect_header(XOOPS_URL, 3, _NOPERM);
}
// XOOPS Class
include_once $GLOBALS['xoops']->path('/class/pagenav.php');
include_once $GLOBALS['xoops']->path('/class/template.php');
include_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');
include_once $GLOBALS['xoops']->path('/class/xoopslists.php');
// System Class
include_once $GLOBALS['xoops']->path('/modules/system/class/breadcrumb.php');
//include_once $GLOBALS['xoops']->path('/modules/system/class/cookie.php');
// Load Language
xoops_loadLanguage('admin', 'system');
// Include System files
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
// include system category definitions
include_once $GLOBALS['xoops']->path('/modules/system/constants.php');
// Get request variable
$fct = system_CleanVars($_REQUEST, 'fct', '', 'string');

$xoBreadCrumb = new SystemBreadcrumb($fct);
$xoBreadCrumb->addLink(_AM_SYSTEM_CPANEL, XOOPS_URL . '/admin.php', true);
