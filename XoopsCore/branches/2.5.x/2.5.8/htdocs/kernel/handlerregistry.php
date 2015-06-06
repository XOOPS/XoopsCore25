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
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @since           2.0.0
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * A registry for holding references to {@link XoopsObjectHandler} classes
 *
 * @package     kernel
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 */
class XoopsHandlerRegistry
{
    /**
     * holds references to handler class objects
     *
     * @var     array
     * @access	private
     */
    var $_handlers = array();

    /**
     * get a reference to the only instance of this class
     *
     * if the class has not been instantiated yet, this will also take
     * care of that
     *
     * @static
     * @staticvar   object  The only instance of this class
     * @return      object  Reference to the only instance of this class
     */
    function &instance()
    {
        static $instance;
        if (! isset($instance)) {
            $instance = new XoopsHandlerRegistry();
        }
        return $instance;
    }

    /**
     * Register a handler class object
     *
     * @param	string  $name     Short name of a handler class
     * @param	object  &$handler {@link XoopsObjectHandler} class object
     */
    function setHandler($name, &$handler)
    {
        $this->_handlers['kernel'][$name] =& $handler;
    }

    /**
     * Get a registered handler class object
     *
     * @param	string  $name     Short name of a handler class
     *
     * @return	object {@link XoopsObjectHandler}, FALSE if not registered
     */
    function &getHandler($name)
    {
        if (!isset($this->_handlers['kernel'][$name])) {
            return false;
        }
        return $this->_handlers['kernel'][$name];
    }

    /**
     * Unregister a handler class object
     *
     * @param	string  $name     Short name of a handler class
     */
    function unsetHandler($name)
    {
        unset($this->_handlers['kernel'][$name]);
    }

    /**
     * Register a handler class object for a module
     *
     * @param	string  $module   Directory name of a module
     * @param	string  $name     Short name of a handler class
     * @param	object  &$handler {@link XoopsObjectHandler} class object
     */
    function setModuleHandler($module, $name, &$handler)
    {
        $this->_handlers['module'][$module][$name] =& $handler;
    }

    /**
     * Get a registered handler class object for a module
     *
     * @param	string  $module   Directory name of a module
     * @param	string  $name     Short name of a handler class
     *
     * @return	object {@link XoopsObjectHandler}, FALSE if not registered
     */
    function &getModuleHandler($module, $name)
    {
        if (!isset($this->_handlers['module'][$module][$name])) {
            return false;
        }
        return $this->_handlers['module'][$module][$name];
    }

    /**
     * Unregister a handler class object for a module
     *
     * @param	string  $module   Directory name of a module
     * @param	string  $name     Short name of a handler class
     */
    function unsetModuleHandler($module, $name)
    {
        unset($this->_handlers['module'][$module][$name]);
    }

}
