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
define('XOOPS_INSTALL_PATH', dirname(__DIR__));

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

$options = [
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => null,
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
];
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

//@include __DIR__ . '/../../mainfile.php';
$mainfile = dirname(__DIR__, 2) . '/mainfile.php';
if (file_exists($mainfile)) {
    include $mainfile;
}

if (!defined('XOOPS_ROOT_PATH')) {
    define('XOOPS_ROOT_PATH', str_replace("\\", '/', realpath('../')));
    define("XOOPS_PATH", $_SESSION['settings']['PATH'] ?? "");
    define("XOOPS_VAR_PATH", $_SESSION['settings']['VAR_PATH'] ?? "");
    define("XOOPS_URL", $_SESSION['settings']['URL'] ?? "");
}

date_default_timezone_set(@date_default_timezone_get());
//include __DIR__ . '/../class/installwizard.php';
//include_once __DIR__ . '/../../include/version.php';
include XOOPS_INSTALL_PATH . '/class/installwizard.php';
include_once XOOPS_ROOT_PATH . '/include/version.php';

//require_once __DIR__ . '/../../include/xoopssetcookie.php';
include_once XOOPS_ROOT_PATH . '/include/xoopssetcookie.php';

//include_once __DIR__ . '/../include/functions.php';
include_once XOOPS_INSTALL_PATH . '/include/functions.php';

//include_once __DIR__ . '/../../class/module.textsanitizer.php';
//include_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';

//include_once __DIR__ . '/../../xoops_lib/vendor/autoload.php';
//include_once XOOPS_TRUST_PATH . '/vendor/autoload.php';


if (defined('XOOPS_TRUST_PATH')) {
    include_once XOOPS_TRUST_PATH . '/vendor/autoload.php';
} elseif(isset($_SESSION['settings']['TRUST_PATH'])) {

    include_once $_SESSION['settings']['TRUST_PATH'] . '/vendor/autoload.php';

    } else {
    $possiblePaths = [
        dirname(__DIR__, 2) . '/xoops_lib/vendor/autoload.php',
        dirname(__DIR__, 2) . '/class/libraries/vendor/autoload.php'
    ];
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            include_once $path;
            break;
        }
    }
}



$pageHasHelp = false;
$pageHasForm = false;

$wizard = new XoopsInstallWizard();
if (!$wizard->xoInit()) {
    exit('Init Error');
}

if (!isset($_SESSION['settings']) || !is_array($_SESSION['settings'])) {
    $_SESSION['settings'] = [];
}
