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
 * @copyright      {@link http://xoops.org/ XOOPS Project}
 * @license        {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author         XOOPS Development Team
 */

include dirname(dirname(dirname(dirname(__DIR__)))) . '/mainfile.php';
include $GLOBALS['xoops']->path('/include/cp_functions.php');
if (is_object($xoopsUser)) {
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $xoopsModule    = $module_handler->getByDirname('system');
    if (!in_array(XOOPS_GROUP_ADMIN, $xoopsUser->getGroups())) {
        include_once $GLOBALS['xoops']->path('modules/system/constants.php');
        /* @var $sysperm_handler XoopsGroupPermHandler  */
        $sysperm_handler = xoops_getHandler('groupperm');
        if (!$sysperm_handler->checkRight('system_admin', XOOPS_SYSTEM_COMMENT, $xoopsUser->getGroups())) {
            redirect_header(XOOPS_URL . '/', 3, _NOPERM);
        }
    }
} else {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
}
