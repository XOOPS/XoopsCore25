<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

$modversion['name']        = _MI_SYSTEM_NAME;
$modversion['version']     = 2.11; // irmtfan bug fix: bug fix: remove codes for delete templates
$modversion['description'] = _MI_SYSTEM_DESC;
$modversion['author']      = '';
$modversion['credits']     = 'The XOOPS Project; MusS, Kraven30, Mage';
$modversion['help']        = 'system.html';
$modversion['license'] = "GPL see LICENSE";
$modversion['official']    = 1;
$modversion['image']       = 'images/system_slogo.png';
$modversion['dirname']     = 'system';

// Admin things
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin.php';
$modversion['adminmenu']  = 'menu.php';

$modversion['onUpdate']   = 'include/update.php';

// Templates
$modversion['templates'][] = array( 'file' => 'system_imagemanager.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_imagemanager2.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_userinfo.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_userform.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_rss.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_redirect.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_comment.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_comments_flat.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_comments_thread.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_comments_nest.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_siteclosed.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_dummy.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_notification_list.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_notification_select.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_block_dummy.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_homepage.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_bannerlogin.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_banner.html', 'description' => '' );
$modversion['templates'][] = array( 'file' => 'system_bannerdisplay.html', 'description' => '' );

// Admin Templates
$modversion['templates'][] = array( 'file' => 'system_header.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_banners.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_modules.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_modules_install.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_modules_confirm.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_modules_result.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_avatars.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_smilies.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_blocks.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_blocks_item.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_comments.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_comments_list.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_userrank.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_users.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_preferences.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_mailusers.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_groups.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_images.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_templates.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_filemanager.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_index.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_maintenance.html', 'description' => '', 'type' => 'admin' );
$modversion['templates'][] = array( 'file' => 'system_help.html', 'description' => '', 'type' => 'admin' );

// Blocks
$modversion['blocks'][] = array(
    'file'        => 'system_blocks.php',
    'name'        => _MI_SYSTEM_BNAME2,
    'description' => 'Shows user block',
    'show_func'   => 'b_system_user_show',
    'template'    => 'system_block_user.html');

$modversion['blocks'][2]['file'] = 'system_blocks.php';
$modversion['blocks'][2]['name'] = _MI_SYSTEM_BNAME3;
$modversion['blocks'][2]['description'] = 'Shows login form';
$modversion['blocks'][2]['show_func'] = 'b_system_login_show';
$modversion['blocks'][2]['template'] = 'system_block_login.html';

$modversion['blocks'][3]['file'] = 'system_blocks.php';
$modversion['blocks'][3]['name'] = _MI_SYSTEM_BNAME4;
$modversion['blocks'][3]['description'] = 'Shows search form block';
$modversion['blocks'][3]['show_func'] = 'b_system_search_show';
$modversion['blocks'][3]['template'] = 'system_block_search.html';

$modversion['blocks'][4]['file'] = 'system_blocks.php';
$modversion['blocks'][4]['name'] = _MI_SYSTEM_BNAME5;
$modversion['blocks'][4]['description'] = 'Shows contents waiting for approval';
$modversion['blocks'][4]['show_func'] = 'b_system_waiting_show';
$modversion['blocks'][4]['template'] = 'system_block_waiting.html';

$modversion['blocks'][5]['file'] = 'system_blocks.php';
$modversion['blocks'][5]['name'] = _MI_SYSTEM_BNAME6;
$modversion['blocks'][5]['description'] = 'Shows the main navigation menu of the site';
$modversion['blocks'][5]['show_func'] = 'b_system_main_show';
$modversion['blocks'][5]['template'] = 'system_block_mainmenu.html';

$modversion['blocks'][6]['file'] = 'system_blocks.php';
$modversion['blocks'][6]['name'] = _MI_SYSTEM_BNAME7;
$modversion['blocks'][6]['description'] = 'Shows basic info about the site and a link to Recommend Us pop up window';
$modversion['blocks'][6]['show_func'] = 'b_system_info_show';
$modversion['blocks'][6]['edit_func'] = 'b_system_info_edit';
$modversion['blocks'][6]['options'] = '320|190|s_poweredby.gif|1';
$modversion['blocks'][6]['template'] = 'system_block_siteinfo.html';

$modversion['blocks'][7]['file'] = 'system_blocks.php';
$modversion['blocks'][7]['name'] = _MI_SYSTEM_BNAME8;
$modversion['blocks'][7]['description'] = 'Displays users/guests currently online';
$modversion['blocks'][7]['show_func'] = 'b_system_online_show';
$modversion['blocks'][7]['template'] = 'system_block_online.html';

