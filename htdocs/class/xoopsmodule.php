<?php
/**
 * XOOPS module
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
 * @deprecated
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$GLOBALS['xoopsLogger']->addDeprecated("'/class/xoopsmodule.php' is deprecated since XOOPS 2.5.4, please use '/kernel/module.php' instead.");

/**
 * Path Change: This file is here for backward compatibility only.
 *
 **/
include_once $GLOBALS['xoops']->path('kernel/module.php');
