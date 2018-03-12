<?php
/**
 * Object factory class.
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
 * @subpackage          model
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once XOOPS_ROOT_PATH . '/kernel/object.php';

/**
 * Factory for object handlers
 *
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 * @package kernel
 */
class XoopsModelFactory
{
    /**
     * static private
     */
    public $handlers = array();

    /**
     * XoopsModelFactory::__construct()
     */
    protected function __construct()
    {
    }

    /**
     * Get singleton instance
     *
     * @access public
     */
    public function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class    = __CLASS__;
            $instance = new $class();
        }

        return $instance;
    }

    /**
     * Load object handler
     *
     * @access   public
     *
     * @param XoopsPersistableObjectHandler $ohandler reference to {@link XoopsPersistableObjectHandler}
     * @param string $name     handler name
     * @param mixed  $args     args
     *
     * @internal param XoopsPersistableObjectHandler $ohandler reference to {@link XoopsPersistableObjectHandler}
     * @return object of handler
     */
    public static function loadHandler(XoopsPersistableObjectHandler $ohandler, $name, $args = null)
    {
        static $handlers;
        if (!isset($handlers[$name])) {
            if (file_exists($file = __DIR__ . '/' . $name . '.php')) {
                include_once $file;
                $className = 'XoopsModel' . ucfirst($name);
                $handler   = new $className();
            } elseif (xoops_load('model', 'framework')) {
                $handler = XoopsModel::loadHandler($name);
            }
            if (!is_object($handler)) {
                trigger_error('Handler not found in file ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);

                return null;
            }
            $handlers[$name] = $handler;
        }
        $handlers[$name]->setHandler($ohandler);
        if (!empty($args) && is_array($args) && is_a($handlers[$name], 'XoopsModelAbstract')) {
            $handlers[$name]->setVars($args);
        }

        return $handlers[$name];
    }
}

/**
 * abstract class object handler
 *
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 * @package kernel
 */
class XoopsModelAbstract
{
    /**
     * holds referenced to handler object
     *
     * @var   object
     * @param XoopsPersistableObjectHandler $ohandler reference to {@link XoopsPersistableObjectHandler}
     * @access protected
     */
    public $handler;

    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access protected
     * @param null $args
     * @param null $handler
     */
    public function __construct($args = null, $handler = null)
    {
        $this->setHandler($handler);
        $this->setVars($args);
    }

    /**
     * XoopsModelAbstract::setHandler()
     *
     * @param  mixed $handler
     * @return bool
     */
    public function setHandler($handler)
    {
        if (is_object($handler) && is_a($handler, 'XoopsPersistableObjectHandler')) {
            $this->handler =& $handler;

            return true;
        }

        return false;
    }

    /**
     * XoopsModelAbstract::setVars()
     *
     * @param  mixed $args
     * @return bool
     */
    public function setVars($args)
    {
        if (!empty($args) && is_array($args)) {
            foreach ($args as $key => $value) {
                $this->$key = $value;
            }
        }

        return true;
    }
}