$modversion['blocks'][8]['file'] = 'system_blocks.php';
$modversion['blocks'][8]['name'] = _MI_SYSTEM_BNAME9;
$modversion['blocks'][8]['description'] = 'Top posters';
$modversion['blocks'][8]['show_func'] = 'b_system_topposters_show';
$modversion['blocks'][8]['options'] = '10|1';
$modversion['blocks'][8]['edit_func'] = 'b_system_topposters_edit';
$modversion['blocks'][8]['template'] = 'system_block_topusers.html';

$modversion['blocks'][9]['file'] = 'system_blocks.php';
$modversion['blocks'][9]['name'] = _MI_SYSTEM_BNAME10;
$modversion['blocks'][9]['description'] = 'Shows most recent users';
$modversion['blocks'][9]['show_func'] = 'b_system_newmembers_show';
$modversion['blocks'][9]['options'] = '10|1';
$modversion['blocks'][9]['edit_func'] = 'b_system_newmembers_edit';
$modversion['blocks'][9]['template'] = 'system_block_newusers.html';

$modversion['blocks'][10]['file'] = 'system_blocks.php';
$modversion['blocks'][10]['name'] = _MI_SYSTEM_BNAME11;
$modversion['blocks'][10]['description'] = 'Shows most recent comments';
$modversion['blocks'][10]['show_func'] = 'b_system_comments_show';
$modversion['blocks'][10]['options'] = '10';
$modversion['blocks'][10]['edit_func'] = 'b_system_comments_edit';
$modversion['blocks'][10]['template'] = 'system_block_comments.html';

// RMV-NOTIFY:
// Adding a block...
$modversion['blocks'][11]['file'] = 'system_blocks.php';
$modversion['blocks'][11]['name'] = _MI_SYSTEM_BNAME12;
$modversion['blocks'][11]['description'] = 'Shows notification options';
$modversion['blocks'][11]['show_func'] = 'b_system_notification_show';
$modversion['blocks'][11]['template'] = 'system_block_notification.html';

$modversion['blocks'][12]['file'] = 'system_blocks.php';
$modversion['blocks'][12]['name'] = _MI_SYSTEM_BNAME13;
$modversion['blocks'][12]['description'] = 'Shows theme selection box';
$modversion['blocks'][12]['show_func'] = 'b_system_themes_show';
$modversion['blocks'][12]['options'] = '0|80';
$modversion['blocks'][12]['edit_func'] = 'b_system_themes_edit';
$modversion['blocks'][12]['template'] = 'system_block_themes.html';

// Menu
$modversion['hasMain'] = 0;

