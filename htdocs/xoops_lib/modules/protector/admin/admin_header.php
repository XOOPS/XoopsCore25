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
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             pm
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

use Xmf\Module\Admin;
use XoopsModules\Protector;

CONST MODULES = '/modules/';
CONST LANGUAGE = '/language/';

require_once dirname(__DIR__) . '/preloads/autoloader.php';

require_once XOOPS_ROOT_PATH . '/include/cp_header.php';

global $xoopsUser, $xoopsModule, $xoopsModule, $xoopsConfig;

$adminObject = Admin::getInstance();

$myts = MyTextSanitizer::getInstance();

//$moduleInfo = $module_handler->get($xoopsModule->getVar('mid'));
//$pathIcon16 = XOOPS_URL . '/' . $moduleInfo->getInfo('icons16');
//$pathIcon32 = XOOPS_URL . '/' . $moduleInfo->getInfo('icons32');

$pathIcon16 = Admin::iconUrl('', '16');
$pathIcon32 = Admin::iconUrl('', '32');

if ($xoopsUser) {
    /** @var XoopsGroupPermHandler $modulepermHandler */
    $modulepermHandler = xoops_getHandler('groupperm');
    if (!$modulepermHandler->checkRight('module_admin', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
        redirect_header(XOOPS_URL, 1, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    require_once XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new XoopsTpl();
}

$xoopsTpl->assign('pathIcon16', $pathIcon16);

// Load language files
if (!@include(XOOPS_TRUST_PATH . MODULES . $xoopsModule->getVar('dirname') . LANGUAGE . $xoopsConfig['language'] . '/admin.php')) {
    require_once XOOPS_TRUST_PATH . MODULES . $xoopsModule->getVar('dirname') . LANGUAGE . 'english/admin.php';
}
if (!@include(XOOPS_TRUST_PATH . MODULES . $xoopsModule->getVar('dirname') . LANGUAGE . $xoopsConfig['language'] . '/modinfo.php')) {
    require_once XOOPS_TRUST_PATH . MODULES . $xoopsModule->getVar('dirname') . LANGUAGE . 'english/modinfo.php';
}
if (!@include(XOOPS_TRUST_PATH . MODULES . $xoopsModule->getVar('dirname') . LANGUAGE . $xoopsConfig['language'] . '/main.php')) {
    require_once XOOPS_TRUST_PATH . MODULES . $xoopsModule->getVar('dirname') . LANGUAGE . 'english/main.php';
}
