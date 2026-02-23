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

// Build status rows
$hasDebugbar = \class_exists('DebugBar\StandardDebugBar');
$hasMonolog  = \class_exists('Monolog\Logger');
$assetsDir   = XOOPS_ROOT_PATH . '/modules/debugbar/assets';
$assetsExist = \is_dir($assetsDir) && \count(\glob($assetsDir . '/*')) > 0;
$hasRay      = \function_exists('ray');

$statusRows = [
    [_AM_DEBUGBAR_PHP_DEBUGBAR, $hasDebugbar ? _AM_DEBUGBAR_INSTALLED : _AM_DEBUGBAR_NOT_FOUND, $hasDebugbar ? 'green' : 'red'],
    [_AM_DEBUGBAR_MONOLOG,      $hasMonolog  ? _AM_DEBUGBAR_INSTALLED : _AM_DEBUGBAR_NOT_FOUND, $hasMonolog  ? 'green' : 'red'],
    [_AM_DEBUGBAR_PHP_VERSION,  PHP_VERSION, 'green'],
    [_AM_DEBUGBAR_ASSETS,       $assetsExist ? _AM_DEBUGBAR_COPIED : _AM_DEBUGBAR_NOT_COPIED,   $assetsExist ? 'green' : 'orange'],
    [_AM_DEBUGBAR_RAY,          $hasRay      ? _AM_DEBUGBAR_AVAILABLE : _AM_DEBUGBAR_NOT_INSTALLED, $hasRay ? 'green' : 'gray'],
];

// Render as a single HTML table inside one info box line
$html          = '<table style="border-collapse: collapse; width: auto;">';
$allowedColors = ['green', 'red', 'orange', 'gray'];
foreach ($statusRows as $row) {
    $label = \htmlspecialchars((string) $row[0], ENT_QUOTES, 'UTF-8');
    $value = \htmlspecialchars((string) $row[1], ENT_QUOTES, 'UTF-8');
    $color = \in_array($row[2], $allowedColors, true) ? $row[2] : 'black';

    $html .= '<tr>'
        . '<td style="padding: 2px 20px 2px 0; white-space: nowrap;">' . $label . '</td>'
        . '<td style="padding: 2px 0; font-weight: bold; color: ' . $color . ';">' . $value . '</td>'
        . '</tr>';
}
$html .= '</table>';

$adminObject->addInfoBoxLine($html, 'information');

$adminObject->displayIndex();

require_once __DIR__ . '/admin_footer.php';
