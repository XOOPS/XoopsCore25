<?php
/**
 * Extended User Profile
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
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

$xoopsOption['pagetype'] = 'user';
include dirname(__DIR__, 2) . '/mainfile.php';
$xoopsOption['xoops_module_header'] = '<link rel="stylesheet" type="text/css" href="assets/css/style.css" />';

$xoBreadcrumbs   = [];
$xoBreadcrumbs[] = [
    'title' => $GLOBALS['xoopsModule']->getVar('name'),
    'link' => XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/',
];

//disable cache
$GLOBALS['xoopsConfig']['module_cache'][$GLOBALS['xoopsModule']->getVar('mid')] = 0;
