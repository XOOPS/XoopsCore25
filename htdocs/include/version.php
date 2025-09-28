<?php
/**
 * XOOPS Version Definition
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * YOU SHOULD NEVER TOUCH RELEVANT VARIABLES/FILES, THEY WILL BE REMOVED
 */
/**
 * INCLUDE Licence Header
 *
 * It is highly discouraged to use the license file
 * It will be depreciated and removed
 */
$licenseFile = __DIR__ . '/license.php';
if (file_exists($licenseFile)) {
    include_once $licenseFile;
}

/**
 *  Define XOOPS version
 */
define('XOOPS_VERSION', 'XOOPS 2.5.12-Beta8');
