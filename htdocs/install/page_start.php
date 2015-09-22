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
 * Installer introduction page
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright    (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license          http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 * @version          $Id: page_start.php 13082 2015-06-06 21:59:41Z beckmi $
 **/

require_once './include/common.inc.php';
defined('XOOPS_INSTALL') or die('XOOPS Installation wizard die');

$pageHasForm = false;

$content = "";
include "./language/{$wizard->language}/welcome.php";

$writable = "<ul class='confirmMsg'>";
foreach ($wizard->configs['writable'] as $key => $value) {
    if (is_dir('../' . $value)) {
        $writable .= "<li class='directory'>$value</li>";
    } else {
        $writable .= "<li class='files'>$value</li>";
    }
}
$writable .= "</ul>";

$xoops_trust = "<ul class='confirmMsg'>";
foreach ($wizard->configs['xoopsPathDefault'] as $key => $value) {
    $xoops_trust .= "<li class='directory'>$value</li>";
}
$xoops_trust .= "</ul>";

$writable_trust = "<ul class='confirmMsg'>";
foreach ($wizard->configs['dataPath'] as $key => $value) {
    $writable_trust .= "<li class='directory'>" . $wizard->configs['xoopsPathDefault']['data'] . '/' . $key . "</li>";
    if (is_array($value)) {
        foreach ($value as $key2 => $value2) {
            $writable_trust .= "<li class='directory'>" . $wizard->configs['xoopsPathDefault']['data'] . '/' . $key . '/' . $value2 . "</li>";
        }
    }
}
$writable_trust .= "</ul>";

$content = sprintf($content, $writable, $xoops_trust, $writable_trust);

include './include/install_tpl.php';
