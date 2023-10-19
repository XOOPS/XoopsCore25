<?php
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
 * @copyright    (c) 2000-2021 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

/**
 * If non-empty, only this user can access this installer
 */
define('INSTALL_USER', '');
define('INSTALL_PASSWORD', '');

define('XOOPS_INSTALL', 1);

function fatalPhpErrorHandler($e = null) {
    $messageFormat = '<br><div>Fatal %s %s file: %s : %d </div>';
    $exceptionClass = '\Exception';
    $throwableClass = '\Throwable';
    if ($e === null) {
        $lastError = error_get_last();
        if ($lastError !== null && $lastError['type'] === E_ERROR) {
            // fatal error
            printf($messageFormat, 'Error', $lastError['message'], $lastError['file'], $lastError['line']);
        }
    } elseif ($e instanceof $exceptionClass || $e instanceof $throwableClass) {
        /** @var \Exception $e */
        printf($messageFormat, get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
    }
}
register_shutdown_function('fatalPhpErrorHandler');
set_exception_handler('fatalPhpErrorHandler');

$options = array(
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => null,
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'strict',
);
// options for mainfile.php
if (empty($xoopsOption['hascommon'])) {
    $xoopsOption['nocommon'] = true;
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params($options);
    }

    session_start();

    if (PHP_VERSION_ID < 70300) {
        require_once __DIR__ . '/../../include/xoopssetcookie.php';
        xoops_setcookie(session_name(), session_id(), $options);
    }

}

@include __DIR__ . '/../../mainfile.php';
if (!defined('XOOPS_ROOT_PATH')) {
    define('XOOPS_ROOT_PATH', str_replace("\\", '/', realpath('../')));
}

date_default_timezone_set(@date_default_timezone_get());
include __DIR__ . '/../class/installwizard.php';
include_once __DIR__ . '/../../include/version.php';
require_once __DIR__ . '/../../include/xoopssetcookie.php';
include_once __DIR__ . '/../include/functions.php';
include_once __DIR__ . '/../../class/module.textsanitizer.php';
include_once __DIR__ . '/../../class/libraries/vendor/autoload.php';

$pageHasHelp = false;
$pageHasForm = false;

$wizard = new XoopsInstallWizard();
if (!$wizard->xoInit()) {
    exit();
}

if (!isset($_SESSION['settings']) || !is_array($_SESSION['settings'])) {
    $_SESSION['settings'] = array();
}
