<?php
/**
 * XOOPS Kernel Class
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
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * A registry for holding references to {@link XoopsObjectHandler} classes
 *
 * @package             kernel
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsHandlerRegistry
{
    /**
     * holds references to handler class objects
     *
     * @var array
     * @access    private
     */
    public $_handlers = array();

    /**
     * get a reference to the only instance of this class
     *
     * if the class has not been instantiated yet, this will also take
     * care of that
     *
     * @static
     * @staticvar   object  The only instance of this class
     * @return XoopsHandlerRegistry Reference to the only instance of this class
     */
    public function instance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new XoopsHandlerRegistry();
        }

        return $instance;
    }

    /**
     * Register a handler class object
     *
     * @param string $name     Short name of a handler class
     * @param XoopsObjectHandler &$handler {@link XoopsObjectHandler} class object
     */
    public function setHandler($name, XoopsObjectHandler $handler)
    {
        $this->_handlers['kernel'][$name] =& $handler;
    }

    /**
     * Get a registered handler class object
     *
     * @param string $name Short name of a handler class
     *
     * @return XoopsObjectHandler {@link XoopsObjectHandler}, FALSE if not registered
     */
    public function getHandler($name)
    {
        if (!isset($this->_handlers['kernel'][$name])) {
            return false;
        }

        return $this->_handlers['kernel'][$name];
    }

    /**
     * Unregister a handler class object
     *
     * @param string $name Short name of a handler class
     */
    public function unsetHandler($name)
    {
        unset($this->_handlers['kernel'][$name]);
    }

    /**
     * Register a handler class object for a module
     *
     * @param string $module   Directory name of a module
     * @param string $name     Short name of a handler class
     * @param XoopsObjectHandler &$handler {@link XoopsObjectHandler} class object
     */
    public function setModuleHandler($module, $name, XoopsObjectHandler $handler)
    {
        $this->_handlers['module'][$module][$name] =& $handler;
    }

    /**
     * Get a registered handler class object for a module
     *
     * @param string $module Directory name of a module
     * @param string $name   Short name of a handler class
     *
     * @return XoopsObjectHandler {@link XoopsObjectHandler}, FALSE if not registered
     */
    public function getModuleHandler($module, $name)
    {
        if (!isset($this->_handlers['module'][$module][$name])) {
            return false;
        }

        return $this->_handlers['module'][$module][$name];
    }

    /**
     * Unregister a handler class object for a module
     *
     * @param string $module Directory name of a module
     * @param string $name   Short name of a handler class
     */
    public function unsetModuleHandler($module, $name)
    {
        unset($this->_handlers['module'][$module][$name]);
    }
}
