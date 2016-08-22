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
 * Installer final page
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
 **/

require_once './include/common.inc.php';
include_once '../class/xoopsload.php';
include_once '../class/preload.php';
include_once '../class/database/databasefactory.php';
include_once '../class/logger/xoopslogger.php';

$_SESSION = array();
setcookie('xo_install_user', '', null, null, null);
$key = \Xmf\Jwt\KeyFactory::build('install');
$key->kill();
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

$install_rename_suffix = uniqid(substr(md5($x = mt_rand()) . $x, -10));
$installer_modified    = 'install_remove_' . $install_rename_suffix;

$pageHasForm = false;

$content = '';
include "./language/{$wizard->language}/finish.php";

include './include/install_tpl.php';
