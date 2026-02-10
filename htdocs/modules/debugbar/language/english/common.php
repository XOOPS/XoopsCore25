<?php
declare(strict_types=1);

/**
 * DebugBar Module - Common Language Constants
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             debugbar
 */

$moduleDirName      = \basename(\dirname(__DIR__, 2));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

// Module Stats
\define('CO_' . $moduleDirNameUpper . '_' . 'STATS_SUMMARY', 'Module Status');
