<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

xoops_load("gui", "system");

/**
 * Xoops Cpanel legacy GUI class
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package     system
 * @usbpackage  GUI
 * @since       2.3.0
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @version     $Id$
 */

class XoopsGuiLegacy extends /* implements */ XoopsSystemGui
{
    /**
     * Reference to template object
     */
    var $template;

    /**
     * Holding navigation
     */
    var $navigation;

    var $menu;

    /**
     *
     */
    function __construct()
    {
        include_once __DIR__ . "/cp_functions.php";
    }

    function XoopsGuiLegacy()
    {
        $this->__construct();
    }

    /**
     * @return bool
     */
    function validate()
    {
        return true;
    }

    function flush()
    {
        @unlink(XOOPS_CACHE_PATH . '/adminmenu.php');
    }

    /**
     * @access  private
     *
     */
    function generateMenu()
    {
        xoops_legacy_module_write_admin_menu( xoops_legacy_module_get_admin_menu() );

        return true;
    }

    /**
     * @return bool
     */
    function header()
    {
        parent::header();
        $tpl =& $this->template;
        if (!file_exists(XOOPS_CACHE_PATH . '/adminmenu.php')) {
            $this->generateMenu();
        }

        xoops_legacy_cp_header($tpl);

        return true;
    }

    /**
     * @return bool
     */
    function footer()
    {
        xoops_legacy_cp_footer();
        parent::footer();

        return true;
    }
}
