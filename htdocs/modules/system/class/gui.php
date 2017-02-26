<?php
/**
 * Xoops Cpanel GUI abstract class
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
 * @package             system
 * @subpackage          class
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
class XoopsSystemGui
{
    /**
     * Reference to template object
     */
    public $template;

    /**
     * Holding navigation
     */
    public $navigation;

    /**
     * Holding gui folder name
     */
    public $foldername;

    /**
     * Reference for Theme
     */
    public $xoTheme;

    public function header()
    {
        global $xoops, $xoopsConfig, $xoopsModule, $xoopsUser, $xoopsOption, $xoTheme, $xoopsTpl;
        ob_start();

        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('cpanel', 'system');
        xoops_loadLanguage('modinfo', 'system');

        $xoopsLogger = XoopsLogger::getInstance();
        $xoopsLogger->stopTime('Module init');
        $xoopsLogger->startTime('XOOPS output init');

        if (!headers_sent()) {
            header('Content-Type:text/html; charset=' . _CHARSET);
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
        }

        require_once XOOPS_ROOT_PATH . '/class/template.php';
        require_once XOOPS_ROOT_PATH . '/class/theme.php';

        if (@$GLOBALS['xoopsOption']['template_main']) {
            if (false === strpos($GLOBALS['xoopsOption']['template_main'], ':')) {
                $GLOBALS['xoopsOption']['template_main'] = 'db:' . $GLOBALS['xoopsOption']['template_main'];
            }
        }

        $adminThemeFactory = new xos_opal_AdminThemeFactory();
        $this->xoTheme     =& $adminThemeFactory->createInstance(array(
                                                                     'folderName'      => $this->foldername,
                                                                     'themesPath'      => 'modules/system/themes',
                                                                     'contentTemplate' => @$GLOBALS['xoopsOption']['template_main']));

        $this->xoTheme->loadLocalization('admin');
        $this->template =& $this->xoTheme->template;

        $GLOBALS['xoTheme']  =& $this->xoTheme;
        $GLOBALS['adminTpl'] =& $this->xoTheme->template;

        $xoopsLogger->stopTime('XOOPS output init');
        $xoopsLogger->startTime('Module display');

        $xoopsPreload = XoopsPreload::getInstance();
        $xoopsPreload->triggerEvent('system.class.gui.header');

        if (isset($xoopsModule) && $xoopsModule->getVar('dirname') === 'system') {
            $xoopsModule->loadAdminMenu();

            foreach (array_keys($xoopsModule->adminmenu) as $item) {
                $sys_menu[$item]['link'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/' . $xoopsModule->adminmenu[$item]['link'];
                $GLOBALS['xoopsTpl']->append_by_ref('sys_menu', $sys_menu);
                unset($sys_menu);
            }
        }
        // Module adminmenu
        if (isset($xoopsModule) && $xoopsModule->getVar('dirname') !== 'system') {
            if ($xoopsModule->getInfo('system_menu')) {
                $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/system/css/menu.css');

                $xoopsModule->loadAdminMenu();
                // Get menu tab handler
                /* @var $menu_handler SystemMenuHandler */
                $menu_handler = xoops_getModuleHandler('menu', 'system');
                // Define top navigation
                $menu_handler->addMenuTop(XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $xoopsModule->getVar('mid', 'e'), _AM_SYSTEM_PREF);
                $menu_handler->addMenuTop(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=' . $xoopsModule->getVar('dirname', 'e'), _AM_SYSTEM_UPDATE);
                $menu_handler->addMenuTop(XOOPS_URL . '/modules/system/admin.php?fct=blocksadmin&amp;op=list&amp;filter=1&amp;selgen=' . $xoopsModule->getVar('mid', 'e') . '&amp;selmod=-2&amp;selgrp=-1&amp;selvis=-1', _AM_SYSTEM_BLOCKS);
                $menu_handler->addMenuTop(XOOPS_URL . '/modules/system/admin.php?fct=tplsets&amp;op=listtpl&amp;tplset=default&amp;moddir=' . $xoopsModule->getVar('dirname', 'e'), _AM_SYSTEM_TPLSETS);
                $menu_handler->addMenuTop(XOOPS_URL . '/modules/system/admin.php?module=' . $xoopsModule->getVar('mid', 'e') . '&amp;status=0&amp;limit=10&amp;fct=comments', _AM_SYSTEM_COMMENTS);
                $menu_handler->addMenuTop(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&amp;op=uninstall&amp;module=' . $xoopsModule->getVar('dirname', 'e'), _AM_SYSTEM_UNINSTALL);

                $menu_handler->addMenuTop(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname', 'e') . '/', _AM_SYSTEM_GOTOMODULE);
                // Define main tab navigation
                $i       = 0;
                $current = $i;
                foreach ($xoopsModule->adminmenu as $menu) {
                    if (stripos($_SERVER['REQUEST_URI'], $menu['link']) !== false) {
                        $current = $i;
                    }
                    $menu_handler->addMenuTabs($menu['link'], $menu['title']);
                    ++$i;
                }
                if ($xoopsModule->getInfo('help')) {
                    if (stripos($_SERVER['REQUEST_URI'], 'admin/' . $xoopsModule->getInfo('help')) !== false) {
                        $current = $i;
                    }
                    $menu_handler->addMenuTabs('../system/help.php?mid=' . $xoopsModule->getVar('mid', 's') . '&amp;' . $xoopsModule->getInfo('help'), _AM_SYSTEM_HELP);
                }

                // Display navigation tabs
                $GLOBALS['xoopsTpl']->assign('xo_system_menu', $menu_handler->render($current, false));
            }
        }
    }

    public function footer()
    {
        global $xoopsConfig, $xoopsOption, $xoopsTpl, $xoTheme;

        $xoopsLogger = XoopsLogger::getInstance();
        $xoopsLogger->stopTime('Module display');

        if (!headers_sent()) {
            header('Content-Type:text/html; charset=' . _CHARSET);
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Cache-Control: private, no-cache');
            header('Pragma: no-cache');
        }

        //@internal: using global $xoTheme dereferences the variable in old versions, this does not
        if (!isset($xoTheme)) {
            $xoTheme = $GLOBALS['xoTheme'];
        }

        if (isset($GLOBALS['xoopsOption']['template_main']) && $GLOBALS['xoopsOption']['template_main'] != $xoTheme->contentTemplate) {
            trigger_error("xoopsOption['template_main'] should be defined before call xoops_cp_header function", E_USER_WARNING);
            if (false === strpos($GLOBALS['xoopsOption']['template_main'], ':')) {
                $xoTheme->contentTemplate = 'db:' . $GLOBALS['xoopsOption']['template_main'];
            } else {
                $xoTheme->contentTemplate = $GLOBALS['xoopsOption']['template_main'];
            }
        }

        $xoTheme->render();
        $xoopsLogger->stopTime();
        ob_end_flush();
    }

    public static function validate()
    {
    }

    public static function flush()
    {
    }

    public function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class    = __CLASS__;
            $instance = new $class();
        }

        return $instance;
    }
}
