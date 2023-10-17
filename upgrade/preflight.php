<?php
/**
 * Upgrade Smarty 3 Migration
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2023 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             upgrader
 * @since               2.3.0
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
/* @var  XoopsUser $xoopsUser */

use Xoops\Upgrade\ScannerOutput;
use Xoops\Upgrade\ScannerProcess;
use Xoops\Upgrade\ScannerWalker;
use Xoops\Upgrade\Smarty3ScannerOutput;
use Xoops\Upgrade\Smarty3TemplateChecks;
use Xoops\Upgrade\Smarty3TemplateRepair;
use Xoops\Upgrade\Smarty3RepairOutput;

function fatalPhpErrorHandler($e = null)
{
    $messageFormat = '<br><div>Fatal %s %s file: %s : %d </div>';
    $exceptionClass = '\Exception';
    $throwableClass = '\Throwable';
    if ($e === null) {
        $lastError = error_get_last();
        if (null !== $lastError && $lastError['type'] === E_ERROR) {
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

$endscan = Xmf\Request::getString('endscan', 'no');
if ($endscan === 'yes') {
    $_SESSION['preflight'] = 'complete';
    header("Location: ./index.php");
    exit;
}
$_SESSION['preflight'] = 'active'; // so that manually loading preflight.php forces to active

$reporting = 0;
if (isset($_GET['debug'])) {
    $reporting = -1;
}
error_reporting($reporting);
$xoopsLogger->activated = true;
$xoopsLogger->enableRendering();
xoops_loadLanguage('logger');
set_exception_handler('fatalPhpErrorHandler'); // should have been changed by now, reset to ours

require __DIR__ . '/class/Xoops/Upgrade/ScannerOutput.php';
require __DIR__ . '/class/Xoops/Upgrade/ScannerProcess.php';
require __DIR__ . '/class/Xoops/Upgrade/ScannerWalker.php';
require __DIR__ . '/class/Xoops/Upgrade/Smarty3ScannerOutput.php';
require __DIR__ . '/class/Xoops/Upgrade/Smarty3TemplateChecks.php';
require __DIR__ . '/class/Xoops/Upgrade/Smarty3TemplateRepair.php';
require __DIR__ . '/class/Xoops/Upgrade/Smarty3RepairOutput.php';

require __DIR__ . '/class/abstract.php';
require __DIR__ . '/class/patchstatus.php';
require __DIR__ . '/class/control.php';

$GLOBALS['error'] = false;
$GLOBALS['upgradeControl'] = new UpgradeControl();

$upgradeControl->determineLanguage();

if (file_exists(__DIR__ . "../language/{$upgradeControl->upgradeLanguage}/user.php")) {
    include_once __DIR__ . "../language/{$upgradeControl->upgradeLanguage}/user.php";
} else {
    include_once XOOPS_ROOT_PATH . "/language/english/user.php";
}
if (file_exists(__DIR__ . "/language/{$upgradeControl->upgradeLanguage}/smarty3.php")) {
    include_once __DIR__ . "/language/{$upgradeControl->upgradeLanguage}/smarty3.php";
} else {
    include_once __DIR__ . "/language/english/smarty3.php";
}

/**
 * User options form for preflight
 *  template_dir  a directory relative to XOOPS_ROOT_PATH, i.e. /themes/ or /themes/xbootstrap/
 *  template_ext  a file extension to scan for. Typical values are tpl or html
 *  runfix        if checked, attempt to fix any issues found. Note not all possible issues can be automatically fixed
 *
 * @return string options form
 */
function tplScannerForm($parameters=null)
{
    $action = XOOPS_URL . '/upgrade/preflight.php';

    $form = '<h2>' . _XOOPS_SMARTY3_RESCAN_OPTIONS . '</h2>';
    $form .= '<form action="' . $action . '" method="post" class="form-horizontal">';

    $form .= '<div class="form-group">';
    $form .= '<input name="template_dir" class="form-control" type="text" placeholder="/themes/">';
    $form .= '<label for="template_dir">' . _XOOPS_SMARTY3_TEMPLATE_DIR  . '</label>';
    $form .= '</div>';

    $form .= '<div class="form-group">';
    $form .= '<input name="template_ext" class="form-control" type="text" placeholder="tpl">';
    $form .= '<label for="template_ext">' . _XOOPS_SMARTY3_TEMPLATE_EXT  . '</label>';
    $form .= '</div>';

    $form .= '<div class="form-group row">';
    $form .= '<div class="form-check">';
    $form .= '<legend class="col-form-label">' . _XOOPS_SMARTY3_FIX_BUTTON . '</legend>';
    $form .= '<input class="form-check-input" type="checkbox" name="runfix" >';
    $form .= '<label class="form-check-label" for="runfix">' . _YES . '</label>';
    $form .= '</div>';
    $form .= '</div>';

    $form .= '<div class="form-group">';
    $form .= '<button class="btn btn-lg btn-success" type="submit">' . _XOOPS_SMARTY3_SCANNER_RUN;
    $form .= '  <span class="fa fa-caret-right"></span></button>';
    $form .= '</div>';

    $form .= '</form>';

    $form .= '<form action="' . $action . '" method="post" class="form-horizontal">';
    $form .= '<div class="form-group">';
    $form .= '<button class="btn btn-lg btn-danger" type="submit">' . _XOOPS_SMARTY3_SCANNER_END;
    $form .= '  <span class="fa fa-caret-right"></span></button>';
    $form .= '<input type="hidden" name="endscan" value="yes">';
    $form .= '</div>';

    $form .= '</form>';

    return $form;
}

ob_start();

global $xoopsUser;
if (!$xoopsUser || !$xoopsUser->isAdmin()) {
    include_once __DIR__ . '/login.php';
} else {
    $template_dir = Xmf\Request::getString('template_dir', '');
    $template_ext = Xmf\Request::getString('template_ext', '');
    $runfix = Xmf\Request::getString('runfix', 'off');
    Xmf\Debug::dump($_POST, $runfix, $template_dir, $template_ext);
    if (empty($op)) {
        $upgradeControl->loadLanguage('welcome');
        echo _XOOPS_SMARTY3_SCANNER_OFFER;
    }

    if ($runfix==='on') {
        $output = new Smarty3RepairOutput();
        $process = new Smarty3TemplateRepair($output);
    } else {
        $output = new Smarty3ScannerOutput();
        $process = new Smarty3TemplateChecks($output);
    }
    $scanner = new ScannerWalker($process, $output);
    if('' === $template_dir) {
        $scanner->addDirectory(XOOPS_ROOT_PATH . '/themes/');
        $scanner->addDirectory(XOOPS_ROOT_PATH . '/modules/');
    } else {
        $scanner->addDirectory(XOOPS_ROOT_PATH . $template_dir);
    }
    if('' === $template_ext) {
        $scanner->addExtension('tpl');
        $scanner->addExtension('html');
    } else {
        $scanner->addExtension($template_ext);
    }
    $scanner->runScan();

    echo $output->outputFetch();

    echo tplScannerForm();
}
$content = ob_get_contents();
ob_end_clean();

//echo $content;

include_once __DIR__ . '/upgrade_tpl.php';
