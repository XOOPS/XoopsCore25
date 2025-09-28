<?php
/**
 * XOOPS Editor usage guide
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
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

if (!function_exists('xoopseditor_get_rootpath')) {
    /**
     * @return string
     */
    function xoopseditor_get_rootpath()
    {
        return XOOPS_ROOT_PATH . '/class/xoopseditor';
    }
}
if (defined('XOOPS_ROOT_PATH')) {
    return true;
}

$mainfile = dirname(__DIR__, 2) . '/mainfile.php';
if (DIRECTORY_SEPARATOR !== '/') {
    $mainfile = str_replace(DIRECTORY_SEPARATOR, '/', $mainfile);
}
include $mainfile;

return defined('XOOPS_ROOT_PATH');
