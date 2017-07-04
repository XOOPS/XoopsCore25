<?php

/**
 * Xoops Cpanel class
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
class XoopsSystemCpanel
{
    /**
     * Reference to GUI object
     */
    public $gui;

    /**
     * Constructer
     *
     */
    public function __construct()
    {
        $cpanel = xoops_getConfigOption('cpanel');
        $this->loadGui($cpanel);
    }

    /**
     * Get an instance of the class
     *
     * @return XoopsSystemCpanel
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class    = __CLASS__;
            $instance = new $class();
        }

        return $instance;
    }

    /**
     * Load the Xoops Admin Gui by preference
     *
     * @param string $gui
     */
    public function loadGui($gui)
    {
        if (!empty($gui)) {
            $class = 'XoopsGui' . ucfirst($gui);
            if (!class_exists($class)) {
                include_once XOOPS_ADMINTHEME_PATH . '/' . $gui . '/' . $gui . '.php';
            }
            if (class_exists($class)) {
                if (call_user_func(array($class, 'validate'))) {
                    $this->gui             = new $class();
                    $this->gui->foldername = $gui;
                }
            }
        }
        if (!isset($this->gui)) {
            if (file_exists($file = XOOPS_ADMINTHEME_PATH . '/transition/transition.php')) {
                include_once $file;
                $this->gui             = new XoopsGuiTransition();
                $this->gui->foldername = 'transition';
            }
        }
    }

    /**
     * Get a list of Xoops Admin Gui
     *
     * @return unknown
     */
    public static function getGuis()
    {
        $guis = array();
        xoops_load('XoopsLists');
        $lists = XoopsLists::getDirListAsArray(XOOPS_ADMINTHEME_PATH);
        foreach (array_keys($lists) as $gui) {
            if (file_exists($file = XOOPS_ADMINTHEME_PATH . '/' . $gui . '/' . $gui . '.php')) {
                include_once $file;
                if (class_exists($class = 'XoopsGui' . ucfirst($gui))) {
                    if (call_user_func(array($class, 'validate'))) {
                        $guis[$gui] = $gui;
                    }
                }
            }
        }

        return $guis;
    }

    /**
     * Flush the Xoops Admin Gui
     *
     */
    public static function flush()
    {
        $guis = XoopsSystemCpanel::getGuis();
        foreach ($guis as $gui) {
            if ($file = XOOPS_ADMINTHEME_PATH . '/' . $gui . '/' . $gui . '.php') {
                include_once $file;
                if (class_exists($class = 'XoopsGui' . ucfirst($gui))) {
                    call_user_func(array($class, 'flush'));
                }
            }
        }
    }
}
