<?php
declare(strict_types=1);

/**
 * DebugBar Module - Admin Index
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             debugbar
 */

use Xmf\Module\Admin;
use XoopsModules\Debugbar\{
    Helper
};

/** @var Admin $adminObject */
/** @var Helper $helper */

require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$adminObject = Admin::getInstance();
$adminObject->displayNavigation(\basename(__FILE__));

// --- InfoBox: Module Status ---
$adminObject->addInfoBox(\constant('CO_' . $moduleDirNameUpper . '_' . 'STATS_SUMMARY'));

// Helper to format a two-column info line using XOOPS admin CSS tags
$infoLine = static function ($label, $value, $color = 'green') {
    return '<infolabel>' . $label . '</infolabel>'
        . '<infotext><span style="font-weight: bold; color: ' . $color . ';">' . $value . '</span></infotext>';
};

// PHP DebugBar library
$hasDebugbar = \class_exists('DebugBar\StandardDebugBar');
$adminObject->addInfoBoxLine(
    $infoLine(_AM_DEBUGBAR_PHP_DEBUGBAR, $hasDebugbar ? _AM_DEBUGBAR_INSTALLED : _AM_DEBUGBAR_NOT_FOUND, $hasDebugbar ? 'green' : 'red')
);

// Monolog library
$hasMonolog = \class_exists('Monolog\Logger');
$adminObject->addInfoBoxLine(
    $infoLine(_AM_DEBUGBAR_MONOLOG, $hasMonolog ? _AM_DEBUGBAR_INSTALLED : _AM_DEBUGBAR_NOT_FOUND, $hasMonolog ? 'green' : 'red')
);

// PHP Version
$adminObject->addInfoBoxLine(
    $infoLine(_AM_DEBUGBAR_PHP_VERSION, PHP_VERSION, 'green')
);

// DebugBar Assets
$assetsDir   = XOOPS_ROOT_PATH . '/modules/debugbar/assets';
$assetsExist = \is_dir($assetsDir) && \count(\glob($assetsDir . '/*')) > 0;
$adminObject->addInfoBoxLine(
    $infoLine(_AM_DEBUGBAR_ASSETS, $assetsExist ? _AM_DEBUGBAR_COPIED : _AM_DEBUGBAR_NOT_COPIED, $assetsExist ? 'green' : 'orange')
);

// Ray Integration
$hasRay = \function_exists('ray');
$adminObject->addInfoBoxLine(
    $infoLine(_AM_DEBUGBAR_RAY, $hasRay ? _AM_DEBUGBAR_AVAILABLE : _AM_DEBUGBAR_NOT_INSTALLED, $hasRay ? 'green' : 'gray')
);

$adminObject->displayIndex();

require_once __DIR__ . '/admin_footer.php';
