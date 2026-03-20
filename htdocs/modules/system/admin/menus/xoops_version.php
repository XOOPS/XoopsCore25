<?php
/**
 * @copyright    2000-2026 XOOPS Project https://xoops.org/
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since        2.5.12
 * @author       XOOPS Development Team
 */
 
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$modversion = [
    'name'        => _AM_SYSTEM_MENUS,
    'version'     => '1.0',
    'description' => _AM_SYSTEM_MENUS_DESC,
    'author'      => '',
    'credits'     => 'XOOPS Development Team',
    'help'        => 'page=menus',
    'license'     => 'GPL see LICENSE',
    'official'    => 1,
    'image'       => 'menus.png',
    'icon'        => 'fa fa-bars',
    'hasAdmin'    => 1,
    'adminpath'   => 'admin.php?fct=menus',
    'category'    => XOOPS_SYSTEM_MENUS,
];
