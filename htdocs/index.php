<?php
/**
 * XOOPS global entry
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
 * @package             core
 * @since               2.0.0
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
/* @var  $xoopsUser XoopsUser */

if (file_exists(__DIR__ . '/mainfile.php')) {
    include __DIR__ . '/mainfile.php';
}
if (!defined('XOOPS_MAINFILE_INCLUDED')) {
    if (file_exists(__DIR__ . '/install/index.php')) {
        header('Location: install/index.php');
        exit;
    }
}

$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.index.start');

//check if start page is defined
if (isset($xoopsConfig['startpage']) && $xoopsConfig['startpage'] != '' && $xoopsConfig['startpage'] != '--' && xoops_isActiveModule($xoopsConfig['startpage'])) {
    // Temporary solution for start page redirection
    define('XOOPS_STARTPAGE_REDIRECTED', 1);

    global $xoopsModuleConfig;
    /* @var $module_handler XoopsModuleHandler  */
    $module_handler = xoops_getHandler('module');
    $xoopsModule    = $module_handler->getByDirname($xoopsConfig['startpage']);
    if (!$xoopsModule || !$xoopsModule->getVar('isactive')) {
        include_once $GLOBALS['xoops']->path('header.php');
        echo '<h4>' . _MODULENOEXIST . '</h4>';
        include_once $GLOBALS['xoops']->path('footer.php');
        exit();
    }
    /* @var  $moduleperm_handler XoopsGroupPermHandler */
    $moduleperm_handler = xoops_getHandler('groupperm');
    if ($xoopsUser) {
        if (!$moduleperm_handler->checkRight('module_read', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
            redirect_header(XOOPS_URL, 1, _NOPERM, false);
        }
        $xoopsUserIsAdmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
    } else {
        if (!$moduleperm_handler->checkRight('module_read', $xoopsModule->getVar('mid'), XOOPS_GROUP_ANONYMOUS)) {
            redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
        }
    }
    if ($xoopsModule->getVar('hasconfig') == 1 || $xoopsModule->getVar('hascomments') == 1 || $xoopsModule->getVar('hasnotification') == 1) {
        $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
    }

    chdir('modules/' . $xoopsConfig['startpage'] . '/');
    xoops_loadLanguage('main', $xoopsModule->getVar('dirname', 'n'));
    $parsed = parse_url(XOOPS_URL);
    $url    = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : 'http://';
    if (isset($parsed['host'])) {
        $url .= $parsed['host'];
        if (isset($parsed['port'])) {
            $url .= ':' . $parsed['port'];
        }
    } else {
        $url .= $_SERVER['HTTP_HOST'];
    }

    $_SERVER['REQUEST_URI'] = substr(XOOPS_URL, strlen($url)) . '/modules/' . $xoopsConfig['startpage'] . '/index.php';
    include $GLOBALS['xoops']->path('modules/' . $xoopsConfig['startpage'] . '/index.php');
    exit();
} else {
    $xoopsOption['show_cblock']   = 1;
    $GLOBALS['xoopsOption']['template_main'] = 'db:system_homepage.tpl';
    include $GLOBALS['xoops']->path('header.php');
    include $GLOBALS['xoops']->path('footer.php');
}
