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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
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
     * Constructor
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
            $class    = self::class;
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
                if (call_user_func([$class, 'validate'])) {
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
     * @return mixed
     */
    public static function getGuis()
    {
        $guis = [];
        xoops_load('XoopsLists');
        $lists = XoopsLists::getDirListAsArray(XOOPS_ADMINTHEME_PATH);
        foreach (array_keys($lists) as $gui) {
            if (file_exists($file = XOOPS_ADMINTHEME_PATH . '/' . $gui . '/' . $gui . '.php')) {
                include_once $file;
                if (class_exists($class = 'XoopsGui' . ucfirst($gui))) {
                    if (call_user_func([$class, 'validate'])) {
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
                if (class_exists($class = 'XoopsGui' . ucfirst((string) $gui))) {
                    call_user_func([$class, 'flush']);
                }
            }
        }
    }
}
