<?php
/**
 * Upgrader index file
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
 * @package             upgrader
 * @since               2.3.0
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
/** @var  XoopsUser $xoopsUser */

function fatalPhpErrorHandler($e = null) {
    $messageFormat = '<br><div>Fatal %s %s file: %s : %d </div>';
    $exceptionClass = '\Exception';
    $throwableClass = '\Throwable';
    if ($e === null) {
        $lastError = error_get_last();
        if ($lastError['type'] === E_ERROR) {
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

/*
 * Before xoops 2.5.8 the table 'sess_ip' was of type varchar (15). This is a problem for IPv6
 * addresses because it is longer. The upgrade process would change the column to VARCHAR(45)
 * but it requires login, which is failing. If the user has an IPv6 address, it is converted to
 * short IP during the upgrade. At the end of the upgrade IPV6 works
 *
 * Here we save the current IP address if needed
 */
$ip = $_SERVER['REMOTE_ADDR'];
if (strlen($_SERVER['REMOTE_ADDR']) > 15) {
    //new IP for upgrade
    $_SERVER['REMOTE_ADDR'] = '::1';
}

include_once __DIR__ . '/checkmainfile.php';
defined('XOOPS_ROOT_PATH') or die('Bad installation: please add this folder to the XOOPS install you want to upgrade');

if (!isset($_SESSION['preflight']) || (isset($_SESSION['preflight']) && $_SESSION['preflight']!=='complete')) {
    $_SESSION['preflight'] = 'active';
    header("Location: ./preflight.php");
    exit;
}

$reporting = 0;
if (isset($_GET['debug'])) {
    $reporting = -1;
}
error_reporting($reporting);
$xoopsLogger->activated = true;
$xoopsLogger->enableRendering();
xoops_loadLanguage('logger');
set_exception_handler('fatalPhpErrorHandler'); // should have been changed by now, reset to ours

require __DIR__ . '/class/abstract.php';
require __DIR__ . '/class/patchstatus.php';
require __DIR__ . '/class/control.php';

$GLOBALS['error'] = false;
$GLOBALS['upgradeControl'] = new UpgradeControl();

if (file_exists(__DIR__ . "../language/{$upgradeControl->upgradeLanguage}/user.php")) {
    include_once __DIR__ . "../language/{$upgradeControl->upgradeLanguage}/user.php";
} else {
    include_once XOOPS_ROOT_PATH . '/language/english/user.php';
}

if (file_exists(__DIR__ . "/language/{$upgradeControl->upgradeLanguage}/smarty3.php")) {
    include_once __DIR__ . "/language/{$upgradeControl->upgradeLanguage}/smarty3.php";
} else {
    include_once __DIR__ . "/language/english/smarty3.php";
}


$upgradeControl->storeMainfileCheck($needMainfileRewrite, $mainfileKeys);
$upgradeControl->determineLanguage();
$upgradeControl->buildUpgradeQueue();

ob_start();
global $xoopsUser;
if (!$xoopsUser || !$xoopsUser->isAdmin()) {
    include_once __DIR__ . '/login.php';
} else {
    $op = Xmf\Request::getCmd('action', '');
    if (!$upgradeControl->needUpgrade) {
        $op = '';
    }
    if (empty($op)) {
        $upgradeControl->loadLanguage('welcome');
        echo _XOOPS_UPGRADE_WELCOME;
    } else {
        if (!empty($upgradeControl->needWriteFiles)) {
            echo '<div class="panel panel-danger">'
                . '<div class="panel-heading">' . _SET_FILES_WRITABLE . '</div>'
                . '<div class="panel-body"><ul class="fa-ul">';
            foreach ($upgradeControl->needWriteFiles as $file) {
                echo '<li><i class="fa-li fa fa-ban text-danger"></i>' . $file . '</li>';
                $GLOBALS['error'] = true;
            }
            echo '</ul></div></div>';
        } else {
            $next = $upgradeControl->getNextPatch();
            printf('<h2>' . _PERFORMING_UPGRADE . '</h2>', $next);
            /** @var XoopsUpgrade $upgrader */
            $upgradeClass = $upgradeControl->upgradeQueue[$next]->patchClass;
            $upgrader = new $upgradeClass();
            $res = $upgrader->apply();
            if ($message = $upgrader->message()) {
                echo '<div class="well">' . $message . '</div>';
            }

            if ($res) {
                $upgradeControl->upgradeQueue[$next]->applied = true;
            }
        }
    }
    if (0 === $upgradeControl->countUpgradeQueue()) {
            echo $upgradeControl->oneButtonContinueForm(
                XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=system',
                array()
            );
    } else {
        echo $upgradeControl->oneButtonContinueForm();
    }
}
$content = ob_get_contents();
ob_end_clean();

include_once __DIR__ . '/upgrade_tpl.php';
