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

// PHP DebugBar library
$hasDebugbar = \class_exists('DebugBar\StandardDebugBar');
$color       = $hasDebugbar ? 'green' : 'red';
$status      = $hasDebugbar ? _AM_DEBUGBAR_INSTALLED : _AM_DEBUGBAR_NOT_FOUND;
$adminObject->addInfoBoxLine(
    \sprintf('%s <span style="font-weight: bold; color: %s;">%s</span>', _AM_DEBUGBAR_PHP_DEBUGBAR, $color, $status)
);

// Monolog library
$hasMonolog = \class_exists('Monolog\Logger');
$color      = $hasMonolog ? 'green' : 'red';
$status     = $hasMonolog ? _AM_DEBUGBAR_INSTALLED : _AM_DEBUGBAR_NOT_FOUND;
$adminObject->addInfoBoxLine(
    \sprintf('%s <span style="font-weight: bold; color: %s;">%s</span>', _AM_DEBUGBAR_MONOLOG, $color, $status)
);

// PHP Version
$adminObject->addInfoBoxLine(
    \sprintf('%s <span style="font-weight: bold; color: green;">%s</span>', _AM_DEBUGBAR_PHP_VERSION, PHP_VERSION)
);

// DebugBar Assets
$assetsDir   = XOOPS_ROOT_PATH . '/modules/debugbar/assets';
$assetsExist = \is_dir($assetsDir) && \count(\glob($assetsDir . '/*')) > 0;
$color       = $assetsExist ? 'green' : 'orange';
$status      = $assetsExist ? _AM_DEBUGBAR_COPIED : _AM_DEBUGBAR_NOT_COPIED;
$adminObject->addInfoBoxLine(
    \sprintf('%s <span style="font-weight: bold; color: %s;">%s</span>', _AM_DEBUGBAR_ASSETS, $color, $status)
);

// Ray Integration
$hasRay = \function_exists('ray');
$color  = $hasRay ? 'green' : 'gray';
$status = $hasRay ? _AM_DEBUGBAR_AVAILABLE : _AM_DEBUGBAR_NOT_INSTALLED;
$adminObject->addInfoBoxLine(
    \sprintf('%s <span style="font-weight: bold; color: %s;">%s</span>', _AM_DEBUGBAR_RAY, $color, $status)
);

$adminObject->displayIndex();

require_once __DIR__ . '/admin_footer.php';
