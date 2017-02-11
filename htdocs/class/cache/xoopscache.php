<?php
/**
 * Cache engine For XOOPS
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
 * @package             class
 * @subpackage          cache
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Caching for CakePHP.
 *
 * @package    cake
 * @subpackage cake.cake.libs
 */
class XoopsCache
{
    /**
     * Cache engine to use
     *
     * @var object
     * @access protected
     */

    protected $engine;

    /**
     * Cache configuration stack
     *
     * @var array
     * @access private
     */
    private $configs = array();

    /**
     * Holds name of the current configuration being used
     *
     * @var array
     * @access private
     */
    private $name;

    /**
     * XoopsCache::__construct()
     */
    public function __construct()
    {
    }

    /**
     * Returns a singleton instance
     *
     * @return object
     * @access public
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
     * Tries to find and include a file for a cache engine and returns object instance
     *
     * @param  $name Name of the engine
     * @return mixed $engine object or null
     * @access private
     */
    private function loadEngine($name)
    {
        if (!class_exists('XoopsCache' . ucfirst($name))) {
            if (file_exists($file = __DIR__ . '/' . strtolower($name) . '.php')) {
                include $file;
            } else {
                trigger_error('File :' . $file . ' not found in file : ' . __FILE__ . ' at line: ' . __LINE__, E_USER_WARNING);

                return false;
            }
        }

        return true;
    }

    /**
     * Set the cache configuration to use
     *
     * @param  string|array $name     Name of the configuration
     * @param  array  $settings Optional associative array of settings passed to the engine
     * @return array  (engine, settings) on success, false on failure
     * @access public
     */
    public function config($name = 'default', $settings = array())
    {
        $_this = XoopsCache::getInstance();
        if (is_array($name)) {
            extract($name);
        }

        if (isset($_this->configs[$name])) {
            $settings = array_merge($_this->configs[$name], $settings);
        } elseif (!empty($settings)) {
            $_this->configs[$name] = $settings;
        } elseif ($_this->configs !== null && isset($_this->configs[$_this->name])) {
            $name     = $_this->name;
            $settings = $_this->configs[$_this->name];
        } else {
            $name = 'default';
            if (!empty($_this->configs['default'])) {
                $settings = $_this->configs['default'];
            } else {
                $settings = array(
                    'engine' => 'file');
            }
        }
        $engine = 'file';
        if (!empty($settings['engine'])) {
            $engine = $settings['engine'];
        }

        if ($name !== $_this->name) {
            if ($_this->engine($engine, $settings) === false) {
                trigger_error("Cache Engine {$engine} is not set", E_USER_WARNING);

                return false;
            }
            $_this->name           = $name;
            $_this->configs[$name] = $_this->settings($engine);
        }

        $settings = $_this->configs[$name];

        return compact('engine', 'settings');
    }

    /**
     * Set the cache engine to use or modify settings for one instance
     *
     * @param  string $name     Name of the engine (without 'Engine')
     * @param  array  $settings Optional associative array of settings passed to the engine
     * @return boolean True on success, false on failure
     * @access public
     */
    public function engine($name = 'file', $settings = array())
    {
        if (!$name) {
            return false;
        }

        $cacheClass = 'XoopsCache' . ucfirst($name);
        $_this      = XoopsCache::getInstance();
        if (!isset($_this->engine[$name])) {
            if ($_this->loadEngine($name) === false) {
                trigger_error("Cache Engine {$name} is not loaded", E_USER_WARNING);

                return false;
            }
            $_this->engine[$name] = new $cacheClass();
        }

        if ($_this->engine[$name]->init($settings)) {
            if (time() % $_this->engine[$name]->settings['probability'] == 0) {
                $_this->engine[$name]->gc();
            }

            return true;
        }
        $_this->engine[$name] = null;
        trigger_error("Cache Engine {$name} is not initialized", E_USER_WARNING);

        return false;
    }

    /**
     * Garbage collection
     *
     * Permanently remove all expired and deleted data
     *
     * @access public
     */
    public function gc()
    {
        $_this  = XoopsCache::getInstance();
        $config = $_this->config();
        extract($config);
        $_this->engine[$engine]->gc();
    }

