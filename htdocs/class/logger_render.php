<?php
/**
 * XOOPS legacy logger renderer
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          logger
 * @since               2.0.0
 * @deprecated
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * this file is for backward compatibility only
 * @package     kernel
 * @subpackage  logger
 **/

/**
 * Load the new XoopsLogger class
 **/
include_once $GLOBALS['xoops']->path('class/logger/render.php');
trigger_error('Instance of ' . __FILE__ . ' file is deprecated, check in class/logger/render.php');
