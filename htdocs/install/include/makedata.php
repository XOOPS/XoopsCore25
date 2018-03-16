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
 * Installer data generation file
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             installer
 * @since               2.3.0
 * @author              Haruki Setoyama <haruki@planewave.org>
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              DuGris (aka L. JEN) <dugris@frxoops.org>
 * @param $dbm
 * @return bool
 */
// include_once './class/dbmanager.php';
// RMV
// TODO: Shouldn't we insert specific field names??  That way we can use
// the defaults specified in the database...!!!! (and don't have problem
// of missing fields in install file, when add new fields to database)
function make_groups(&$dbm)
{
    $groups['XOOPS_GROUP_ADMIN']     = $dbm->insert('groups', " VALUES (1, '" . addslashes(_INSTALL_WEBMASTER) . "', '" . addslashes(_INSTALL_WEBMASTERD) . "', 'Admin')");
    $groups['XOOPS_GROUP_USERS']     = $dbm->insert('groups', " VALUES (2, '" . addslashes(_INSTALL_REGUSERS) . "', '" . addslashes(_INSTALL_REGUSERSD) . "', 'User')");
    $groups['XOOPS_GROUP_ANONYMOUS'] = $dbm->insert('groups', " VALUES (3, '" . addslashes(_INSTALL_ANONUSERS) . "', '" . addslashes(_INSTALL_ANONUSERSD) . "', 'Anonymous')");
    if (!$groups['XOOPS_GROUP_ADMIN'] || !$groups['XOOPS_GROUP_USERS'] || !$groups['XOOPS_GROUP_ANONYMOUS']) {
        return false;
    }

    return $groups;
}

/**
 * @param $dbm
 * @param $adminname
 * @param $hashedAdminPass
 * @param $adminmail
 * @param $language
 * @param $groups
 *
 * @return mixed
 */
