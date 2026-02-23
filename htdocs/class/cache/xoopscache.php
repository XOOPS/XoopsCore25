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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          cache
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

if (!defined('XOOPS_ROOT_PATH')) {
    throw new \RuntimeException('Restricted access');
}

/**
 * Caching for CakePHP.
 *
 * @package    cake
 * @subpackage cake.cake.libs
 */
class XoopsCache
{
    /**
     * Cache engine instances
     *
     * @var array<string, XoopsCacheEngineInterface|null>
     * @access protected
     */
    protected $engine = [];

    /**
     * Cache configuration stack
     *
     * @var array
     * @access private
     */
    private $configs = [];

    /**
     * Holds name of the current configuration being used
     *
     * @var string|null
     * @access private
     */
    private $name;

    /**
     * XoopsCache::__construct()
     */
    public function __construct() {}

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
            $class    = self::class;
            $instance = new $class();
        }

        return $instance;
    }

    /**
     * Resolves and validates engine configuration
     *
     * @param string|array|null $config Configuration name or array
     * @return array{engine: string, settings: array<string, mixed>}|false
     */
    private function resolveEngineConfig($config)
    {
        $config = $this->config($config);

        if (
            !is_array($config) ||
            !isset($config['engine'], $config['settings']) ||
            !is_string($config['engine']) ||
            !is_array($config['settings'])
        ) {
            return false;
        }

        return $config;
    }

    /**
     * Resolve engine name from parameter or current configuration
     *
     * @param string|null $engine Engine name or null to use current
     * @return string|null Engine name or null if cannot be resolved
     */
    private function resolveEngineName($engine)
    {
        if ($engine !== null && is_string($engine) && $engine !== '') {
            return $engine;
        }

        if (
            is_string($this->name) &&
            $this->name !== '' &&
            isset($this->configs[$this->name]['engine'])
        ) {
            return $this->configs[$this->name]['engine'];
        }

        return null;
    }

    /**
     * Tries to find and include a file for a cache engine
     *
     * @param  string $name Name of the engine
     * @return bool True if the engine was successfully loaded, false on failure
     * @access private
     */
    private function loadEngine($name)
    {
        if (!class_exists('XoopsCache' . ucfirst($name))) {
            $baseDir   = realpath(__DIR__);
            $candidate = __DIR__ . '/' . strtolower($name) . '.php';
            $file      = $baseDir !== false ? realpath($candidate) : false;

            if ($file !== false && strpos($file, $baseDir . DIRECTORY_SEPARATOR) === 0) {
                include $file;
                if (!class_exists('XoopsCache' . ucfirst($name), false)) {
                    trigger_error(
                        'Class XoopsCache' . ucfirst($name) . ' not found after including ' . basename($file),
                        E_USER_WARNING
                    );
                    return false;
                }
            } else {
                trigger_error(
                    'File: ' . basename($candidate) . ' not found in file: ' . basename(__FILE__) . ' at line: ' . __LINE__,
                    E_USER_WARNING
                );
                return false;
            }
        }

        return true;
    }

    /**
     * Set the cache configuration to use
     *
     * @param  string|array|null $name     Name of the configuration
     * @param  array  $settings Optional associative array of settings passed to the engine
     * @return array|false  (engine, settings) on success, false on failure
     * @access public
     */
    public function config($name = 'default', $settings = [])
    {
        $_this = XoopsCache::getInstance();

        if (!is_array($settings)) {
            $settings = [];
        }

        if (is_array($name)) {
            $config = $name;

            if (isset($config['name']) && is_string($config['name'])) {
                $name = $config['name'];
                unset($config['name']);
            } else {
                $name = 'default';
            }

            if (isset($config['settings']) && is_array($config['settings'])) {
                $settings = $config['settings'];
                // Carry forward top-level engine key into settings if not already set
                if (isset($config['engine']) && !isset($settings['engine'])) {
                    $settings['engine'] = $config['engine'];
                }
            } else {
                // Back-compat: treat array input as settings when no nested "settings" exists
                $settings = $config;
            }
        }

        if (!is_string($name) || $name === '') {
            $name = 'default';
        }

        if (isset($_this->configs[$name])) {
            $settings = array_merge($_this->configs[$name], $settings);
        } elseif (!empty($settings)) {
            $_this->configs[$name] = $settings;
        } elseif (
            is_string($_this->name)
            && $_this->name !== ''
            && isset($_this->configs[$_this->name])
        ) {
            $name     = $_this->name;
            $settings = $_this->configs[$_this->name];
        } else {
            $name = 'default';
            if (!empty($_this->configs['default'])) {
                $settings = $_this->configs['default'];
            } else {
                $settings = [
                    'engine' => 'file',
                ];
            }
        }

        $engine = (!empty($settings['engine']) && is_string($settings['engine']))
            ? $settings['engine']
            : 'file';

        // Normalize: ensure the resolved engine name is stored in settings
        $settings['engine'] = $engine;

        if ($name !== $_this->name) {
            if ($_this->engine($engine, $settings) === false) {
                trigger_error("Cache Engine {$engine} is not set", E_USER_WARNING);

                return false;
            }
            $_this->name           = $name;
            $_this->configs[$name] = $_this->settings($engine);
        } elseif (!empty($settings)) {
            // Same config name but caller supplied overrides – re-init the engine
            // so the returned {engine, settings} pair stays consistent.
            if ($_this->isInitialized($engine)) {
                $_this->engine[$engine]->init($settings);
            }
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
     * @return bool True on success, false on failure
     * @access public
     */
    public function engine($name = 'file', $settings = []): bool
    {
        if (!is_string($name) || $name === '') {
            return false;
        }
        if (!is_array($settings)) {
            $settings = [];
        }

        $cacheClass = 'XoopsCache' . ucfirst($name);
        $_this      = XoopsCache::getInstance();

        if (!isset($_this->engine[$name])) {
            if ($_this->loadEngine($name) === false) {
                trigger_error("Cache Engine {$name} is not loaded", E_USER_WARNING);
                return false;
            }

            $_this->engine[$name] = new $cacheClass();

            // Validate that the engine implements the required interface
            if (!$_this->engine[$name] instanceof XoopsCacheEngineInterface) {
                $_this->engine[$name] = null;
                trigger_error(
                    "Cache engine {$name} must implement XoopsCacheEngineInterface",
                    E_USER_WARNING
                );
                return false;
            }
        }

        if ($_this->engine[$name]->init($settings)) {
            // Safely check probability before using it via the engine's settings() method
            $engineSettings = $_this->engine[$name]->settings();
            $probability    = 0;
            if (is_array($engineSettings) && isset($engineSettings['probability'])) {
                $probability = (int)$engineSettings['probability'];
            }
            if ($probability > 0 && time() % $probability === 0) {
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
     * @return bool True on success, false on failure
     * @access public
     */
    public function gc(): bool
    {
        $_this  = XoopsCache::getInstance();
        $config = $_this->resolveEngineConfig(null);

        if ($config === false) {
            return false;
        }

        $engine = $config['engine'];

        // Engine configuration is valid, but runtime initialization may have failed
        if (!$_this->isInitialized($engine)) {
            trigger_error("Cache engine {$engine} not initialized for garbage collection", E_USER_WARNING);
            return false;
        }

        return (bool) $_this->engine[$engine]->gc();
    }

    /**
     * Write data for key into cache
     *
     * @param  string $key       Identifier for the data
     * @param  mixed  $value     Data to be cached - anything except a resource
     * @param  mixed  $duration  Optional - string configuration name OR how long to cache the data, either in seconds or a
     *                           string that can be parsed by the strtotime() function OR array('config' => 'default', 'duration' => '3600')
     * @return bool True if the data was successfully cached, false on failure
     * @access public
     */
    public static function write($key, $value, $duration = null): bool
    {
        $key    = substr(md5(XOOPS_URL), 0, 8) . '_' . $key;
        $_this  = XoopsCache::getInstance();

        $config = null;

        if (is_array($duration)) {
            if (isset($duration['config']) && is_string($duration['config'])) {
                $config = $duration['config'];
            }
            if (isset($duration['duration'])) {
                $duration = $duration['duration'];
            } else {
                $duration = null;
            }
        } elseif (
            is_string($duration)
            && $duration !== ''
            && isset($_this->configs[$duration])
        ) {
            $config   = $duration;
            $duration = null;
        }

        $config = $_this->resolveEngineConfig($config);
        if ($config === false) {
            return false;
        }

        $engine   = $config['engine'];
        $settings = $config['settings'];

        if (!isset($settings['duration'])) {
            $settings['duration'] = 0;
        }

        if (!$_this->isInitialized($engine)) {
            trigger_error('Cache write not initialized: ' . $engine, E_USER_WARNING);

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
        $duration = is_numeric($duration) ? (int) $duration : strtotime($duration) - time();

        if ($duration < 1) {
            return false;
        }
        $_this->engine[$engine]->init($settings);
        $success = (bool) $_this->engine[$engine]->write($key, $value, $duration);

        return $success;
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key    Identifier for the data
     * @param  string|array|null $config name of the configuration to use
     * @return mixed The cached data, or false/null if:
     *               - Configuration is invalid (false)
     *               - Data doesn't exist (false or null, engine-dependent)
     *               - Data has expired (false)
     *               - Error occurred during retrieval (false)
     * @access public
     */
    public static function read($key, $config = null)
    {
        $key    = substr(md5(XOOPS_URL), 0, 8) . '_' . $key;
        $_this  = XoopsCache::getInstance();

        $config = $_this->resolveEngineConfig($config);
        if ($config === false) {
            return false;
        }

        $engine   = $config['engine'];
        $settings = $config['settings'];

        if (!$_this->isInitialized($engine)) {
            return false;
        }
        if (!$key = $_this->key($key)) {
            return false;
        }
        $_this->engine[$engine]->init($settings);
        $result = $_this->engine[$engine]->read($key);

        return $result;
    }

    /**
     * Delete a key from the cache
     *
     * @param  string $key    Identifier for the data
     * @param string|null $config name of the configuration to use
     * @return boolean True if the value was successfully deleted, false if it didn't exist or couldn't be removed
     * @access public
     */
    public static function delete($key, $config = null): bool
    {
        $key   = substr(md5(XOOPS_URL), 0, 8) . '_' . $key;
        $_this = XoopsCache::getInstance();

        $config = $_this->resolveEngineConfig($config);
        if ($config === false) {
            return false;
        }

        $engine   = $config['engine'];
        $settings = $config['settings'];

        if (!$_this->isInitialized($engine)) {
            return false;
        }

        if (!$key = $_this->key($key)) {
            return false;
        }

        $_this->engine[$engine]->init($settings);
        $success = (bool) $_this->engine[$engine]->delete($key);

        return $success;
    }

    /**
     * Delete all keys from the cache
     *
     * @param  boolean $check  if true will check expiration, otherwise delete all
     * @param string|null $config name of the configuration to use
     * @return boolean True if the cache was successfully cleared, false otherwise
     * @access public
     */
    public function clear($check = false, $config = null): bool
    {
        $_this = XoopsCache::getInstance();

        $config = $_this->resolveEngineConfig($config);
        if ($config === false) {
            return false;
        }

        $engine   = $config['engine'];
        $settings = $config['settings'];

        if (!$_this->isInitialized($engine)) {
            return false;
        }

        $success = (bool) $_this->engine[$engine]->clear($check);
        $_this->engine[$engine]->init($settings);

        return $success;
    }

    /**
     * Check if Cache has initialized a working storage engine
     *
     * @param  string|null $engine Name of the engine
     * @return bool
     * @access public
     */
    public function isInitialized($engine = null)
    {
        $_this = XoopsCache::getInstance();
        $engine = $_this->resolveEngineName($engine);

        return is_string($engine)
            && isset($_this->engine[$engine])
            && $_this->engine[$engine] instanceof XoopsCacheEngineInterface;
    }

    /**
     * Return the settings for current cache engine
     *
     * @param  string|null $engine Name of the engine
     * @return array<string, mixed> list of settings for this engine
     * @access public
     */
    public function settings($engine = null): array
    {
        $_this = XoopsCache::getInstance();
        $engine = $_this->resolveEngineName($engine);

        if (
            is_string($engine) &&
            isset($_this->engine[$engine]) &&
            $_this->engine[$engine] instanceof XoopsCacheEngineInterface
        ) {
            return $_this->engine[$engine]->settings();
        }

        return [];
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
        $key = str_replace(['/', '.'], '_', (string) $key);

        return $key;
    }
}

/**
 * Interface for XOOPS cache engine implementations
 *
 * Defines the contract that all cache engines must implement.
 * Third-party cache engines should implement this interface.
 *
 * @category   XOOPS
 * @package    core
 * @subpackage cache
 * @author     XOOPS Project
 * @copyright  (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license    GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link       https://xoops.org
 * @since      2.5.12
 */
interface XoopsCacheEngineInterface
{
    /**
     * Initialize the cache engine
     *
     * @param  array $settings Associative array of parameters for the engine
     * @return bool True if the engine has been successfully initialized, false if not
     */
    public function init($settings = []);

    /**
     * Garbage collection - permanently remove all expired and deleted data
     *
     * @return bool True on success (including no-op when there is nothing to clean), false on failure
     */
    public function gc();

    /**
     * Write value for a key into cache
     *
     * @param  string   $key      Identifier for the data
     * @param  mixed    $value    Data to be cached
     * @param  int|null $duration How long to cache the data, in seconds
     * @return bool True if the data was successfully cached, false on failure
     */
    public function write($key, $value, $duration = null);

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed The cached data, or a falsy value (such as false or null) if the data
     *               doesn't exist or has expired. Implementations MUST NOT throw on cache
     *               miss; callers SHOULD treat any falsy return value as a cache miss.
     */
    public function read($key);

    /**
     * Delete a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return bool True if the value was successfully deleted, false otherwise
     */
    public function delete($key);

    /**
     * Delete all keys from the cache
     *
     * @param  bool $check If true will check expiration, otherwise delete all
     * @return bool True if the cache was successfully cleared, false otherwise
     */
    public function clear($check);

    /**
     * Return the settings for this cache engine
     *
     * @return array Associative array of current engine settings
     */
    public function settings();
}

/**
 * Abstract class for storage engine for caching
 *
 * @package    core
 * @subpackage cache
 */
abstract class XoopsCacheEngine implements XoopsCacheEngineInterface
{
    /**
     * settings of current engine instance
     *
     * @var array
     * @access public
     */
    public $settings;

    /**
     * Initialize the cache engine
     *
     * Called automatically by the cache frontend. This method may be called
     * multiple times to reconfigure the engine with different settings.
     * Implementations should handle repeated initialization gracefully.
     *
     * @param  array $settings Associative array of parameters for the engine
     * @return boolean True if the engine has been successfully initialized, false if not
     * @access   public
     */
    public function init($settings = [])
    {
        $this->settings = array_merge(
            [
                'duration'    => 31556926,
                'probability' => 100,
            ],
            $settings,
        );

        return true;
    }

    /**
     * Garbage collection
     *
     * Permanently remove all expired and deleted data
     *
     * @return bool True on success (including no-op), false on failure
     * @access public
     */
    public function gc()
    {
        return true;
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
        return false;
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
        return false;
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
