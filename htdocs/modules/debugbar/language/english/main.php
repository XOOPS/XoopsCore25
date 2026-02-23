<?php
/**
 * DebugBar Module - Main Language Constants
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              trabis <lusopoemas@gmail.com>
 * @author              Richard Griffith <richard@geekwright.com>
 */

define('_MD_DEBUGBAR_DEBUG', 'Debug');
define('_MD_DEBUGBAR_INCLUDED_FILES', 'Included files');
define('_MD_DEBUGBAR_PHP_VERSION', 'PHP Version');
define('_MD_DEBUGBAR_NONE', 'None');
define('_MD_DEBUGBAR_ERRORS', 'Errors');
define('_MD_DEBUGBAR_DEPRECATED', 'Deprecated');
define('_MD_DEBUGBAR_QUERIES', 'Queries');
define('_MD_DEBUGBAR_BLOCKS', 'Blocks');
define('_MD_DEBUGBAR_EXTRA', 'Extra');
define('_MD_DEBUGBAR_TIMERS', 'Timers');
define('_MD_DEBUGBAR_TIMETOLOAD', '%s took %s seconds to load.');
define('_MD_DEBUGBAR_TOTAL', 'Total');
define('_MD_DEBUGBAR_NOT_CACHED', 'Not cached');
define('_MD_DEBUGBAR_CACHED', 'Cached (regenerates every %s seconds)');

// Value display labels (Smarty/DebugBar panels)
define('_MD_DEBUGBAR_EMPTY_STRING', '(empty string)');
define('_MD_DEBUGBAR_NULL', 'NULL');
define('_MD_DEBUGBAR_BOOL_TRUE', 'bool TRUE');
define('_MD_DEBUGBAR_BOOL_FALSE', 'bool FALSE');

// Extra panel labels
define('_MD_DEBUGBAR_DATABASE_QUERIES', 'Database Queries');
define('_MD_DEBUGBAR_MEMORY_USAGE', 'Memory Usage');
define('_MD_DEBUGBAR_QUERY_SUMMARY', '%d queries');
define('_MD_DEBUGBAR_QUERY_DUPLICATES', ' (%d duplicates)');
define('_MD_DEBUGBAR_BYTES', '%s bytes');
define('_MD_DEBUGBAR_DB_VERSION', '%s version');

// Query error formatting
define('_MD_DEBUGBAR_QUERY_ERROR', ' -- Error number: %s Error message: %s');
define('_MD_DEBUGBAR_QUERY_ERROR_RAY', "\n-- Error #%s: %s");

// Ray labels
define('_MD_DEBUGBAR_RAY_EXCEPTION', 'Exception');
define('_MD_DEBUGBAR_RAY_QUERY', 'Query #%d');
define('_MD_DEBUGBAR_RAY_DUP', ' [DUP x%d]');
define('_MD_DEBUGBAR_RAY_SLOW', ' SLOW');
define('_MD_DEBUGBAR_RAY_BLOCK_CACHED', 'Block (cached %ds)');
define('_MD_DEBUGBAR_RAY_BLOCK_NOT_CACHED', 'Block (not cached)');
define('_MD_DEBUGBAR_RAY_DUMP', 'Dump');
define('_MD_DEBUGBAR_RAY_TEMPLATE_CONTEXT', 'Template Context');
define('_MD_DEBUGBAR_RAY_NO_VARS', '(no template variables)');
define('_MD_DEBUGBAR_RAY_VARS_COUNT', '%s (%d vars)');

// Install error messages
define('_MD_DEBUGBAR_ERR_DIR_CREATE', 'Directory "modules/debugbar/%s" was not created');
define('_MD_DEBUGBAR_ERR_DIR_COPY', 'Failed to create directory "%s" during asset copy');
