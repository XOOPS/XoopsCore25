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
 * Xoops Cpanel ThAdmin GUI class
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license             http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package             system
 * @usbpackage          GUI
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @version             $Id: thadmin.php 13082 2015-06-06 21:59:41Z beckmi $
 */
class XoopsGuiThadmin extends /* implements */
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

    /**
     *
     */
    public function __construct()
    {
        include_once XOOPS_ROOT_PATH . "/modules/thadmin/include/cp_functions.php";
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $module_handler =& xoops_getHandler("module");
        if ($admin_module = $module_handler->getByDirname("thadmin")) {
            if ($admin_module->getVar("isactive")) {
                return true;
            }
        }

        return false;
    }

    public function header()
    {
        xoops_thadmin_cp_header();
    }

    public function footer()
    {
        xoops_thadmin_cp_footer();
    }
}
