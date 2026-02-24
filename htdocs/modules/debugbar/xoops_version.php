<?php
/**
 * DebugBar Module for XOOPS 2.5.12
 *
 * Provides PHP DebugBar integration for in-browser debugging.
 * Ported from XOOPS 2.6.0 modules/debugbar.
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              trabis <lusopoemas@gmail.com>
 * @author              Richard Griffith <richard@geekwright.com>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$modversion = [];

// --- Module Info ---
$modversion['name']         = _MI_DEBUGBAR_NAME;
$modversion['version']      = '1.0.0-beta1';
$modversion['release_date'] = '2026/02/09';
$modversion['description']  = _MI_DEBUGBAR_DSC;
$modversion['author']       = 'XOOPS Project';
$modversion['credits']      = 'trabis, Richard Griffith';
$modversion['license']      = 'GNU GPL 2.0 or later';
$modversion['license_url']  = 'https://www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']     = 1;
$modversion['image']        = 'assets/images/logoModule.png'; // optional, module works without it
$modversion['dirname']      = 'debugbar';

// --- Min Requirements ---
$modversion['min_php']   = '8.2.0';
$modversion['min_xoops'] = '2.5.12';

// --- Admin ---
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// --- Install/Update callbacks ---
$modversion['onInstall'] = 'include/install.php';
$modversion['onUpdate']  = 'include/install.php';

$modversion['help']        = 'page=help';
$modversion['helpsection'] = [
    ['name' => _MI_DEBUGBAR_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_DEBUGBAR_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_DEBUGBAR_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_DEBUGBAR_SUPPORT, 'link' => 'page=support'],
];



// --- Module Config ---
$modversion['config'][] = [
    'name'        => 'debugbar_enable',
    'title'       => '_MI_DEBUGBAR_ENABLE',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'debug_smarty_enable',
    'title'       => '_MI_DEBUGBAR_SMARTYDEBUG',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'debug_files_enable',
    'title'       => '_MI_DEBUGBAR_FILESDEBUG',
    'description' => '_MI_DEBUGBAR_FILESDEBUG_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'slow_query_threshold',
    'title'       => '_MI_DEBUGBAR_SLOWQUERY',
    'description' => '_MI_DEBUGBAR_SLOWQUERY_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '0.05',
];

$modversion['config'][] = [
    'name'        => 'ray_enable',
    'title'       => '_MI_DEBUGBAR_RAY_ENABLE',
    'description' => '_MI_DEBUGBAR_RAY_ENABLE_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];
