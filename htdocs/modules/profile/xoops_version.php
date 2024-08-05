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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

/**
 * This is a temporary solution for merging XOOPS 2.0 and 2.2 series
 * A thorough solution will be available in XOOPS 3.0
 *
 */
$modversion                   = [];
$modversion['name']           = _PROFILE_MI_NAME;
$modversion['version']        = '1.9.2-Stable';
$modversion['description']    = _PROFILE_MI_DESC;
$modversion['author']         = 'Jan Pedersen, Taiwen Jiang, alfred, Wishcraft';
$modversion['credits']        = 'Ackbarr, mboyden, marco, mamba, trabis, etc.';
$modversion['help']           = 'page=help';
$modversion['license']        = 'GNU GPL 2.0 or later';
$modversion['license_url']    = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['image']          = 'assets/images/logo.png';
$modversion['dirname']        = 'profile';
$modversion['dirmoduleadmin'] = '/Frameworks/moduleclasses/moduleadmin';
$modversion['icons16']        = '../../Frameworks/moduleclasses/icons/16';
$modversion['icons32']        = '../../Frameworks/moduleclasses/icons/32';

//about
$modversion['release_date']        = '2022/09/09';
$modversion['module_website_url']  = 'https://xoops.org/';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.6.0';
$modversion['min_xoops']           = '2.5.11';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.0.7'];

// Admin menu
// Set to 1 if you want to display menu generated by system module
$modversion['system_menu'] = 1;

// Admin things
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/menu.php';

// Scripts to run upon installation or update
$modversion['onInstall'] = 'include/install.php';
$modversion['onUpdate']  = 'include/update.php';

// Menu
$modversion['hasMain'] = 1;
if ($GLOBALS['xoopsUser']) {
    $modversion['sub'][1]['name'] = _PROFILE_MI_EDITACCOUNT;
    $modversion['sub'][1]['url']  = 'edituser.php';
    $modversion['sub'][2]['name'] = _PROFILE_MI_PAGE_SEARCH;
    $modversion['sub'][2]['url']  = 'search.php';
    $modversion['sub'][3]['name'] = _PROFILE_MI_CHANGEPASS;
    $modversion['sub'][3]['url']  = 'changepass.php';
}

// Mysql file
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'][1] = 'profile_category';
$modversion['tables'][2] = 'profile_profile';
$modversion['tables'][3] = 'profile_field';
$modversion['tables'][4] = 'profile_visibility';
$modversion['tables'][5] = 'profile_regstep';

// Config items
$modversion['config'][] = [
    'name'        => 'profile_search',
    'title'       => '_PROFILE_MI_PROFILE_SEARCH',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'profileCaptchaAfterStep1',
    'title'       => '_PROFILE_MI_PROFILE_CAPTCHA_STEP1',
    'description' => '_PROFILE_MI_PROFILE_CAPTCHA_STEP1_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

// Templates

$modversion['templates'][] = ['file' => 'profile_breadcrumbs.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_form.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_admin_fieldlist.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_userinfo.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_admin_categorylist.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_search.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_results.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_admin_visibility.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_admin_steplist.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_register.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_changepass.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_editprofile.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_userform.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_avatar.tpl', 'description' => ''];
$modversion['templates'][] = ['file' => 'profile_email.tpl', 'description' => ''];
