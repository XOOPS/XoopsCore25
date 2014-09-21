<?php
/**
 * Xoops Preload Classes
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      class
 * @since           2.4.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id$
 * @deprecated      To be deprecated in XOOPS 3
 */

/**
 * XOOPS preload implemented in XOOPS is different from methods defined in this class, thus module developers are advised to be careful if you use current preload methods
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

XoopsLoad::load('XoopsLists');
XoopsLoad::load('XoopsCache');

/**
 * Class for handling events
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      class
 * @author          trabis <lusopoemas@gmail.com>
 */
class XoopsPreload
{
    /**
     * @var array $_preloads array containing information about the event observers
     */
    var $_preloads = array();

    /**
     * @var array $_events array containing the events that are being observed
     */
    var $_events = array();

    /**
     * Constructor
     *
     * @return	void
     */
    function XoopsPreload()
    {
        $this->setPreloads();
        $this->setEvents();
    }

    /**
     * Allow one instance only!
     *
     * @return object
     */
    static function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new XoopsPreload();
        }
        return $instance;
    }

    /**
     * Get available preloads information and set them to go!
     *
     * @return void
     */
    function setPreloads()
    {
        //$modules_list = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . "/modules/");
        if ($modules_list = XoopsCache::read('system_modules_active')) {
            $i = 0;
            foreach ($modules_list as $module) {
                if (is_dir($dir = XOOPS_ROOT_PATH . "/modules/{$module}/preloads/")) {
                    $file_list = XoopsLists::getFileListAsArray($dir);
                    foreach ($file_list as $file) {
                        if (preg_match('/(\.php)$/i', $file)) {
                            $file = substr($file, 0, -4);
                            $this->_preloads[$i]['module'] = $module;
                            $this->_preloads[$i]['file'] = $file;
                            ++$i;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get available events and set them to go!
     *
     * @return void
     */
    function setEvents()
    {
        foreach ($this->_preloads as $preload) {
            include_once XOOPS_ROOT_PATH . '/modules/' . $preload['module'] . '/preloads/' . $preload['file']. '.php';
            $class_name = ucfirst($preload['module']) . ucfirst($preload['file']) . 'Preload' ;
            if (!class_exists($class_name)) {
                continue;
            }
            $class_methods = get_class_methods($class_name);
            foreach ($class_methods as $method) {
                if (strpos($method, 'event') === 0) {
                    $event_name = strtolower(str_replace('event', '', $method));
                    $event= array('class_name' => $class_name, 'method' => $method);
                    $this->_events[$event_name][] = $event;
                }
            }
        }
    }

    /**
     * Triggers a specific event
     *
     * @param $event_name string Name of the event to trigger
     * @param $args array Method arguments
     *
     * @return void
     */
    function triggerEvent($event_name, $args = array())
    {
        $event_name = strtolower(str_replace('.', '', $event_name));
        if (isset($this->_events[$event_name])) {
            foreach ($this->_events[$event_name] as $event) {
                call_user_func(array($event['class_name'], $event['method']), $args);
            }
        }
    }

}

/**
 * XoopsPreloadItem
 *
 * Class which is extended by any preload item.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      class
 * @author          trabis <lusopoemas@gmail.com>
 */
class XoopsPreloadItem
{
    function XoopsPreloadItem()
    {
    }
}
