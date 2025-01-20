<?php declare(strict_types=1);

/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since       2.3.0
 * @author      Haruki Setoyama  <haruki@planewave.org>
 * @author      Kazumi Ono <webmaster@myweb.ne.jp>
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @author      DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

use Xmf\Module\Admin;

require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$xoopsOption['checkadmin'] = true;
$xoopsOption['hascommon']  = true;
require_once \dirname(__DIR__) . '/include/common.inc.php';
require_once XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php';
//defined('XOOPS_INSTALL') || exit('XOOPS Installation wizard die');

xoops_loadLanguage('global');
xoops_loadLanguage('admin/modulesadmin', 'system');

require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
XoopsLoad::load('XoopsLists');

//$xoTheme->addStylesheet( XOOPS_URL . "/modules/" . $xoopsModule->getVar("dirname") . "/assets/css/style.css" );

$pageHasForm = true;
$pageHasHelp = false;

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
    require_once XOOPS_ROOT_PATH . '/kernel/module.php';
    require_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
    require_once XOOPS_ROOT_PATH . '/include/version.php';
    //    require_once  \dirname(__DIR__) . '/include/modulesadmin.php';

    /** @var \XoopsConfigHandler $configHandler */
    $configHandler = xoops_getHandler('config');
    $xoopsConfig   = $configHandler->getConfigsByCat(XOOPS_CONF);

    $msgs = [];
    foreach ($_REQUEST['modules'] as $dirname => $installmod) {
        if ($installmod) {
            $msgs[] = xoops_module_install($dirname);
        }
    }

    $pageHasForm = false;

    if (count($msgs) > 0) {
        $content = "<div class='x2-note successMsg'>" . INSTALLED_MODULES . "</div><ul class='log'>";
        foreach ($msgs as $msg) {
            $content .= "<dt>{$msg}</dt>";
        }
        $content .= '</ul>';
    } else {
        $content = "<div class='x2-note confirmMsg'>" . NO_INSTALLED_MODULES . '</div>';
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
    /** @var \XoopsModuleHandler $moduleHandler */
    $moduleHandler  = xoops_getHandler('module');
    $installed_mods = $moduleHandler->getObjects();
    $listed_mods    = [];
    if ((is_countable($installed_mods) ? count($installed_mods) : 0) > 0) {
        foreach ($installed_mods as $module) {
            $listed_mods[] = $module->getVar('dirname');
        }
    }

    XoopsLoad::load('XoopsLists');
    $dirlist  = \XoopsLists::getModulesList();
    $toinstal = 0;

    $javascript = '';
    $content    = "<ul class='log'><li>";
    $content    .= "<table class='module'>\n";
    //    $content .= "<input type='button' name='getTotal1' id='getTotal1' value='Select All' onclick='selectAll();'> <input type='button' name='getTotal1' id='getTotal1' value='Unselect All' onclick='unselectAll();'><br>";

    if (!isset($wizard->configs['modules'])) {
        foreach ($dirlist as $file) {
            clearstatcache();
            if (!in_array($file, $listed_mods, true)) {
                $value = 0;
                $style = '';
                if (isset($wizard->configs['modules']) && in_array($file, $wizard->configs['modules'], true)) {
                    $value = 1;
                    $style = " style='background-color:#E6EFC2;'";
                }

                $file   = trim((string) $file);
                $module = $moduleHandler->create();
                if (!$module->loadInfo($file, false)) {
                    continue;
                }

                $form     = new \XoopsThemeForm('', 'modules', 'index.php', 'post', true);
                $moduleYN = new \XoopsFormRadioYN('', 'modules[' . $module->getInfo('dirname') . ']', $value, _YES, _NO);
                $moduleYN->setExtra("onclick='selectModule(\"" . $file . "\", this)'");
                $form->addElement($moduleYN);

                $content .= "<tr id='" . $file . "'" . $style . ">\n";
                $content .= "    <td class='img' ><img src='" . XOOPS_URL . '/modules/' . $module->getInfo('dirname') . '/' . $module->getInfo('image') . "' alt='" . $module->getInfo('name') . "'></td>\n";
                $content .= '    <td>';
                $content .= '        ' . $module->getInfo('name') . '&nbsp;' . $module->getInfo('version') . '&nbsp;' . $module->getInfo('module_status') . '&nbsp;(folder: /' . $module->getInfo('dirname') . ')';
                $content .= '        <br>' . $module->getInfo('description');
                $content .= "    </td>\n";
                $content .= "    <td class='yesno'>";
                $content .= $moduleYN->render();
                $content .= "    </td></tr>\n";
                ++$toinstal;
            }
        }
    }
    $content .= '</table>';
    $content .= "</li></ul><script type='text/javascript'>" . $javascript . '</script>';
    if (0 == $toinstal) {
        $pageHasForm = false;
        $content     = "<div class='x2-note confirmMsg'>" . NO_MODULES_FOUND . '</div>';
    }
}

$adminObject = Admin::getInstance();
$adminObject->displayNavigation(basename(__FILE__));

$adminObject->addItemButton(_AM_INSTALLER_SELECT_ALL, 'javascript:selectAll();', 'button_ok');

$adminObject->addItemButton(_AM_INSTALLER_SELECT_NONE, 'javascript:unselectAll();', 'prune');

$adminObject->displayButton('left', '');

require_once \dirname(__DIR__) . '/include/install_tpl.php';
require_once __DIR__ . '/admin_footer.php';
