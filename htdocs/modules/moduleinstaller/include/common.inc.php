<?php declare(strict_types=1);
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Installer common include file
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since       2.3.0
 * @author      Haruki Setoyama  <haruki@planewave.org>
 * @author      Kazumi Ono <webmaster@myweb.ne.jp>
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @author      DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

use XoopsModules\Moduleinstaller;

/**
 * If non-empty, only this user can access this installer
 */
define('INSTALL_USER', '');
define('INSTALL_PASSWORD', '');

define('XOOPS_INSTALL', 1);

// options for mainfile.php
if (empty($xoopsOption['hascommon'])) {
    $xoopsOption['nocommon'] = true;
    session_start();
}
require_once \dirname(__DIR__, 3) . '/mainfile.php';

/*
error_reporting( 0 );
if (isset($xoopsLogger)) {
    $xoopsLogger->activated = false;
}
error_reporting(E_ALL);
$xoopsLogger->activated = true;
*/

// require_once  \dirname(__DIR__) . '/class/installwizard.php';
require_once XOOPS_ROOT_PATH . '/include/version.php';
require_once XOOPS_ROOT_PATH . '/include/functions.php';
require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';

$pageHasHelp = false;
$pageHasForm = false;

$wizard = new Moduleinstaller\InstallWizard();
if (!$wizard->xoInit()) {
    exit();
}

if (isset($_SESSION['settings']) && !is_array($_SESSION['settings'])) {
    $_SESSION['settings'] = [];
}
