<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.5.12
 */

/**
 * PHPStan constant stubs for XOOPS core.
 *
 * Declares constants that are defined at runtime by mainfile.php and the
 * XOOPS bootstrap so PHPStan can resolve their types during static analysis.
 * This file is never executed — it is parsed by PHPStan via the stubFiles
 * directive.
 */

// Core paths (defined in mainfile.php)
define('XOOPS_ROOT_PATH', '/path/to/htdocs');
define('XOOPS_PATH', '/path/to/xoops_lib');
define('XOOPS_VAR_PATH', '/path/to/xoops_data');
define('XOOPS_TRUST_PATH', '/path/to/xoops_lib');
define('XOOPS_URL', 'https://example.com');
define('XOOPS_PROT', 'https://');
define('XOOPS_COOKIE_DOMAIN', '');
define('XOOPS_CHECK_PATH', 0);
define('XOOPS_MAINFILE_INCLUDED', 1);

// Derived paths (defined in include/common.php or kernel)
define('XOOPS_CACHE_PATH', '/path/to/xoops_data/caches/xoops_cache');
define('XOOPS_UPLOAD_PATH', '/path/to/htdocs/uploads');
define('XOOPS_UPLOAD_URL', 'https://example.com/uploads');
define('XOOPS_COMPILE_PATH', '/path/to/xoops_data/caches/smarty_compile');
define('XOOPS_THEME_PATH', '/path/to/htdocs/themes');
define('XOOPS_THEME_URL', 'https://example.com/themes');

// Group constants — strings to match mainfile.dist.php
define('XOOPS_GROUP_ADMIN', '1');
define('XOOPS_GROUP_USERS', '2');
define('XOOPS_GROUP_ANONYMOUS', '3');

// Misc runtime constants
define('NWLINE', "\r\n");
define('_CHARSET', 'UTF-8');

// Database
define('XOOPS_DB_PREFIX', 'xoops');
define('XOOPS_DB_LEGACY_LOG', false);
define('XOOPS_DEBUG', false);
