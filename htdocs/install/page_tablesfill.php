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
 * Installer db inserting page
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 */

require_once './include/common.inc.php';
defined('XOOPS_INSTALL') or die('XOOPS Installation wizard die');

$pageHasForm = false;
$pageHasHelp = false;

$vars =& $_SESSION['settings'];

include_once '../mainfile.php';
include_once './class/dbmanager.php';
$dbm = new Db_manager();

if (!$dbm->isConnectable()) {
    $wizard->redirectToPage('dbsettings');
    exit();
}
$res = $dbm->query('SELECT COUNT(*) FROM ' . $dbm->db->prefix('users'));
if (!$res) {
    $wizard->redirectToPage('dbsettings');
    exit();
}

list($count) = $dbm->db->fetchRow($res);
$process = $count ? '' : 'insert';
$update  = false;

extract($_SESSION['siteconfig'], EXTR_SKIP);
if ($state = xoDiagIfWritable('include/license.php')) {
    if (!is_writable('../include/license.php')) {
    }
}

$hashedAdminPass = password_hash($adminpass, PASSWORD_DEFAULT);
if ($process && is_writable('../include/license.php')) {
    include_once './include/makedata.php';
    //$cm = 'dummy';
    $wizard->loadLangFile('install2');
    $language = $wizard->language;

    $result  = $dbm->queryFromFile('./sql/' . XOOPS_DB_TYPE . '.data.sql');
    $result  = $dbm->queryFromFile('./language/' . $language . '/' . XOOPS_DB_TYPE . '.lang.data.sql');
    $group   = make_groups($dbm);
    $result  = make_data($dbm, $adminname, $hashedAdminPass, $adminmail, $language, $group);
    $content = '<div class="x2-note successMsg">' . DATA_INSERTED . '</div><br />' . $dbm->report();
    // Writes License Key
    $content .= '<div class="x2-note successMsg">' . sprintf(LICENSE_IS_WRITEABLE, $state) . '</div>';
    $content .= '<div class="x2-note successMsg">' . write_key() . '</div><br />';
} elseif ($update) {
    $sql = "UPDATE " . $dbm->db->prefix("users")
        . " SET `uname` = " . $dbm->db->quote($adminname)
        . ", `email` = " . $dbm->db->quote($adminmail)
        . ", `user_regdate` = '" . time() . "'"
        . ", `pass` = " . $dbm->db->quote($hashedAdminPass)
        . ", `last_login` = '" . time() . "' "
        . "WHERE uid = 1";
    $dbm->db->queryF($sql);
    $content = '';
} elseif (!is_writable('../include/license.php')) {
    include_once './include/makedata.php';
    //$cm = 'dummy';
    $wizard->loadLangFile('install2');

    $content .= '<div class="x2-note errorMsg">' . sprintf(LICENSE_NOT_WRITEABLE, $state) . '</div>';
} else {
    $content = "<div class='x2-note confirmMsg'>" . DATA_ALREADY_INSERTED . '</div>';
}

setcookie('xo_install_user', '', null, null, null);
if (!empty($_SESSION['settings']['authorized']) && !empty($adminname) && !empty($adminpass)) {
    $claims = array(
        'uname' => $adminname,
        'sub' => 'xoopsinstall',
    );
    $token = \Xmf\Jwt\TokenFactory::build('install', $claims, 60*15);

    setcookie('xo_install_user', $token, 0, null, null, null, true);
}

include './include/install_tpl.php';
