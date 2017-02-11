<?php
/**
 * Private message
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
 * @package             pm
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

//include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include_once XOOPS_ROOT_PATH . '/mainfile.php';

//include_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
include '../../../include/cp_header.php';
//require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/include/functions.php';

if (file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))) {
    include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');
    //return true;
} else {
    redirect_header('../../../admin.php', 5, _AM_MODULEADMIN_MISSING, false);
    //return false;
}

$myts = MyTextSanitizer::getInstance();

$moduleInfo = $module_handler->get($xoopsModule->getVar('mid'));
$pathIcon16 = XOOPS_URL . '/' . $moduleInfo->getInfo('icons16');
$pathIcon32 = XOOPS_URL . '/' . $moduleInfo->getInfo('icons32');

if ($xoopsUser) {
    /* @var $moduleperm_handler XoopsGroupPermHandler  */
    $moduleperm_handler = xoops_getHandler('groupperm');
    if (!$moduleperm_handler->checkRight('module_admin', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
        redirect_header(XOOPS_URL, 1, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    include_once(XOOPS_ROOT_PATH . '/class/template.php');
    $xoopsTpl = new XoopsTpl();
}

$xoopsTpl->assign('pathIcon16', $pathIcon16);

// Load language files
if (!@include_once(XOOPS_TRUST_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/admin.php')) {
    include_once(XOOPS_TRUST_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/english/admin.php');
}
if (!@include_once(XOOPS_TRUST_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
    include_once(XOOPS_TRUST_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/english/modinfo.php');
}
if (!@include_once(XOOPS_TRUST_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/main.php')) {
    include_once(XOOPS_TRUST_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/english/main.php');
}
