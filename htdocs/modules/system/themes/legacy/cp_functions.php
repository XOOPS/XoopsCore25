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
 * Xoops legacy cp_functions
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             system
 * @subpackage          class
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @param $tpl
 */

function xoops_legacy_cp_header($tpl)
{
    global $xoopsConfig, $xoopsUser, $xoTheme;

    xoops_loadLanguage('cpanel', 'system');
    $xoTheme->addStylesheet(XOOPS_URL . '/xoops.css');
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/themes/legacy/css/style.css');
    include XOOPS_CACHE_PATH . '/adminmenu.php';
    $moduleperm_handler = xoops_getHandler('groupperm');
    $admin_mids         = $moduleperm_handler->getItemIds('module_admin', $xoopsUser->getGroups());
    $xoTheme->addScript(XOOPS_URL . '/include/layersmenu.js');
    $xoTheme->addScript('', '', '
        var thresholdY = 15; // in pixels; threshold for vertical repositioning of a layer
        var ordinata_margin = 20; // to start the layer a bit above the mouse vertical coordinate');

    $xoTheme->addScript('', '', $xoops_admin_menu_js . '
        function moveLayers() { ' . $xoops_admin_menu_ml . ' }
        function shutdown() { ' . $xoops_admin_menu_sd . ' }
        if (NS4) {
            document.onmousedown = function() { shutdown(); }
        } else {
            document.onclick = function() { shutdown(); }
        }');
    foreach (array_keys($xoops_admin_menu_ft) as $adm) {
        if (in_array($adm, $admin_mids)) {
            $admin_menu['content'] = $xoops_admin_menu_ft[$adm];

            $tpl->append_by_ref('admin_menu', $admin_menu);
            unset($admin_menu);
        }
    }
}

function xoops_legacy_cp_footer()
{
    global $xoopsConfig, $xoopsLogger;

    include XOOPS_CACHE_PATH . '/adminmenu.php';
    echo $xoops_admin_menu_dv;
}

/**
 * @return string
 */
function xoops_legacy_module_get_admin_menu()
{
    /************************************************************
     * Based on:
     * - PHP Layers Menu 1.0.7(c)2001,2002 Marco Pratesi <pratesi@telug.it>
     * - TreeMenu 1.1 - Bjorge Dijkstra <bjorge@gmx.net>
     ************************************************************
     * - php code Optimized by DuGris
     ************************************************************/

    $left            = 105;
    $top             = 135;
    $js              = '';
    $moveLayers      = '';
    $shutdown        = '';
    $firstleveltable = '';
    $menu_layers     = '';

    $module_handler = xoops_getHandler('module');
    $criteria       = new CriteriaCompo();
    $criteria->add(new Criteria('hasadmin', 1));
    $criteria->add(new Criteria('isactive', 1));
    $criteria->setSort('mid');
    $mods = $module_handler->getObjects($criteria);

    foreach ($mods as $mod) {
        $mid         = $mod->getVar('mid');
        $module_name = $mod->getVar('name');
        $module_url  = "\".XOOPS_URL.\"/modules/" . $mod->getVar('dirname') . '/' . trim($mod->getInfo('adminindex'));
        $module_img  = "<img class='admin_layer_img' src='\".XOOPS_URL.\"/modules/" . $mod->getVar('dirname') . '/' . $mod->getInfo('image') . "' alt='' />";
        $module_desc = "<strong>\"._VERSION.\":</strong> " . round($mod->getVar('version') / 100, 2) . "<br /><strong>\"._DESCRIPTION.\":</strong> " . $mod->getInfo('description');

        $top += 15;

        $js .= "\nfunction popUpL" . $mid . "() {\n    shutdown();\n    popUp('L" . $mid . "',true);}";
        $moveLayers .= "\n    setleft('L" . $mid . "'," . $left . ");\n    settop('L" . $mid . "'," . $top . ');';
        $shutdown .= "\n    popUp('L" . $mid . "',false);";
        $firstleveltable .= "$" . 'xoops_admin_menu_ft[' . $mid . "] = \"<a href='" . $module_url . "' title='" . $module_name . "' onmouseover='moveLayerY(\\\"L" . $mid . "\\\", currentY, event) ; popUpL" . $mid . "(); ' >" . $module_img . "</a><br />\";\n";
        $menu_layers .= "\n<div id='L" . $mid . "' style='position: absolute; visibility: hidden; z-index:1000;' >\n<table class='admin_layer' cellpadding='0' cellspacing='0'>\n<tr><th nowrap='nowrap'>" . $module_name . "</th></tr>\n<tr><td class='even' nowrap='nowrap'>";

        $adminmenu = $mod->getAdminMenu();

        if ($mod->getVar('hasnotification') || ($mod->getInfo('config') && is_array($mod->getInfo('config'))) || ($mod->getInfo('comments') && is_array($mod->getInfo('comments')))) {
            $adminmenu[] = array(
                'link'     => '".XOOPS_URL."/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mid,
                'title'    => _PREFERENCES,
                'absolute' => true);
        }
        if (count($adminmenu) != 0) {
            $currenttarget = '';
            foreach ($adminmenu as $menuitem) {
                $menu_link   = trim($menuitem['link']);
                $menu_title  = trim($menuitem['title']);
                $menu_target = isset($menuitem['target']) ? " target='" . trim($menuitem['target']) . "'" : '';
                if (isset($menuitem['absolute']) && $menuitem['absolute']) {
                    $menu_link = empty($menu_link) ? '#' : $menu_link;
                } else {
                    $menu_link = empty($menu_link) ? '#' : "\".XOOPS_URL.\"/modules/" . $mod->getVar('dirname') . '/' . $menu_link;
                }

                $menu_layers .= "\n<img src='\".XOOPS_URL.\"/images/pointer.gif' width='8' height='8' alt='' />&nbsp;<a href='" . $menu_link . "'" . $menu_target . " onmouseover='popUpL" . $mid . "' >" . $menu_title . "</a><br />\n";
            }
        }

        $menu_layers .= "\n<div style='margin-top: 5px; font-size: smaller; text-align: right;'><a href='#' onmouseover='shutdown();'>[" . _CLOSE . "]</a></div></td></tr><tr><th style='font-size: smaller; text-align: left;'>" . $module_img . '<br />' . $module_desc . "</th></tr></table></div>\n";
    }

    $menu_layers .= "\n<script language='JavaScript' type='text/javascript'>\n<!--\nmoveLayers();\nloaded = 1;\n// -->\n</script>\n";

    $content = '<' . "?php\n";
    $content .= "\$xoops_admin_menu_js = \"" . $js . "\n\";\n\n";
    $content .= "\$xoops_admin_menu_ml = \"" . $moveLayers . "\n\";\n\n";
    $content .= "\$xoops_admin_menu_sd = \"" . $shutdown . "\n\";\n\n";
    $content .= $firstleveltable . "\n";
    $content .= "\$xoops_admin_menu_dv = \"" . $menu_layers . "\";\n";
    $content .= "\n?" . '>';

    return $content;
}

/**
 * @param $content
 *
 * @return bool
 */
function xoops_legacy_module_write_admin_menu($content)
{
    $filename = XOOPS_CACHE_PATH . '/adminmenu.php';
    if (!$file = fopen($filename, 'w')) {
        echo 'failed open file';

        return false;
    }
    if (fwrite($file, $content) == -1) {
        echo 'failed write file';

        return false;
    }
    fclose($file);

    return true;
}
