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
 * @license             http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package             system
 * @usbpackage          GUI
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @version             $Id: legacy.php 13082 2015-06-06 21:59:41Z beckmi $
 */
class XoopsGuiLegacy extends /* implements */
    XoopsSystemGui
{
    /**
     * Reference to template object
     */
    public $template;

    /**
     * Holding navigation
     */
    public $navigation;

    public $menu;

    /**
     *
     */
    public function __construct()
    {
        include_once __DIR__ . "/cp_functions.php";
    }

    public function XoopsGuiLegacy()
    {
        $this->__construct();
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    public function flush()
    {
        @unlink(XOOPS_CACHE_PATH . '/adminmenu.php');
    }

    /**
     * @access  private
     *
     */
    public function generateMenu()
    {
        xoops_legacy_module_write_admin_menu(xoops_legacy_module_get_admin_menu());

        return true;
    }

    /**
     * @return bool
     */
    public function header()
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
    public function footer()
    {
        xoops_legacy_cp_footer();
        parent::footer();

        return true;
    }
}