function make_data(&$dbm, $adminname, $hashedAdminPass, $adminmail, $language, $groups)
{
    $defaultTheme = 'xbootstrap';
    // $xoopsDB = Database::getInstance();
    // $dbm = new Db_manager;
    $tables = array();
    // data for table 'groups_users_link'
    /* @var  $dbm Db_manager */
    $dbm->insert('groups_users_link', ' VALUES (0, ' . $groups['XOOPS_GROUP_ADMIN'] . ', 1)');
    $dbm->insert('groups_users_link', ' VALUES (0, ' . $groups['XOOPS_GROUP_USERS'] . ', 1)');
    // data for table 'group_permission'
    $dbm->insert('group_permission', ' VALUES (0,' . $groups['XOOPS_GROUP_ADMIN'] . ",1,1,'module_admin')");
    $dbm->insert('group_permission', ' VALUES (0,' . $groups['XOOPS_GROUP_ADMIN'] . ",1,1, 'module_read')");
    $dbm->insert('group_permission', ' VALUES (0,' . $groups['XOOPS_GROUP_USERS'] . ",1,1,'module_read')");
    $dbm->insert('group_permission', ' VALUES (0,' . $groups['XOOPS_GROUP_ANONYMOUS'] . ",1,1,'module_read')");

    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",1,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",2,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",3,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",4,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",5,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",6,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",7,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",8,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",9,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",10,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",11,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",12,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",13,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",14,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",15,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",16,1,'system_admin')");
    $dbm->insert('group_permission', ' VALUES(0,' . $groups['XOOPS_GROUP_ADMIN'] . ",17,1,'system_admin')");
    // data for table 'banner'
    $dbm->insert('banner', " (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date, htmlbanner, htmlcode) VALUES (0, 1, 0, 1, 0, '" . XOOPS_URL . "/images/banners/xoops_flashbanner2.htm', 'http://www.xoops.org/', 1008813250, 1,'')");
    $dbm->insert('banner', " (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date, htmlbanner, htmlcode) VALUES (0, 1, 0, 1, 0, '" . XOOPS_URL . "/images/banners/xoops_banner_2.gif', 'http://www.xoops.org/', 1008813250, 0, '')");
    $dbm->insert('banner', " (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date, htmlbanner, htmlcode) VALUES (0, 1, 0, 1, 0, '" . XOOPS_URL . "/images/banners/banner.htm', 'http://www.xoops.org/', 1008813250, 1, '')");
    $dbm->insert('banner', " (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date, htmlbanner, htmlcode) VALUES (0, 1, 0, 1, 0, '" . XOOPS_URL . "/images/banners/xoopsifyIt.gif', 'http://www.xoops.org/', 1008813250, 1, '')");
    // default theme
    $time = time();
    $dbm->insert('tplset', " VALUES (1, 'default', 'XOOPS Default Template Set', '', " . $time . ')');
    // system modules
    if (file_exists('../modules/system/language/' . $language . '/modinfo.php')) {
        include '../modules/system/language/' . $language . '/modinfo.php';
    } else {
        include '../modules/system/language/english/modinfo.php';
        $language = 'english';
    }

    $modversion = array();
    include_once '../modules/system/xoops_version.php';
    $time = time();
    // RMV-NOTIFY (updated for extra column in table)
    $dbm->insert('modules', " VALUES (1, '" . _MI_SYSTEM_NAME . "', " . ($modversion['version'] * 100) . ', ' . $time . ", 0, 1, 'system', 0, 1, 0, 0, 0, 0)");

    foreach ($modversion['templates'] as $tplfile) {
        // Main templates
        if ($fp = fopen('../modules/system/templates/' . $tplfile['file'], 'r')) {
            $newtplid = $dbm->insert('tplfile', " VALUES (0, 1, 'system', 'default', '" . addslashes($tplfile['file']) . "', '" . addslashes($tplfile['description']) . "', " . $time . ', ' . $time . ", 'module')");
            // $newtplid = $xoopsDB->getInsertId();
            $tplsource = fread($fp, filesize('../modules/system/templates/' . $tplfile['file']));
            fclose($fp);
            $dbm->insert('tplsource', ' (tpl_id, tpl_source) VALUES (' . $newtplid . ", '" . addslashes($tplsource) . "')");
        }
        // Admin templates
        if ($fp = fopen('../modules/system/templates/admin/' . $tplfile['file'], 'r')) {
            $newtplid = $dbm->insert('tplfile', " VALUES (0, 1, 'system', 'default', '" . addslashes($tplfile['file']) . "', '" . addslashes($tplfile['description']) . "', " . $time . ', ' . $time . ", 'admin')");
            // $newtplid = $xoopsDB->getInsertId();
            $tplsource = fread($fp, filesize('../modules/system/templates/admin/' . $tplfile['file']));
            fclose($fp);
            $dbm->insert('tplsource', ' (tpl_id, tpl_source) VALUES (' . $newtplid . ", '" . addslashes($tplsource) . "')");
        }
    }

    foreach ($modversion['blocks'] as $func_num => $newblock) {
        if ($fp = fopen('../modules/system/templates/blocks/' . $newblock['template'], 'r')) {
            $visible = 0;
            if (in_array($newblock['template'], array('system_block_user.tpl', 'system_block_login.tpl', 'system_block_mainmenu.tpl'))) {
                $visible = 1;
            }
            $options   = !isset($newblock['options']) ? '' : trim($newblock['options']);
            $edit_func = !isset($newblock['edit_func']) ? '' : trim($newblock['edit_func']);
            $newbid    = $dbm->insert('newblocks', ' VALUES (0, 1, ' . $func_num . ", '" . addslashes($options) . "', '" . addslashes($newblock['name']) . "', '" . addslashes($newblock['name']) . "', '', 0, 0, " . $visible . ", 'S', 'H', 1, 'system', '" . addslashes($newblock['file']) . "', '" . addslashes($newblock['show_func']) . "', '" . addslashes($edit_func) . "', '" . addslashes($newblock['template']) . "', 0, " . $time . ')');
            // $newbid = $xoopsDB->getInsertId();
            $newtplid = $dbm->insert('tplfile', ' VALUES (0, ' . $newbid . ", 'system', 'default', '" . addslashes($newblock['template']) . "', '" . addslashes($newblock['description']) . "', " . $time . ', ' . $time . ", 'block')");
            // $newtplid = $xoopsDB->getInsertId();
            $tplsource = fread($fp, filesize('../modules/system/templates/blocks/' . $newblock['template']));
            fclose($fp);
            $dbm->insert('tplsource', ' (tpl_id, tpl_source) VALUES (' . $newtplid . ", '" . addslashes($tplsource) . "')");
            $dbm->insert('group_permission', ' VALUES (0, ' . $groups['XOOPS_GROUP_ADMIN'] . ', ' . $newbid . ", 1, 'block_read')");
            // $dbm->insert("group_permission", " VALUES (0, ".$groups['XOOPS_GROUP_ADMIN'].", ".$newbid.", 'xoops_blockadmiin')");
            $dbm->insert('group_permission', ' VALUES (0, ' . $groups['XOOPS_GROUP_USERS'] . ', ' . $newbid . ", 1, 'block_read')");
            $dbm->insert('group_permission', ' VALUES (0, ' . $groups['XOOPS_GROUP_ANONYMOUS'] . ', ' . $newbid . ", 1, 'block_read')");
        }
    }
    // data for table 'users'
    $temp    = $hashedAdminPass;
    $regdate = time();
    // $dbadminname= addslashes($adminname);
    // RMV-NOTIFY (updated for extra columns in user table)
    $dbm->insert('users', " VALUES (1,'','" . addslashes($adminname) . "','" . addslashes($adminmail) . "','" . XOOPS_URL . "/','avatars/blank.gif','" . $regdate . "','','','',1,'','','','','" . $temp . "',0,0,7,5,'default','0.0'," . time() . ",'flat',0,1,0,'','','',0)");
    // data for table 'block_module_link'
    $sql    = 'SELECT bid, side FROM ' . $dbm->prefix('newblocks');
    $result = $dbm->query($sql);

    while (false !== ($myrow = $dbm->fetchArray($result))) {
        if ($myrow['side'] == 0) {
            $dbm->insert('block_module_link', ' VALUES (' . $myrow['bid'] . ', 0)');
        } else {
            $dbm->insert('block_module_link', ' VALUES (' . $myrow['bid'] . ', -1)');
        }
    }
    // data for table 'config'
    $dbm->insert('config', " VALUES (1, 0, 1, 'sitename', '_MD_AM_SITENAME', 'XOOPS Site', '_MD_AM_SITENAMEDSC', 'textbox', 'text', 0)");
    $dbm->insert('config', " VALUES (2, 0, 1, 'slogan', '_MD_AM_SLOGAN', 'Just Use it!', '_MD_AM_SLOGANDSC', 'textbox', 'text', 2)");
    $dbm->insert('config', " VALUES (3, 0, 1, 'language', '_MD_AM_LANGUAGE', '" . addslashes($language) . "', '_MD_AM_LANGUAGEDSC', 'language', 'other', 4)");
    $dbm->insert('config', " VALUES (4, 0, 1, 'startpage', '_MD_AM_STARTPAGE', '--', '_MD_AM_STARTPAGEDSC', 'startpage', 'other', 6)");
    $dbm->insert('config', " VALUES (5, 0, 1, 'server_TZ', '_MD_AM_SERVERTZ', '0', '_MD_AM_SERVERTZDSC', 'timezone', 'float', 8)");
    $dbm->insert('config', " VALUES (6, 0, 1, 'default_TZ', '_MD_AM_DEFAULTTZ', '0', '_MD_AM_DEFAULTTZDSC', 'timezone', 'float', 10)");
    $dbm->insert('config', " VALUES (7, 0, 1, 'theme_set', '_MD_AM_DTHEME', '" . $defaultTheme ."', '_MD_AM_DTHEMEDSC', 'theme', 'other', 12)");
    $dbm->insert('config', " VALUES (8, 0, 1, 'anonymous', '_MD_AM_ANONNAME', '" . addslashes(_INSTALL_ANON) . "', '_MD_AM_ANONNAMEDSC', 'textbox', 'text', 15)");
    $dbm->insert('config', " VALUES (9, 0, 1, 'gzip_compression', '_MD_AM_USEGZIP', '0', '_MD_AM_USEGZIPDSC', 'yesno', 'int', 16)");
    $dbm->insert('config', " VALUES (10, 0, 1, 'usercookie', '_MD_AM_USERCOOKIE', 'xoops_user_" . dechex(time()) . "', '_MD_AM_USERCOOKIEDSC', 'textbox', 'text', 18)");
    $dbm->insert('config', " VALUES (11, 0, 1, 'session_expire', '_MD_AM_SESSEXPIRE', '15', '_MD_AM_SESSEXPIREDSC', 'textbox', 'int', 22)");
    $dbm->insert('config', " VALUES (12, 0, 1, 'banners', '_MD_AM_BANNERS', '1', '_MD_AM_BANNERSDSC', 'yesno', 'int', 26)");
    $dbm->insert('config', " VALUES (13, 0, 1, 'debug_mode', '_MD_AM_DEBUGMODE', '0', '_MD_AM_DEBUGMODEDSC', 'select', 'int', 24)");
    $dbm->insert('config', " VALUES (14, 0, 1, 'my_ip', '_MD_AM_MYIP', '127.0.0.1', '_MD_AM_MYIPDSC', 'textbox', 'text', 29)");
    $dbm->insert('config', " VALUES (15, 0, 1, 'use_ssl', '_MD_AM_USESSL', '0', '_MD_AM_USESSLDSC', 'yesno', 'int', 30)");
    $dbm->insert('config', " VALUES (16, 0, 1, 'session_name', '_MD_AM_SESSNAME', 'xoops_session_" . dechex(time()) . "', '_MD_AM_SESSNAMEDSC', 'textbox', 'text', 20)");
    $dbm->insert('config', " VALUES (17, 0, 2, 'minpass', '_MD_AM_MINPASS', '5', '_MD_AM_MINPASSDSC', 'textbox', 'int', 1)");
    $dbm->insert('config', " VALUES (18, 0, 2, 'minuname', '_MD_AM_MINUNAME', '3', '_MD_AM_MINUNAMEDSC', 'textbox', 'int', 2)");
    $dbm->insert('config', " VALUES (19, 0, 2, 'new_user_notify', '_MD_AM_NEWUNOTIFY', '1', '_MD_AM_NEWUNOTIFYDSC', 'yesno', 'int', 4)");
    $dbm->insert('config', " VALUES (20, 0, 2, 'new_user_notify_group', '_MD_AM_NOTIFYTO', " . $groups['XOOPS_GROUP_ADMIN'] . ", '_MD_AM_NOTIFYTODSC', 'group', 'int', 6)");
    $dbm->insert('config', " VALUES (21, 0, 2, 'activation_type', '_MD_AM_ACTVTYPE', '0', '_MD_AM_ACTVTYPEDSC', 'select', 'int', 8)");
    $dbm->insert('config', " VALUES (22, 0, 2, 'activation_group', '_MD_AM_ACTVGROUP', " . $groups['XOOPS_GROUP_ADMIN'] . ", '_MD_AM_ACTVGROUPDSC', 'group', 'int', 10)");
    $dbm->insert('config', " VALUES (23, 0, 2, 'uname_test_level', '_MD_AM_UNAMELVL', '0', '_MD_AM_UNAMELVLDSC', 'select', 'int', 12)");
    $dbm->insert('config', " VALUES (24, 0, 2, 'avatar_allow_upload', '_MD_AM_AVATARALLOW', '0', '_MD_AM_AVATARALWDSC', 'yesno', 'int', 14)");
    $dbm->insert('config', " VALUES (27, 0, 2, 'avatar_width', '_MD_AM_AVATARW', '128', '_MD_AM_AVATARWDSC', 'textbox', 'int', 16)");
    $dbm->insert('config', " VALUES (28, 0, 2, 'avatar_height', '_MD_AM_AVATARH', '128', '_MD_AM_AVATARHDSC', 'textbox', 'int', 18)");
    $dbm->insert('config', " VALUES (29, 0, 2, 'avatar_maxsize', '_MD_AM_AVATARMAX', '35000', '_MD_AM_AVATARMAXDSC', 'textbox', 'int', 20)");
    $dbm->insert('config', " VALUES (30, 0, 1, 'adminmail', '_MD_AM_ADMINML', '" . addslashes($adminmail) . "', '_MD_AM_ADMINMLDSC', 'textbox', 'text', 3)");
    $dbm->insert('config', " VALUES (31, 0, 2, 'self_delete', '_MD_AM_SELFDELETE', '0', '_MD_AM_SELFDELETEDSC', 'yesno', 'int', 22)");
    $dbm->insert('config', " VALUES (32, 0, 1, 'com_mode', '_MD_AM_COMMODE', 'flat', '_MD_AM_COMMODEDSC', 'select', 'text', 34)");
    $dbm->insert('config', " VALUES (33, 0, 1, 'com_order', '_MD_AM_COMORDER', '0', '_MD_AM_COMORDERDSC', 'select', 'int', 36)");
    $dbm->insert('config', " VALUES (34, 0, 2, 'bad_unames', '_MD_AM_BADUNAMES', '" . addslashes(serialize(array(
                                                                                                               'webmaster',
                                                                                                               '^xoops',
                                                                                                               '^admin'))) . "', '_MD_AM_BADUNAMESDSC', 'textarea', 'array', 24)");
    $dbm->insert('config', " VALUES (35, 0, 2, 'bad_emails', '_MD_AM_BADEMAILS', '" . addslashes(serialize(array('xoops.org$'))) . "', '_MD_AM_BADEMAILSDSC', 'textarea', 'array', 26)");
    $dbm->insert('config', " VALUES (36, 0, 2, 'maxuname', '_MD_AM_MAXUNAME', '10', '_MD_AM_MAXUNAMEDSC', 'textbox', 'int', 3)");
    $dbm->insert('config', " VALUES (37, 0, 1, 'bad_ips', '_MD_AM_BADIPS', '" . addslashes(serialize(array('127.0.0.1'))) . "', '_MD_AM_BADIPSDSC', 'textarea', 'array', 42)");
    $dbm->insert('config', " VALUES (38, 0, 3, 'meta_keywords', '_MD_AM_METAKEY', 'xoops, web applications, web 2.0, sns, news, technology, headlines, linux, software, download, downloads, free, community, forum, bulletin board, bbs, php, survey, polls, kernel, comment, comments, portal, odp, open source, opensource, FreeSoftware, gnu, gpl, license, Unix, *nix, mysql, sql, database, databases, web site, blog, wiki, module, modules, theme, themes, cms, content management', '_MD_AM_METAKEYDSC', 'textarea', 'text', 0)");
    $dbm->insert('config', " VALUES (39, 0, 3, 'footer', '_MD_AM_FOOTER', 'Powered by XOOPS &#169; 2001-" . date('Y', time()) . " <a href=\"http://xoops.org\" rel=\"external\" title=\"The XOOPS Project\">The XOOPS Project</a>', '_MD_AM_FOOTERDSC', 'textarea', 'text', 20)");
    $dbm->insert('config', " VALUES (40, 0, 4, 'censor_enable', '_MD_AM_DOCENSOR', '0', '_MD_AM_DOCENSORDSC', 'yesno', 'int', 0)");
    $dbm->insert('config', " VALUES (41, 0, 4, 'censor_words', '_MD_AM_CENSORWRD', '" . addslashes(serialize(array(
                                                                                                                 'fuck',
                                                                                                                 'shit'))) . "', '_MD_AM_CENSORWRDDSC', 'textarea', 'array', 1)");
    $dbm->insert('config', " VALUES (42, 0, 4, 'censor_replace', '_MD_AM_CENSORRPLC', '#OOPS#', '_MD_AM_CENSORRPLCDSC', 'textbox', 'text', 2)");
    $dbm->insert('config', " VALUES (43, 0, 3, 'meta_robots', '_MD_AM_METAROBOTS', 'index,follow', '_MD_AM_METAROBOTSDSC', 'select', 'text', 2)");
    $dbm->insert('config', " VALUES (44, 0, 5, 'enable_search', '_MD_AM_DOSEARCH', '1', '_MD_AM_DOSEARCHDSC', 'yesno', 'int', 0)");
    $dbm->insert('config', " VALUES (45, 0, 5, 'keyword_min', '_MD_AM_MINSEARCH', '5', '_MD_AM_MINSEARCHDSC', 'textbox', 'int', 1)");
    $dbm->insert('config', " VALUES (46, 0, 2, 'avatar_minposts', '_MD_AM_AVATARMP', '0', '_MD_AM_AVATARMPDSC', 'textbox', 'int', 15)");
    $dbm->insert('config', " VALUES (47, 0, 1, 'enable_badips', '_MD_AM_DOBADIPS', '0', '_MD_AM_DOBADIPSDSC', 'yesno', 'int', 40)");
    $dbm->insert('config', " VALUES (48, 0, 3, 'meta_rating', '_MD_AM_METARATING', 'general', '_MD_AM_METARATINGDSC', 'select', 'text', 4)");
    $dbm->insert('config', " VALUES (49, 0, 3, 'meta_author', '_MD_AM_METAAUTHOR', 'XOOPS', '_MD_AM_METAAUTHORDSC', 'textbox', 'text', 6)");
    $dbm->insert('config', " VALUES (50, 0, 3, 'meta_copyright', '_MD_AM_METACOPYR', 'Copyright &#169; 2001-" . date('Y', time()) . "', '_MD_AM_METACOPYRDSC', 'textbox', 'text', 8)");
    $dbm->insert('config', " VALUES (51, 0, 3, 'meta_description', '_MD_AM_METADESC', 'XOOPS is a dynamic Object Oriented based open source portal script written in PHP.', '_MD_AM_METADESCDSC', 'textarea', 'text', 1)");
    $dbm->insert('config', " VALUES (52, 0, 2, 'allow_chgmail', '_MD_AM_ALLWCHGMAIL', '0', '_MD_AM_ALLWCHGMAILDSC', 'yesno', 'int', 3)");
    $dbm->insert('config', " VALUES (53, 0, 1, 'use_mysession', '_MD_AM_USEMYSESS', '1', '_MD_AM_USEMYSESSDSC', 'yesno', 'int', 19)");
    $dbm->insert('config', " VALUES (54, 0, 2, 'reg_dispdsclmr', '_MD_AM_DSPDSCLMR', 1, '_MD_AM_DSPDSCLMRDSC', 'yesno', 'int', 30)");
    $dbm->insert('config', " VALUES (55, 0, 2, 'reg_disclaimer', '_MD_AM_REGDSCLMR', '" . addslashes(_INSTALL_DISCLMR) . "', '_MD_AM_REGDSCLMRDSC', 'textarea', 'text', 32)");
    $dbm->insert('config', " VALUES (56, 0, 2, 'allow_register', '_MD_AM_ALLOWREG', 1, '_MD_AM_ALLOWREGDSC', 'yesno', 'int', 0)");
    $dbm->insert('config', " VALUES (57, 0, 1, 'theme_fromfile', '_MD_AM_THEMEFILE', '0', '_MD_AM_THEMEFILEDSC', 'yesno', 'int', 13)");
    $dbm->insert('config', " VALUES (58, 0, 1, 'closesite', '_MD_AM_CLOSESITE', '0', '_MD_AM_CLOSESITEDSC', 'yesno', 'int', 26)");
    $dbm->insert('config', " VALUES (59, 0, 1, 'closesite_okgrp', '_MD_AM_CLOSESITEOK', '" . addslashes(serialize(array('1'))) . "', '_MD_AM_CLOSESITEOKDSC', 'group_multi', 'array', 27)");
    $dbm->insert('config', " VALUES (60, 0, 1, 'closesite_text', '_MD_AM_CLOSESITETXT', '" . _INSTALL_L165 . "', '_MD_AM_CLOSESITETXTDSC', 'textarea', 'text', 28)");
    $dbm->insert('config', " VALUES (61, 0, 1, 'sslpost_name', '_MD_AM_SSLPOST', 'xoops_ssl', '_MD_AM_SSLPOSTDSC', 'textbox', 'text', 31)");
    $dbm->insert('config', " VALUES (62, 0, 1, 'module_cache', '_MD_AM_MODCACHE', '', '_MD_AM_MODCACHEDSC', 'module_cache', 'array', 50)");
    $dbm->insert('config', " VALUES (63, 0, 1, 'template_set', '_MD_AM_DTPLSET', 'default', '_MD_AM_DTPLSETDSC', 'tplset', 'other', 14)");

    $dbm->insert('config', " VALUES (64,0,6,'mailmethod','_MD_AM_MAILERMETHOD','mail','_MD_AM_MAILERMETHODDESC','select','text',4)");
    $dbm->insert('config', " VALUES (65,0,6,'smtphost','_MD_AM_SMTPHOST','a:1:{i:0;s:0:\"\";}', '_MD_AM_SMTPHOSTDESC','textarea','array',6)");
    $dbm->insert('config', " VALUES (66,0,6,'smtpuser','_MD_AM_SMTPUSER','','_MD_AM_SMTPUSERDESC','textbox','text',7)");
    $dbm->insert('config', " VALUES (67,0,6,'smtppass','_MD_AM_SMTPPASS','','_MD_AM_SMTPPASSDESC','password','text',8)");
    $dbm->insert('config', " VALUES (68,0,6,'sendmailpath','_MD_AM_SENDMAILPATH','/usr/sbin/sendmail','_MD_AM_SENDMAILPATHDESC','textbox','text',5)");
    $dbm->insert('config', " VALUES (69,0,6,'from','_MD_AM_MAILFROM','','_MD_AM_MAILFROMDESC','textbox','text', 1)");
    $dbm->insert('config', " VALUES (70,0,6,'fromname','_MD_AM_MAILFROMNAME','','_MD_AM_MAILFROMNAMEDESC','textbox','text',2)");
    $dbm->insert('config', " VALUES (71, 0, 1, 'sslloginlink', '_MD_AM_SSLLINK', 'https://', '_MD_AM_SSLLINKDSC', 'textbox', 'text', 33)");
    $dbm->insert('config', " VALUES (72, 0, 1, 'theme_set_allowed', '_MD_AM_THEMEOK', '" . serialize(array($defaultTheme)) . "', '_MD_AM_THEMEOKDSC', 'theme_multi', 'array', 13)");
    // RMV-NOTIFY... Need to specify which user is sender of notification PM
    $dbm->insert('config', " VALUES (73,0,6,'fromuid','_MD_AM_MAILFROMUID','1','_MD_AM_MAILFROMUIDDESC','user','int',3)");

    $dbm->insert('config', " VALUES (74,0,7,'auth_method','_MD_AM_AUTHMETHOD','xoops','_MD_AM_AUTHMETHODDESC','select','text',1)");
    $dbm->insert('config', " VALUES (75,0,7,'ldap_port','_MD_AM_LDAP_PORT','389','_MD_AM_LDAP_PORT','textbox','int',2)");
    $dbm->insert('config', " VALUES (76,0,7,'ldap_server','_MD_AM_LDAP_SERVER','your directory server','_MD_AM_LDAP_SERVER_DESC','textbox','text',3)");
    $dbm->insert('config', " VALUES (77,0,7,'ldap_base_dn','_MD_AM_LDAP_BASE_DN','dc=xoops,dc=org','_MD_AM_LDAP_BASE_DN_DESC','textbox','text',4)");
    $dbm->insert('config', " VALUES (78,0,7,'ldap_manager_dn','_MD_AM_LDAP_MANAGER_DN','manager_dn','_MD_AM_LDAP_MANAGER_DN_DESC','textbox','text',5)");
    $dbm->insert('config', " VALUES (79,0,7,'ldap_manager_pass','_MD_AM_LDAP_MANAGER_PASS','manager_pass','_MD_AM_LDAP_MANAGER_PASS_DESC','password','text',6)");
    $dbm->insert('config', " VALUES (80,0,7,'ldap_version','_MD_AM_LDAP_VERSION','3','_MD_AM_LDAP_VERSION_DESC','textbox','text', 7)");
    $dbm->insert('config', " VALUES (81,0,7,'ldap_users_bypass','_MD_AM_LDAP_USERS_BYPASS','" . serialize(array('admin')) . "','_MD_AM_LDAP_USERS_BYPASS_DESC','textarea','array',8)");
    $dbm->insert('config', " VALUES (82,0,7,'ldap_loginname_asdn','_MD_AM_LDAP_LOGINNAME_ASDN','uid_asdn','_MD_AM_LDAP_LOGINNAME_ASDN_D','yesno','int',9)");
    $dbm->insert('config', " VALUES (83,0,7,'ldap_loginldap_attr', '_MD_AM_LDAP_LOGINLDAP_ATTR', 'uid', '_MD_AM_LDAP_LOGINLDAP_ATTR_D', 'textbox', 'text', 10)");
    $dbm->insert('config', " VALUES (84,0,7,'ldap_filter_person','_MD_AM_LDAP_FILTER_PERSON','','_MD_AM_LDAP_FILTER_PERSON_DESC','textbox','text',11)");
    $dbm->insert('config', " VALUES (85,0,7,'ldap_domain_name','_MD_AM_LDAP_DOMAIN_NAME','mydomain','_MD_AM_LDAP_DOMAIN_NAME_DESC','textbox','text',12)");
    $dbm->insert('config', " VALUES (86,0,7,'ldap_provisionning','_MD_AM_LDAP_PROVIS','0','_MD_AM_LDAP_PROVIS_DESC','yesno','int',13)");
    $dbm->insert('config', " VALUES (87,0,7,'ldap_provisionning_group','_MD_AM_LDAP_PROVIS_GROUP','a:1:{i:0;s:1:\"2\";}','_MD_AM_LDAP_PROVIS_GROUP_DSC','group_multi','array',14)");

    $dbm->insert('config', " VALUES (88,0,7,'ldap_mail_attr','_MD_AM_LDAP_MAIL_ATTR','mail','_MD_AM_LDAP_MAIL_ATTR_DESC','textbox','text',15)");
    $dbm->insert('config', " VALUES (89,0,7,'ldap_givenname_attr','_MD_AM_LDAP_GIVENNAME_ATTR','givenname','_MD_AM_LDAP_GIVENNAME_ATTR_DSC','textbox','text',16)");
    $dbm->insert('config', " VALUES (90,0,7,'ldap_surname_attr','_MD_AM_LDAP_SURNAME_ATTR','sn','_MD_AM_LDAP_SURNAME_ATTR_DESC','textbox','text',17)");
    $dbm->insert('config', " VALUES (91,0,7,'ldap_field_mapping','_MD_AM_LDAP_FIELD_MAPPING_ATTR','email=mail|name=displayname','_MD_AM_LDAP_FIELD_MAPPING_DESC','textarea','text',18)");
    $dbm->insert('config', " VALUES (92,0,7,'ldap_provisionning_upd', '_MD_AM_LDAP_PROVIS_UPD', '1', '_MD_AM_LDAP_PROVIS_UPD_DESC', 'yesno', 'int', 19)");
    $dbm->insert('config', " VALUES (93,0,7,'ldap_use_TLS','_MD_AM_LDAP_USETLS','0','_MD_AM_LDAP_USETLS_DESC','yesno','int', 20)");

    $dbm->insert('config', " VALUES (94, 0, 1, 'cpanel', '_MD_AM_CPANEL', 'transition', '_MD_AM_CPANELDSC', 'cpanel', 'other', 11)");
    $dbm->insert('config', " VALUES (95, 0, 2, 'welcome_type', '_MD_AM_WELCOMETYPE', '1', '_MD_AM_WELCOMETYPE_DESC', 'select', 'int', 3)");

    // Module System
    $dbm->insert('config', " VALUES (96, 1, 0, 'break1', '_MI_SYSTEM_PREFERENCE_BREAK_GENERAL', 'head', '', 'line_break', 'textbox', 0)");
    $dbm->insert('config', " VALUES (97, 1, 0, 'usetips', '_MI_SYSTEM_PREFERENCE_TIPS', '1', '_MI_SYSTEM_PREFERENCE_TIPS_DSC', 'yesno', 'int', 10)");
    $dbm->insert('config', " VALUES (98, 1, 0, 'typeicons', '_MI_SYSTEM_PREFERENCE_ICONS', 'transition', '', 'select', 'text', 20)");
    $dbm->insert('config', " VALUES (99, 1, 0, 'typebreadcrumb', '_MI_SYSTEM_PREFERENCE_BREADCRUMB', 'default', '', 'select', 'text', 30)");
    $dbm->insert('config', " VALUES (100, 1, 0, 'break2', '_MI_SYSTEM_PREFERENCE_BREAK_ACTIVE', 'head', '', 'line_break', 'textbox', 40)");
    $dbm->insert('config', " VALUES (101, 1, 0, 'active_avatars', '_MI_SYSTEM_PREFERENCE_ACTIVE_AVATARS', '1', '', 'yesno', 'int', 50)");
    $dbm->insert('config', " VALUES (102, 1, 0, 'active_banners', '_MI_SYSTEM_PREFERENCE_ACTIVE_BANNERS', '1', '', 'yesno', 'int', 60)");
    $dbm->insert('config', " VALUES (103, 1, 0, 'active_blocksadmin', '_MI_SYSTEM_PREFERENCE_ACTIVE_BLOCKSADMIN', '1', '', 'hidden', 'int', 70)");
    $dbm->insert('config', " VALUES (104, 1, 0, 'active_comments', '_MI_SYSTEM_PREFERENCE_ACTIVE_COMMENTS', '1', '', 'yesno', 'int', 80)");
    $dbm->insert('config', " VALUES (105, 1, 0, 'active_filemanager', '_MI_SYSTEM_PREFERENCE_ACTIVE_FILEMANAGER', '1', '', 'yesno', 'int', 90)");
    $dbm->insert('config', " VALUES (106, 1, 0, 'active_groups', '_MI_SYSTEM_PREFERENCE_ACTIVE_GROUPS', '1', '', 'hidden', 'int', 100)");
    $dbm->insert('config', " VALUES (107, 1, 0, 'active_images', '_MI_SYSTEM_PREFERENCE_ACTIVE_IMAGES', '1', '', 'yesno', 'int', 110)");
    $dbm->insert('config', " VALUES (108, 1, 0, 'active_mailusers', '_MI_SYSTEM_PREFERENCE_ACTIVE_MAILUSERS', '1', '', 'yesno', 'int', 120)");
    $dbm->insert('config', " VALUES (109, 1, 0, 'active_modulesadmin', '_MI_SYSTEM_PREFERENCE_ACTIVE_MODULESADMIN', '1', '', 'hidden', 'int', 130)");
    $dbm->insert('config', " VALUES (110, 1, 0, 'active_maintenance', '_MI_SYSTEM_PREFERENCE_ACTIVE_MAINTENANCE', '1', '', 'yesno', 'int', 140)");
    $dbm->insert('config', " VALUES (111, 1, 0, 'active_preferences', '_MI_SYSTEM_PREFERENCE_ACTIVE_PREFERENCES', '1', '', 'hidden', 'int', 150)");
    $dbm->insert('config', " VALUES (112, 1, 0, 'active_smilies', '_MI_SYSTEM_PREFERENCE_ACTIVE_SMILIES', '1', '', 'yesno', 'int', 160)");
    $dbm->insert('config', " VALUES (113, 1, 0, 'active_tplsets', '_MI_SYSTEM_PREFERENCE_ACTIVE_TPLSETS', '1', '', 'hidden', 'int', 170)");
    $dbm->insert('config', " VALUES (114, 1, 0, 'active_userrank', '_MI_SYSTEM_PREFERENCE_ACTIVE_USERRANK', '1', '', 'yesno', 'int', 180)");
    $dbm->insert('config', " VALUES (115, 1, 0, 'active_users', '_MI_SYSTEM_PREFERENCE_ACTIVE_USERS', '1', '', 'yesno', 'int', 190)");
    $dbm->insert('config', " VALUES (116, 1, 0, 'break3', '_MI_SYSTEM_PREFERENCE_BREAK_PAGER', 'head', '', 'line_break', 'textbox', 200)");
    $dbm->insert('config', " VALUES (117, 1, 0, 'avatars_pager', '_MI_SYSTEM_PREFERENCE_AVATARS_PAGER', '10', '', 'textbox', 'int', 210)");
    $dbm->insert('config', " VALUES (118, 1, 0, 'banners_pager', '_MI_SYSTEM_PREFERENCE_BANNERS_PAGER', '10', '', 'textbox', 'int', 220)");
    $dbm->insert('config', " VALUES (119, 1, 0, 'comments_pager', '_MI_SYSTEM_PREFERENCE_COMMENTS_PAGER', '20', '', 'textbox', 'int', 230)");
    $dbm->insert('config', " VALUES (120, 1, 0, 'groups_pager', '_MI_SYSTEM_PREFERENCE_GROUPS_PAGER', '15', '', 'textbox', 'int', 240)");
    $dbm->insert('config', " VALUES (121, 1, 0, 'images_pager', '_MI_SYSTEM_PREFERENCE_IMAGES_PAGER', '15', '', 'textbox', 'int', 250)");
    $dbm->insert('config', " VALUES (122, 1, 0, 'smilies_pager', '_MI_SYSTEM_PREFERENCE_SMILIES_PAGER', '20', '', 'textbox', 'int', 260)");
    $dbm->insert('config', " VALUES (123, 1, 0, 'userranks_pager', '_MI_SYSTEM_PREFERENCE_USERRANKS_PAGER', '20', '', 'textbox', 'int', 270)");
    $dbm->insert('config', " VALUES (124, 1, 0, 'users_pager', '_MI_SYSTEM_PREFERENCE_USERS_PAGER', '20', '', 'textbox', 'int', 280)");
    $dbm->insert('config', " VALUES (125, 1, 0, 'break4', '_MI_SYSTEM_PREFERENCE_BREAK_EDITOR', 'head', '', 'line_break', 'textbox', 290)");
    $dbm->insert('config', " VALUES (126, 1, 0, 'blocks_editor', '_MI_SYSTEM_PREFERENCE_BLOCKS_EDITOR', 'dhtmltextarea', '_MI_SYSTEM_PREFERENCE_BLOCKS_EDITOR_DSC', 'select', 'text', 300)");
    $dbm->insert('config', " VALUES (127, 1, 0, 'comments_editor', '_MI_SYSTEM_PREFERENCE_COMMENTS_EDITOR', 'dhtmltextarea', '_MI_SYSTEM_PREFERENCE_COMMENTS_EDITOR_DSC', 'select', 'text', 310)");
    $dbm->insert('config', " VALUES (128, 1, 0, 'general_editor', '_MI_SYSTEM_PREFERENCE_GENERAL_EDITOR', 'dhtmltextarea', '_MI_SYSTEM_PREFERENCE_GENERAL_EDITOR_DSC', 'select', 'text', 320)");
    $dbm->insert('config', " VALUES (129, 1, 0, 'redirect', '_MI_SYSTEM_PREFERENCE_REDIRECT', 'admin.php?fct=preferences', '', 'hidden', 'text', 330)");
    $dbm->insert('config', " VALUES (130, 1, 0, 'com_anonpost', '_MI_SYSTEM_PREFERENCE_ANONPOST', '', '', 'hidden', 'text', 340)");
    $dbm->insert('config', " VALUES (133, 1, 0, 'jquery_theme', '_MI_SYSTEM_PREFERENCE_JQUERY_THEME', 'base', '', 'select', 'text', 35)");

    $dbm->insert('config', " VALUES (134, 0, 1, 'redirect_message_ajax', '_MD_AM_CUSTOM_REDIRECT', '1', '_MD_AM_CUSTOM_REDIRECT_DESC', 'yesno', 'int', 12)");

    require_once '../class/xoopslists.php';
    $editors = XoopsLists::getDirListAsArray('../class/xoopseditor');
    $conf    = 35;
    foreach ($editors as $dir) {
        $dbm->insert('configoption', ' VALUES (' . $conf . ", '" . $dir . "', '" . $dir . "', 126)");
        ++$conf;
    }
    foreach ($editors as $dir) {
        $dbm->insert('configoption', ' VALUES (' . $conf . ", '" . $dir . "', '" . $dir . "', 127)");
        ++$conf;
    }
    foreach ($editors as $dir) {
        $dbm->insert('configoption', ' VALUES (' . $conf . ", '" . $dir . "', '" . $dir . "', 128)");
        ++$conf;
    }
    $icons = XoopsLists::getDirListAsArray('../modules/system/images/icons');
    foreach ($icons as $dir) {
        $dbm->insert('configoption', ' VALUES (' . $conf . ", '" . $dir . "', '" . $dir . "', 98)");
        ++$conf;
    }
    $breadcrumb = XoopsLists::getDirListAsArray('../modules/system/images/breadcrumb');
    foreach ($breadcrumb as $dir) {
        $dbm->insert('configoption', ' VALUES (' . $conf . ", '" . $dir . "', '" . $dir . "', 99)");
        ++$conf;
    }
    $jqueryui = XoopsLists::getDirListAsArray('../modules/system/css/ui');
    foreach ($jqueryui as $dir) {
        $dbm->insert('configoption', ' VALUES (' . $conf . ", '" . $dir . "', '" . $dir . "', 133)");
        ++$conf;
    }

    return $groups;
}
