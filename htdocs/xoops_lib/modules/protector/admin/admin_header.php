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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             pm
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

//include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include_once XOOPS_ROOT_PATH . '/mainfile.php';

include_once XOOPS_ROOT_PATH . '/include/cp_header.php';
include_once XOOPS_ROOT_PATH . '/include/cp_functions.php';

//include XOOPS_ROOT_PATH . '/include/cp_header.php';
//require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/include/functions.php';

if (file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))) {
    include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');
    //return true;
} else {
    redirect_header(XOOPS_ROOT_PATH . '/admin.php', 5, _AM_MODULEADMIN_MISSING, false);
    //return false;
}

$myts = \MyTextSanitizer::getInstance();

$moduleInfo = $module_handler->get($xoopsModule->getVar('mid'));
$pathIcon16 = XOOPS_URL . '/' . $moduleInfo->getInfo('icons16');
$pathIcon32 = XOOPS_URL . '/' . $moduleInfo->getInfo('icons32');

if ($xoopsUser) {
    /** @var XoopsGroupPermHandler $moduleperm_handler */
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

$moduleDir = $xoopsModule->getVar('dirname');
$language = $xoopsConfig['language'];

// List of language files to include
$languageFiles = ['admin.php', 'modinfo.php', 'main.php'];

foreach ($languageFiles as $file) {
    $languageFile = XOOPS_TRUST_PATH . "/modules/{$moduleDir}/language/{$language}/{$file}";
    $englishFile = XOOPS_TRUST_PATH . "/modules/{$moduleDir}/language/english/{$file}";

    // Attempt to include the language-specific file, fallback to English if not found
    if (file_exists($languageFile)) {
        include_once $languageFile;
    } else {
        include_once $englishFile;
    }
}
