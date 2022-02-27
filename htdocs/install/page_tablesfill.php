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
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 */

require_once __DIR__ . '/include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

$pageHasForm = false;
$pageHasHelp = false;

$vars =& $_SESSION['settings'];

include_once dirname(__DIR__) . '/mainfile.php';
include_once  __DIR__ . '/class/dbmanager.php';
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
$process = ($count == 0);
$update  = false;

extract($_SESSION['siteconfig'], EXTR_SKIP);

include_once __DIR__ . '/include/makedata.php';
//$cm = 'dummy';
$wizard->loadLangFile('install2');

$licenseFile = XOOPS_VAR_PATH . '/data/license.php';
$touched = touch($licenseFile);
if ($touched) {
    $licenseReport = '<div class="alert alert-success"><span class="fa fa-check text-success"></span> '
        . writeLicenseKey() . '</div>';
} else {
    $licenseReport = '<div class="alert alert-danger"><span class="fa fa-ban text-danger"></span> '
        . sprintf(LICENSE_NOT_WRITEABLE, $licenseFile) . '</div>';
}
$error = false;

$hashedAdminPass = password_hash($adminpass, PASSWORD_DEFAULT);

if ($process) {
    $result  = $dbm->queryFromFile( __DIR__ . '/sql/' . XOOPS_DB_TYPE . '.data.sql');
    $result  = $dbm->queryFromFile( __DIR__ . '/language/' . $wizard->language . '/' . XOOPS_DB_TYPE . '.lang.data.sql');
    $group   = make_groups($dbm);
    $result  = make_data($dbm, $adminname, $hashedAdminPass, $adminmail, $wizard->language, $group);
    $content = '<div class="alert alert-success"><span class="fa fa-check text-success"></span> '
        . DATA_INSERTED . '</div><div class="well">' . $dbm->report() . '</div>';
} else {
    $content = '<div class="alert alert-info"><span class="fa fa-info-circle text-info"></span> '
        . DATA_ALREADY_INSERTED . '</div>';
}
$content .= $licenseReport;

xoops_setcookie('xo_install_user', '', time()-60*60*12);
if (!empty($_SESSION['settings']['authorized']) && !empty($adminname) && !empty($adminpass)) {
    $claims = array(
        'uname' => $adminname,
        'sub' => 'xoopsinstall',
    );
    $token = \Xmf\Jwt\TokenFactory::build('install', $claims, 60*60);

    xoops_setcookie('xo_install_user', $token, 0, null, null, null, true);
}

include __DIR__ . '/include/install_tpl.php';
