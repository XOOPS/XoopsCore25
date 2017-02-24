<?php
/**
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

$xoopsOption['checkadmin'] = true;
$xoopsOption['hascommon']  = true;
require_once './include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

if (!@include_once "../language/{$wizard->language}/global.php") {
    include_once '../language/english/global.php';
}
if (!@include_once "../modules/system/language/{$wizard->language}/admin/modulesadmin.php") {
    include_once '../modules/system/language/english/admin/modulesadmin.php';
}
if (!@include_once "../modules/system/language/{$wizard->language}/admin.php") {
    include_once '../modules/system/language/english/admin.php';
}
require_once '../class/xoopsformloader.php';
require_once '../class/xoopslists.php';

$pageHasForm = true;
$pageHasHelp = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once '../class/xoopsblock.php';
    include_once '../kernel/module.php';
    include_once '../include/cp_functions.php';
    include_once '../include/version.php';
    include_once './include/modulesadmin.php';

    /* @var $config_handler XoopsConfigHandler  */
    $config_handler = xoops_getHandler('config');
    $xoopsConfig    = $config_handler->getConfigsByCat(XOOPS_CONF);

    $msgs = array();
    foreach ($_REQUEST['modules'] as $dirname => $installmod) {
        if ($installmod) {
            $msgs[] = xoops_module_install($dirname);
        }
    }

    $pageHasForm = false;

    if (count($msgs) > 0) {
        $content = '<div class="alert alert-success"><span class="fa fa-check text-success"></span> '
            . INSTALLED_MODULES . '</div><div class="well"><ul class="list-unstyled">';
        foreach ($msgs as $msg) {
            $noAnchors = preg_replace(array('"<a (.*?)>"', '"</a>"'), array('',''), $msg);
            $content .= "<li>{$noAnchors}</li>";
        }
        $content .= '</ul></div>';
    } else {
        $content = '<div class="alert alert-info"><span class="fa fa-info-circle text-info"></span> ' . NO_INSTALLED_MODULES . '</div>';
    }

    // Flush cache files for cpanel GUIs
    xoops_load('cpanel', 'system');
    XoopsSystemCpanel::flush();

    //Set active modules in cache folder
    xoops_setActiveModules();
} else {
    if (!isset($GLOBALS['xoopsConfig']['language'])) {
        $GLOBALS['xoopsConfig']['language'] = $_COOKIE['xo_install_lang'];
    }

    // Get installed modules
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $installed_mods = $module_handler->getObjects();
    $listed_mods    = array();
    foreach ($installed_mods as $module) {
        $listed_mods[] = $module->getVar('dirname');
    }

    include_once '../class/xoopslists.php';
    $dirlist  = XoopsLists::getModulesList();
    $toinstal = 0;

    $javascript = '';
    $content  = '';
    $content .= '<div class="panel panel-info">';
    $content .= '<div class="panel-heading">' . MODULES_AVAILABLE . '</div>';
    $content .= '<div class="panel-body">';

    foreach ($dirlist as $file) {
        clearstatcache();
        if (!in_array($file, $listed_mods)) {
            $value = 0;
            $style = '';
            if (in_array($file, $wizard->configs['modules'])) {
                $value = 1;
                $style = " style='background-color:#E6EFC2;'";
            }

            $file   = trim($file);
            $module = $module_handler->create();
            if (!$module->loadInfo($file, false)) {
                continue;
            }

            $form     = new XoopsThemeForm('', 'modules', 'index.php', 'post');
            $moduleYN = new XoopsFormRadio('', 'modules[' . $module->getInfo('dirname') . ']', $value);
            $moduleYN->addOption(1, sprintf(INSTALL_THIS_MODULE, $module->getInfo('name')));
            $moduleYN->setExtra("onclick='selectModule(\"" . $file . "\", this)'");
            $form->addElement($moduleYN);
/*
            $content .= "<tr id='" . $file . "'" . $style . ">\n";
            $content .= "    <td class='img' ><img src='" . XOOPS_URL . '/modules/' . $module->getInfo('dirname') . '/' . $module->getInfo('image') . "' alt='" . $module->getInfo('name') . "'/></td>\n";
            $content .= '    <td>';
            $content .= '        ' . $module->getInfo('name') . '&nbsp;' . number_format(round($module->getInfo('version'), 2), 2) . '&nbsp;(' . $module->getInfo('dirname') . ')';
            $content .= '        <br>' . $module->getInfo('description');
            $content .= "    </td>\n";
            $content .= "    <td class='yesno'>";
            $content .= $moduleYN->render();
            $content .= "    </td></tr>\n";
*/
            $content .= '<div class="row module-row" id="' . $file . '">';
            $content .= '<div class="col-md-2">';
            $content .= '<br><img src="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname')
                . '/' . $module->getInfo('image') . '" alt="' . $module->getInfo('name') . '">';
            $content .= '</div>';
            $content .= '<div class="col-md-7">';
            $content .= '<h3>' . $module->getInfo('name');
            $content .= ' <small> ' . number_format(round($module->getInfo('version'), 2), 2)
                . ' (' . $module->getInfo('dirname') . ')' . '</small>' . '</h3>';
            $content .= '<i>' . $module->getInfo('description') . '</i>';
            $content .= '</div>';
            $content .= '<div class="col-md-3"><br><br><br>' . $moduleYN->render() . '</div>';
            $content .= '</div>';

            ++$toinstal;
        }
    }
    $content .= '</div></div>';
    $content .= "<script type='text/javascript'>" . $javascript . '</script>';
    if ($toinstal == 0) {
        $pageHasForm = false;
        $content     = '<div class="alert alert-info"><span class="fa fa-info-circle text-info"></span> ' . NO_MODULES_FOUND . '</div>';
    }
}

include './include/install_tpl.php';
