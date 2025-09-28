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
 * @copyright    (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
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

$vars = & $_SESSION['settings'];

require_once __DIR__ . '/../mainfile.php';
require_once __DIR__ . '/class/dbmanager.php';
if (!defined('_XOOPS_FATAL_MESSAGE')) {
    include_once(XOOPS_ROOT_PATH . '/include/defines.php');
}
if (!defined('_DB_QUERY_ERROR')) {
    if (file_exists(XOOPS_ROOT_PATH . "/language/{$wizard->language}/global.php")) {
        include_once(XOOPS_ROOT_PATH . "/language/{$wizard->language}/global.php");
    } else {
        include_once(XOOPS_ROOT_PATH . '/language/english/global.php');
    }
}
if (!function_exists('xoops_loadLanguage')) {
    function xoops_loadLanguage($name, $domain = '', $language = null)
    {
        // This may get called even though we loaded the required language files.
        // This function just needs to exist to keep all things happy.
    }
}

$dbm = new Db_manager();

if (!$dbm->isConnectable()) {
    $wizard->redirectToPage('dbsettings');
    exit();
}

$sql = 'SELECT COUNT(*) FROM ' . $dbm->db->prefix('users');
$result = $dbm->query($sql);
if (!$dbm->db->isResultSet($result)) {
    throw new \RuntimeException(
        \sprintf(_DB_QUERY_ERROR, $sql) . $dbm->db->error(),
        E_USER_ERROR,
    );
}

if (!$result) {
    $wizard->redirectToPage('dbsettings');
    exit();
}

[$count] = $dbm->db->fetchRow($result);
$process = (0 == $count);
$update  = false;

extract($_SESSION['siteconfig'], EXTR_SKIP);

require_once __DIR__ . '/include/makedata.php';

$wizard->loadLangFile('install2');

$licenseFile = XOOPS_VAR_PATH . '/data/license.php';
$touched = touch($licenseFile);
if ($touched) {
    $licenseReport = '<div class="alert alert-success"><span class="fa-solid fa-check text-success"></span> '
        . writeLicenseKey() . '</div>';
} else {
    $licenseReport = '<div class="alert alert-danger"><span class="fa-solid fa-ban text-danger"></span> '
        . sprintf(LICENSE_NOT_WRITEABLE, $licenseFile) . '</div>';
}
$error = false;

$hashedAdminPass = password_hash($adminpass, PASSWORD_DEFAULT);

if ($process) {
    $result  = $dbm->queryFromFile('./sql/' . XOOPS_DB_TYPE . '.data.sql');
    $result  = $dbm->queryFromFile('./language/' . $wizard->language . '/' . XOOPS_DB_TYPE . '.lang.data.sql');
    $group   = make_groups($dbm);
    $result  = make_data($dbm, $adminname, $hashedAdminPass, $adminmail, $wizard->language, $group);
    $content = '<div class="alert alert-success"><span class="fa-solid fa-check text-success"></span> '
        . DATA_INSERTED . '</div><div class="well">' . $dbm->report() . '</div>';
} else {
    $content = '<div class="alert alert-info"><span class="fa-solid fa-circle-info text-info"></span> '
        . DATA_ALREADY_INSERTED . '</div>';
}
$content .= $licenseReport;

xoops_setcookie('xo_install_user', '', time() - 60 * 60 * 12);
if (!empty($_SESSION['settings']['authorized']) && !empty($adminname) && !empty($adminpass)) {
    $claims = [
        'uname' => $adminname,
        'sub' => 'xoopsinstall',
    ];
    $token = \Xmf\Jwt\TokenFactory::build('install', $claims, 60 * 60);

    xoops_setcookie('xo_install_user', $token, 0, null, null, null, true);
}

include __DIR__ . '/include/install_tpl.php';
