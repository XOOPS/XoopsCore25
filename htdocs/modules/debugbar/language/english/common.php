<?php
declare(strict_types=1);

/**
 * DebugBar Module - Common Language Constants
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             debugbar
 */

$moduleDirName      = \basename(\dirname(__DIR__, 3));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

// Module Stats
\define('_CO_DEBUGBAR_STATS_SUMMARY', 'Module Status');
