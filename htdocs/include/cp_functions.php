<?php
/**
 * XOOPS control panel functions
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 */

define('XOOPS_CPFUNC_LOADED', 1);

/**
 * CP Header
 *
 */
function xoops_cp_header()
{
    xoops_load('cpanel', 'system');
    $cpanel = XoopsSystemCpanel::getInstance();
    $cpanel->gui->header();
}

/**
 * CP Footer
 *
 */
function xoops_cp_footer()
{
    xoops_load('cpanel', 'system');
    $cpanel = XoopsSystemCpanel::getInstance();
    $cpanel->gui->footer();
}

/**
 * Open Table: DO NOT USE
 *
 * We need these because theme files will not be included
 *
 */
function openTable()
{
    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";
}

/**
 * Cloe Table : NO NOT USE
 *
 */
function closeTable()
{
    echo '</td></tr></table>';
}

/**
 * Enclose Items in a table : DO NOT USE
 *
 * @param string $title
 * @param string $content
 */
function themecenterposts($title, $content)
{
    echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer"><tr><td class="head">' . $title . '</td></tr><tr><td><br>' . $content . '<br></td></tr></table>';
}

/**
 * Text Form : DO NOT USE
 *
 * @param unknown_type $url
 * @param unknown_type $value
 * @return unknown
 */
function myTextForm($url, $value)
{
    return '<form action="' . $url . '" method="post"><input type="submit" value="' . $value . '" /></form>';
}

/**
 * Enter description here...
 *
 * @return unknown
 */
function xoopsfwrite()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    } else {
    }
    if (!$GLOBALS['xoopsSecurity']->checkReferer()) {
        return false;
    } else {
    }

    return true;
}

/**
 * Xoops Module Menu
 * @deprecated
 * @return unknown
 */
function xoops_module_get_admin_menu()
{
    $GLOBALS['xoopsLogger']->addDeprecated(__FUNCTION__ . ' is deprecated, should not be used any longer');
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
    /* @var $module_handler XoopsModuleHandler */
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
        $module_desc = "<strong>\"._VERSION.\":</strong> " . round($mod->getVar('version') / 100, 2) . "<br><strong>\"._DESCRIPTION.\":</strong> " . $mod->getInfo('description');

        $top += 15;
        $js .= "\nfunction popUpL" . $mid . "() {\n    shutdown();\n    popUp('L" . $mid . "',true);}";
        $moveLayers .= "\n    setleft('L" . $mid . "'," . $left . ");\n    settop('L" . $mid . "'," . $top . ');';
        $shutdown .= "\n    popUp('L" . $mid . "',false);";
        $firstleveltable .= "$" . 'xoops_admin_menu_ft[' . $mid . "] = \"<a href='" . $module_url . "' title='" . $module_name . "' onmouseover='moveLayerY(\\\"L" . $mid . "\\\", currentY, event) ; popUpL" . $mid . "(); ' >" . $module_img . "</a><br>\";\n";
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

                $menu_layers .= "\n<img src='\".XOOPS_URL.\"/images/pointer.gif' width='8' height='8' alt='' />&nbsp;<a href='" . $menu_link . "'" . $menu_target . " onmouseover='popUpL" . $mid . "' >" . $menu_title . "</a><br>\n";
            }
        }

        $menu_layers .= "\n<div style='margin-top: 5px; font-size: smaller; text-align: right;'><a href='#' onmouseover='shutdown();'>[" . _CLOSE . "]</a></div></td></tr><tr><th style='font-size: smaller; text-align: left;'>" . $module_img . '<br>' . $module_desc . "</th></tr></table></div>\n";
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
 * Xoops Module Write Admin Menu
 *
 * @param string $content
 * @return bool
 */
function xoops_module_write_admin_menu($content)
{
    $GLOBALS['xoopsLogger']->addDeprecated(__FUNCTION__ . ' is deprecated, should not be used any longer');
    if (!xoopsfwrite()) {
        return false;
    }
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

    // write index.html file in cache folder
    // file is delete after clear_cache (smarty)
    xoops_write_index_file(XOOPS_CACHE_PATH);

    return true;
}

/**
 * Xoops Write Index File
 *
 * @param string $path
 * @return bool
 */
function xoops_write_index_file($path = '')
{
    if (empty($path)) {
        return false;
    }
    if (!xoopsfwrite()) {
        return false;
    }

    $path     = substr($path, -1) === '/' ? substr($path, 0, -1) : $path;
    $filename = $path . '/index.html';
    if (file_exists($filename)) {
        return true;
    }
    if (!$file = fopen($filename, 'w')) {
        echo 'failed open file';

        return false;
    }
    if (fwrite($file, '<script>history.go(-1);</script>') == -1) {
        echo 'failed write file';

        return false;
    }
    fclose($file);

    return true;
}
