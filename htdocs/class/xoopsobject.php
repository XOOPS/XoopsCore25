<?php
/**
 * XOOPS object
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
 * @deprecated
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$GLOBALS['xoopsLogger']->addDeprecated("'/class/xoopsobject.php' is deprecated since XOOPS 2.5.4, please use '/kernel/object.php' instead.");

/**
 * Path Change: This file is here for backward compatibility only.
 *
 **/
include_once $GLOBALS['xoops']->path('kernel/object.php');