// Préférences
$i = 0;
$modversion['config'][$i]['name'] = 'break1';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_BREAK_GENERAL';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'line_break';
$modversion['config'][$i]['valuetype'] = 'textbox';
$modversion['config'][$i]['default'] = 'head';
$i++;
$modversion['config'][$i]['name'] = 'usetips';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_TIPS';
$modversion['config'][$i]['description'] = '_MI_SYSTEM_PREFERENCE_TIPS_DSC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
$icons = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/modules/system/images/icons');
$modversion['config'][$i]['name']           = 'typeicons';
$modversion['config'][$i]['title']          = '_MI_SYSTEM_PREFERENCE_ICONS';
$modversion['config'][$i]['description']    = '';
$modversion['config'][$i]['formtype']       = 'select';
$modversion['config'][$i]['valuetype']      = 'text';
$modversion['config'][$i]['default']        = 'default';
$modversion['config'][$i]['options']        = $icons;
$modversion['config'][$i]['category']       = 'global';
$i++;
$breadcrumb = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/modules/system/images/breadcrumb');
$modversion['config'][$i]['name']           = 'typebreadcrumb';
$modversion['config'][$i]['title']          = '_MI_SYSTEM_PREFERENCE_BREADCRUMB';
$modversion['config'][$i]['description']    = '';
$modversion['config'][$i]['formtype']       = 'select';
$modversion['config'][$i]['valuetype']      = 'text';
$modversion['config'][$i]['default']        = 'default';
$modversion['config'][$i]['options']        = $breadcrumb;
$modversion['config'][$i]['category']       = 'global';
$i++;
$jquery_theme = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/modules/system/css/ui');
$modversion['config'][$i]['name']           = 'jquery_theme';
$modversion['config'][$i]['title']          = '_MI_SYSTEM_PREFERENCE_JQUERY_THEME';
$modversion['config'][$i]['description']    = '';
$modversion['config'][$i]['formtype']       = 'select';
$modversion['config'][$i]['valuetype']      = 'text';
$modversion['config'][$i]['default']        = 'base';
$modversion['config'][$i]['options']        = $jquery_theme;
$modversion['config'][$i]['category']       = 'global';
$i++;
$modversion['config'][$i]['name'] = 'break2';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_BREAK_ACTIVE';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'line_break';
$modversion['config'][$i]['valuetype'] = 'textbox';
$modversion['config'][$i]['default'] = 'head';
$i++;
$modversion['config'][$i]['name'] = 'active_avatars';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_AVATARS';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_banners';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_BANNERS';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_blocksadmin';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_BLOCKSADMIN';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
$modversion['config'][$i]['name'] = 'active_comments';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_COMMENTS';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_filemanager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_FILEMANAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_groups';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_GROUPS';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
$modversion['config'][$i]['name'] = 'active_images';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_IMAGES';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_mailusers';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_MAILUSERS';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_maintenance';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_MAINTENANCE';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_modulesadmin';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_MODULESADMIN';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
$modversion['config'][$i]['name'] = 'active_preferences';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_PREFERENCES';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
$modversion['config'][$i]['name'] = 'active_smilies';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_SMILIES';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_tplsets';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_TPLSETS';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
$modversion['config'][$i]['name'] = 'active_userrank';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_USERRANK';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'active_users';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ACTIVE_USERS';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
$i++;
$modversion['config'][$i]['name'] = 'break3';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_BREAK_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'line_break';
$modversion['config'][$i]['valuetype'] = 'textbox';
$modversion['config'][$i]['default'] = 'head';
$i++;
$modversion['config'][$i]['name'] = 'avatars_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_AVATARS_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 10;
$i++;
$modversion['config'][$i]['name'] = 'banners_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_BANNERS_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 10;
$i++;
$modversion['config'][$i]['name'] = 'comments_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_COMMENTS_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 20;
$i++;
$modversion['config'][$i]['name'] = 'groups_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_GROUPS_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 15;
$i++;
$modversion['config'][$i]['name'] = 'images_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_IMAGES_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 15;
$i++;
$modversion['config'][$i]['name'] = 'smilies_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_SMILIES_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 20;
$i++;
$modversion['config'][$i]['name'] = 'userranks_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_USERRANKS_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 20;
$i++;
$modversion['config'][$i]['name'] = 'users_pager';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_USERS_PAGER';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 20;
$i++;
$modversion['config'][$i]['name'] = 'break4';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_BREAK_EDITOR';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'line_break';
$modversion['config'][$i]['valuetype'] = 'textbox';
$modversion['config'][$i]['default'] = 'head';
$i++;
$editors = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/class/xoopseditor');
$modversion['config'][$i]['name']           = 'blocks_editor';
$modversion['config'][$i]['title']          = '_MI_SYSTEM_PREFERENCE_BLOCKS_EDITOR';
$modversion['config'][$i]['description']    = '_MI_SYSTEM_PREFERENCE_BLOCKS_EDITOR_DSC';
$modversion['config'][$i]['formtype']       = 'select';
$modversion['config'][$i]['valuetype']      = 'text';
$modversion['config'][$i]['default']        = 'dhtmltextarea';
$modversion['config'][$i]['options']        = $editors;
$modversion['config'][$i]['category']       = 'global';
$i++;
$modversion['config'][$i]['name']           = 'comments_editor';
$modversion['config'][$i]['title']          = '_MI_SYSTEM_PREFERENCE_COMMENTS_EDITOR';
$modversion['config'][$i]['description']    = '_MI_SYSTEM_PREFERENCE_COMMENTS_EDITOR_DSC';
$modversion['config'][$i]['formtype']       = 'select';
$modversion['config'][$i]['valuetype']      = 'text';
$modversion['config'][$i]['default']        = 'dhtmltextarea';
$modversion['config'][$i]['options']        = $editors;
$modversion['config'][$i]['category']       = 'global';
$i++;
$modversion['config'][$i]['name']           = 'general_editor';
$modversion['config'][$i]['title']          = '_MI_SYSTEM_PREFERENCE_GENERAL_EDITOR';
$modversion['config'][$i]['description']    = '_MI_SYSTEM_PREFERENCE_GENERAL_EDITOR_DSC';
$modversion['config'][$i]['formtype']       = 'select';
$modversion['config'][$i]['valuetype']      = 'text';
$modversion['config'][$i]['default']        = 'dhtmltextarea';
$modversion['config'][$i]['options']        = $editors;
$modversion['config'][$i]['category']       = 'global';
$i++;
$modversion['config'][$i]['name'] = 'redirect';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_REDIRECT';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'textbox';
$modversion['config'][$i]['default'] = 'admin.php?fct=preferences';
$i++;
$modversion['config'][$i]['name'] = 'com_anonpost';
$modversion['config'][$i]['title'] = '_MI_SYSTEM_PREFERENCE_ANONPOST';
$modversion['config'][$i]['description'] = '';
$modversion['config'][$i]['formtype'] = 'hidden';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 0;
