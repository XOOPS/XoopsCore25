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
 **/

require_once __DIR__ . '/include/common.inc.php';
include_once __DIR__ . '/../class/xoopsload.php';
include_once __DIR__ . '/../class/preload.php';
include_once __DIR__ . '/../class/database/databasefactory.php';
include_once __DIR__ . '/../class/logger/xoopslogger.php';

$_SESSION = array();
xoops_setcookie('xo_install_user', '', null, null, null);
$key = \Xmf\Jwt\KeyFactory::build('install');
$key->kill();
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

$install_rename_suffix = uniqid(substr(md5($x = mt_rand()) . $x, -10), true);
$installer_modified    = 'install_remove_' . $install_rename_suffix;

$pageHasForm = false;

$content = '';
include __DIR__ . "/language/{$wizard->language}/finish.php";

include __DIR__ . '/include/install_tpl.php';
