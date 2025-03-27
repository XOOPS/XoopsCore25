<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 */
$pages = [
    'langselect'      => [
        'name'  => LANGUAGE_SELECTION,
        'title' => LANGUAGE_SELECTION_TITLE,
        'icon'  => 'fa-solid fa-language',
    ],
    'start'           => [
        'name'  => INTRODUCTION,
        'title' => INTRODUCTION_TITLE,
        'icon'  => 'fa-solid fa-circle-exclamation',
    ],
    'modcheck'        => [
        'name'  => CONFIGURATION_CHECK,
        'title' => CONFIGURATION_CHECK_TITLE,
        'icon'  => 'fa-solid fa-server',
    ],
    'pathsettings'    => [
        'name'  => PATHS_SETTINGS,
        'title' => PATHS_SETTINGS_TITLE,
        'icon'  => 'fa-solid fa-folder-open',
    ],
    'dbconnection'    => [
        'name'  => DATABASE_CONNECTION,
        'title' => DATABASE_CONNECTION_TITLE,
        'icon'  => 'fa-solid fa-exchange',
    ],
    'dbsettings'      => [
        'name'  => DATABASE_CONFIG,
        'title' => DATABASE_CONFIG_TITLE,
        'icon'  => 'fa-solid fa-database',
    ],
    'configsave'      => [
        'name'  => CONFIG_SAVE,
        'title' => CONFIG_SAVE_TITLE,
        'icon'  => 'fa-solid fa-download',
    ],
    'tablescreate'    => [
        'name'  => TABLES_CREATION,
        'title' => TABLES_CREATION_TITLE,
        'icon'  => 'fa-solid fa-sitemap',
    ],
    'siteinit'        => [
        'name'  => INITIAL_SETTINGS,
        'title' => INITIAL_SETTINGS_TITLE,
        'icon'  => 'fa-solid fa-sliders',
    ],
    'tablesfill'      => [
        'name'  => DATA_INSERTION,
        'title' => DATA_INSERTION_TITLE,
        'icon'  => 'fa-solid fa-cloud-arrow-up',
    ],
    'configsite'      => [
        'name'  => CONFIG_SITE,
        'title' => CONFIG_SITE_TITLE,
        'icon'  => 'fa-solid fa-edit',
    ],
    'theme'           => [
        'name'  => THEME,
        'title' => THEME_TITLE,
        'icon'  => 'fa-solid fa-object-group',
    ],
    'moduleinstaller' => [
        'name'  => MODULES,
        'title' => MODULES_TITLE,
        'icon'  => 'fa-solid fa-cubes',
    ],
    'end'             => [
        'name'  => WELCOME,
        'title' => WELCOME_TITLE,
        'icon'  => 'fa-solid fa-thumbs-up',
    ],
];

return $pages;