    /**
     * Write data for key into cache
     *
     * @param  string $key       Identifier for the data
     * @param  mixed  $value     Data to be cached - anything except a resource
     * @param  mixed  $duration  Optional - string configuration name OR how long to cache the data, either in seconds or a
     *                           string that can be parsed by the strtotime() function OR array('config' => 'default', 'duration' => '3600')
     * @return boolean True if the data was successfully cached, false on failure
     * @access public
     */
    public static function write($key, $value, $duration = null)
    {
        $key    = substr(md5(XOOPS_URL), 0, 8) . '_' . $key;
        $_this  = XoopsCache::getInstance();
        $config = null;
        if (is_array($duration)) {
            extract($duration);
        } elseif (isset($_this->configs[$duration])) {
            $config   = $duration;
            $duration = null;
        }
        $config = $_this->config($config);

        if (!is_array($config)) {
            return null;
        }
        extract($config);

        if (!$_this->isInitialized($engine)) {
            trigger_error('Cache write not initialized: ' . $engine);

            return false;
        }

        if (!$key = $_this->key($key)) {
            return false;
        }

        if (is_resource($value)) {
            return false;
        }

        if (!$duration) {
            $duration = $settings['duration'];
        }
        $duration = is_numeric($duration) ? (int)$duration : strtotime($duration) - time();

        if ($duration < 1) {
            return false;
        }
        $_this->engine[$engine]->init($settings);
        $success = $_this->engine[$engine]->write($key, $value, $duration);

        return $success;
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key    Identifier for the data
     * @param  string|array $config name of the configuration to use
     * @return mixed  The cached data, or false if the data doesn't exist, has expired, or if there was an error fetching it
     * @access public
     */
    public static function read($key, $config = null)
    {
        $key    = substr(md5(XOOPS_URL), 0, 8) . '_' . $key;
        $_this  = XoopsCache::getInstance();
        $config = $_this->config($config);

        if (!is_array($config)) {
            return null;
        }

        extract($config);

        if (!$_this->isInitialized($engine)) {
            return false;
        }
        if (!$key = $_this->key($key)) {
            return false;
        }
        $_this->engine[$engine]->init($settings);
        $success = $_this->engine[$engine]->read($key);

        return $success;
    }

    /**
     * Delete a key from the cache
     *
     * @param  string $key    Identifier for the data
     * @param  string $config name of the configuration to use
     * @return boolean True if the value was successfully deleted, false if it didn't exist or couldn't be removed
     * @access public
     */
    public static function delete($key, $config = null)
    {
        $key   = substr(md5(XOOPS_URL), 0, 8) . '_' . $key;
        $_this = XoopsCache::getInstance();

        $config = $_this->config($config);
        extract($config);

        if (!$_this->isInitialized($engine)) {
            return false;
        }

        if (!$key = $_this->key($key)) {
            return false;
        }

        $_this->engine[$engine]->init($settings);
        $success = $_this->engine[$engine]->delete($key);

        return $success;
    }

    /**
     * Delete all keys from the cache
     *
     * @param  boolean $check  if true will check expiration, otherwise delete all
     * @param  string  $config name of the configuration to use
     * @return boolean True if the cache was successfully cleared, false otherwise
     * @access public
     */
    public function clear($check = false, $config = null)
    {
        $_this  = XoopsCache::getInstance();
        $config = $_this->config($config);
        extract($config);

        if (!$_this->isInitialized($engine)) {
            return false;
        }
        $success = $_this->engine[$engine]->clear($check);
        $_this->engine[$engine]->init($settings);

        return $success;
    }

    /**
     * Check if Cache has initialized a working storage engine
     *
     * @param  string $engine Name of the engine
     * @return bool
     * @internal param string $configs Name of the configuration setting
     * @access   public
     */
    public function isInitialized($engine = null)
    {
        $_this = XoopsCache::getInstance();
        if (!$engine && isset($_this->configs[$_this->name]['engine'])) {
            $engine = $_this->configs[$_this->name]['engine'];
        }

        return isset($_this->engine[$engine]);
    }

    /**
     * Return the settings for current cache engine
     *
     * @param  string $engine Name of the engine
     * @return array  list of settings for this engine
     * @access public
     */
    public function settings($engine = null)
    {
        $_this = XoopsCache::getInstance();
        if (!$engine && isset($_this->configs[$_this->name]['engine'])) {
            $engine = $_this->configs[$_this->name]['engine'];
        }
        if (isset($_this->engine[$engine]) && null !== $_this->engine[$engine]) {
            return $_this->engine[$engine]->settings();
        }

        return array();
    }

    /**
     * generates a safe key
     *
     * @param  string $key the key passed over
     * @return mixed  string $key or false
     * @access private
     */
    public function key($key)
    {
        if (empty($key)) {
            return false;
        }
        $key = str_replace(array('/', '.'), '_', (string)$key);

        return $key;
    }
}

/**
 * Abstract class for storage engine for caching
 *
 * @package    core
 * @subpackage cache
 */
class XoopsCacheEngine
{
    /**
     * settings of current engine instance
     *
     * @var int
     * @access public
     */
    public $settings;

    /**
     * Iitialize the cache engine
     *
     * Called automatically by the cache frontend
     *
     * @param  array $settings Associative array of parameters for the engine
     * @return boolean True if the engine has been successfully initialized, false if not
     * @access   public
     */
    public function init($settings = array())
    {
        $this->settings = array_merge(array(
                                          'duration'    => 31556926,
                                          'probability' => 100), $settings);

        return true;
    }

    /**
     * Garbage collection
     *
     * Permanently remove all expired and deleted data
     *
     * @access public
     */
    public function gc()
    {
    }

    /**
     * Write value for a key into cache
     *
     * @param  string $key      Identifier for the data
     * @param  mixed  $value    Data to be cached
     * @param  mixed  $duration How long to cache the data, in seconds
     * @return boolean True if the data was successfully cached, false on failure
     * @access public
     */
    public function write($key, $value, $duration = null)
    {
        trigger_error(sprintf(__('Method write() not implemented in %s', true), get_class($this)), E_USER_ERROR);
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  The cached data, or false if the data doesn't exist, has expired, or if there was an error fetching it
     * @access public
     */
    public function read($key)
    {
        trigger_error(sprintf(__('Method read() not implemented in %s', true), get_class($this)), E_USER_ERROR);
    }

    /**
     * Delete a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return boolean True if the value was successfully deleted, false if it didn't exist or couldn't be removed
     * @access public
     */
    public function delete($key)
    {
    }

    /**
     * Delete all keys from the cache
     *
     * @param  boolean $check if true will check expiration, otherwise delete all
     * @return boolean True if the cache was successfully cleared, false otherwise
     * @access public
     */
    public function clear($check)
    {
    }

    /**
     * Cache Engine settings
     *
     * @return array settings
     * @access public
     */
    public function settings()
    {
        return $this->settings;
    }
}
