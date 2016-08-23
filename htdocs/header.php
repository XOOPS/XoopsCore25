<?php
/**
 * XOOPS global header file
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
 * @package             core
 * @since               2.0.0
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.header.start');

include_once $GLOBALS['xoops']->path('class/xoopsblock.php');

$xoopsLogger = XoopsLogger::getInstance();
$xoopsLogger->stopTime('Module init');
$xoopsLogger->startTime('XOOPS output init');

if ($xoopsConfig['theme_set'] !== 'default' && file_exists(XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/theme.php')) {
    require_once $GLOBALS['xoops']->path('include/xoops13_header.php');
} else {
    global $xoopsOption, $xoopsConfig, $xoopsModule;

    $xoopsOption['theme_use_smarty'] = 1;

    // include Smarty template engine and initialize it
    require_once $GLOBALS['xoops']->path('class/template.php');
    require_once $GLOBALS['xoops']->path('class/theme.php');
    require_once $GLOBALS['xoops']->path('class/theme_blocks.php');

    if (@$GLOBALS['xoopsOption']['template_main']) {
        if (false === strpos($GLOBALS['xoopsOption']['template_main'], ':')) {
            $GLOBALS['xoopsOption']['template_main'] = 'db:' . $GLOBALS['xoopsOption']['template_main'];
        }
    }

    $xoopsThemeFactory                = null;
    $xoopsThemeFactory                = new xos_opal_ThemeFactory();
    $xoopsThemeFactory->allowedThemes = $xoopsConfig['theme_set_allowed'];
    $xoopsThemeFactory->defaultTheme  = $xoopsConfig['theme_set'];

    /**
     * @var xos_opal_Theme
     */
    $xoTheme  = $xoopsThemeFactory->createInstance(array('contentTemplate' => @$xoopsOption['template_main']));
    $xoopsTpl = $xoTheme->template;

    $xoopsPreload->triggerEvent('core.header.addmeta');

    // Temporary solution for start page redirection
    if (defined('XOOPS_STARTPAGE_REDIRECTED')) {
        $params = $content = $tpl = $repeat = null;
        $xoTheme->headContent($params, "<base href='" . XOOPS_URL . '/modules/' . $xoopsConfig['startpage'] . "/' />", $tpl, $repeat);
    }

    if (@is_object($xoTheme->plugins['xos_logos_PageBuilder'])) {
        $aggreg =& $xoTheme->plugins['xos_logos_PageBuilder'];
        // Backward compatibility code for pre 2.0.14 themes
        $xoopsTpl->assign_by_ref('xoops_lblocks', $aggreg->blocks['canvas_left']);
        $xoopsTpl->assign_by_ref('xoops_rblocks', $aggreg->blocks['canvas_right']);
        $xoopsTpl->assign_by_ref('xoops_ccblocks', $aggreg->blocks['page_topcenter']);
        $xoopsTpl->assign_by_ref('xoops_clblocks', $aggreg->blocks['page_topleft']);
        $xoopsTpl->assign_by_ref('xoops_crblocks', $aggreg->blocks['page_topright']);
        $xoopsTpl->assign('xoops_showlblock', !empty($aggreg->blocks['canvas_left']));
        $xoopsTpl->assign('xoops_showrblock', !empty($aggreg->blocks['canvas_right']));
        $xoopsTpl->assign('xoops_showcblock', !empty($aggreg->blocks['page_topcenter']) || !empty($aggreg->blocks['page_topleft']) || !empty($aggreg->blocks['page_topright']));
    }

    // Sets cache time
    if (!empty($xoopsModule)) {
        $xoTheme->contentCacheLifetime = @$xoopsConfig['module_cache'][$xoopsModule->getVar('mid', 'n')];
        // Tricky solution for setting cache time for homepage
    } elseif (!empty($GLOBALS['xoopsOption']['template_main']) && $GLOBALS['xoopsOption']['template_main'] === 'db:system_homepage.tpl') {
        $xoTheme->contentCacheLifetime = 604800;
    }

    $xoopsPreload->triggerEvent('core.header.checkcache');
    if ($xoTheme->checkCache()) {
        $xoopsPreload->triggerEvent('core.header.cacheend');
        //exit(); // see https://sourceforge.net/p/xoops/bugs/1261/
    }

    if (!isset($GLOBALS['xoopsOption']['template_main']) && $xoopsModule) {
        // new themes using Smarty does not have old functions that are required in old modules, so include them now
        include $GLOBALS['xoops']->path('include/old_theme_functions.php');
        // need this also
        $xoopsTheme['thename'] = $xoopsConfig['theme_set'];
        ob_start();
    }

    $xoopsLogger->stopTime('XOOPS output init');
    $xoopsLogger->startTime('Module display');
}

$xoopsPreload->triggerEvent('core.header.end');
