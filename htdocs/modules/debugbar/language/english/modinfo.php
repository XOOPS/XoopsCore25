<?php
/**
 * DebugBar Module - Module Info Language Constants
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Richard Griffith <richard@geekwright.com>
 */

define('_MI_DEBUGBAR_NAME', 'DebugBar');
define('_MI_DEBUGBAR_DSC', 'Error reporting and performance analysis using PHP DebugBar');

define('_MI_DEBUGBAR_ENABLE', 'Display DebugBar');
define('_MI_DEBUGBAR_SMARTYDEBUG', 'Enable Smarty Debug');
define('_MI_DEBUGBAR_FILESDEBUG', 'Enable Included Files Tab');
define('_MI_DEBUGBAR_FILESDEBUG_DSC', 'Show all PHP files loaded during the request');
define('_MI_DEBUGBAR_SLOWQUERY', 'Slow Query Threshold (seconds)');
define('_MI_DEBUGBAR_SLOWQUERY_DSC', 'Queries slower than this are highlighted in red (e.g. 0.05 = 50ms)');

define('_MI_DEBUGBAR_RAY_ENABLE', 'Enable Ray Integration');
define('_MI_DEBUGBAR_RAY_ENABLE_DSC', 'Send debug data to Ray desktop app (requires spatie/ray or spatie/global-ray)');

define('_MI_DEBUGBAR_ADMENU1', 'Home');
define('_MI_DEBUGBAR_MENU_ABOUT', 'About');

//Help
\define('_MI_DEBUGBAR_DIRNAME', basename(dirname(__DIR__, 3)));
\define('_MI_DEBUGBAR_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
\define('_MI_DEBUGBAR_BACK_2_ADMIN', 'Back to Administration of ');
\define('_MI_DEBUGBAR_OVERVIEW', 'Overview');

//help multipage
\define('_MI_DEBUGBAR_DISCLAIMER', 'Disclaimer');
\define('_MI_DEBUGBAR_LICENSE', 'License');
\define('_MI_DEBUGBAR_SUPPORT', 'Support');


