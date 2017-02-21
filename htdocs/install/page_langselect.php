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
 * Installer language selection page
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
 * @author           DuGris <dugris@frxoops.org>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

require_once './include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

setcookie('xo_install_lang', 'english', null, null, null);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_REQUEST['lang'])) {
    $lang = $_REQUEST['lang'];
    setcookie('xo_install_lang', $lang, null, null, null);

    $wizard->redirectToPage('+1');
    exit();
}

$_SESSION['settings'] = array();
setcookie('xo_install_user', '', null, null, null);

$pageHasForm = true;
$title = LANGUAGE_SELECTION;
$label = 'Available Languages';
$content =<<<EOT
<div class="form-group col-md-4">
    <label for="lang" class="control-label">{$label}</label>
    <select name="lang" id="lang" class="form-control">
EOT;

$languages = getDirList('./language/');
foreach ($languages as $lang) {
    $sel = ($lang == $wizard->language) ? ' selected' : '';
    $content .= "<option value=\"{$lang}\"{$sel}>{$lang}</option>\n";
}
$content .=<<<EOB
    </select>
</div><div class="clearfix"></div>
EOB;


include './include/install_tpl.php';
