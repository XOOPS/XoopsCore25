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
 * Memcache storage engine for cache
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                 1785 E. Sahara Avenue, Suite 490-204
 *                                 Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright  Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link       http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package    cake
 * @subpackage cake.cake.libs.cache
 * @since      CakePHP(tm) v 1.2.0.4933
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Memcache storage engine for cache
 *
 * @package    cake
 * @subpackage cake.cake.libs.cache
 */
class XoopsCacheMemcache extends XoopsCacheEngine
{
    /**
     * Memcache wrapper.
     *
     * @var object
     * @access private
     */
    private $memcache;

    /**
     * settings
     *          servers = string or array of memcache servers, default => 127.0.0.1
     *          compress = boolean, default => false
     *
     * @var array
     * @access public
     */
    public $settings = array();

    /**
     * Initialize the Cache Engine
     *
     * Called automatically by the cache frontend
     * To reinitialize the settings call Cache::engine('EngineName', [optional] settings = array());
     *
     * @param array $settings array of setting for the engine
     *
     * @return boolean True if the engine has been successfully initialized, false if not
     * @access   public
     */
    public function init($settings = array())
    {
        if (!class_exists('Memcache')) {
            return false;
        }
        parent::init($settings);
        $defaults       = array(
            'servers'  => array(
                '127.0.0.1'),
            'compress' => false);
        $this->settings = array_merge($defaults, $this->settings);

        if (!$this->settings['compress']) {
            $this->settings['compress'] = MEMCACHE_COMPRESSED;
        }
        if (!is_array($this->settings['servers'])) {
            $this->settings['servers'] = array($this->settings['servers']);
        }
        $this->memcache = null;
        $this->memcache = new Memcache();
        foreach ($this->settings['servers'] as $server) {
            $parts = explode(':', $server);
            $host  = $parts[0];
            $port  = 11211;
            if (isset($parts[1])) {
                $port = $parts[1];
            }
            if ($this->memcache->addServer($host, $port)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Write data for key into cache
     *
     * @param  string  $key      Identifier for the data
     * @param  mixed   $value    Data to be cached
     * @param  integer $duration How long to cache the data, in seconds
     * @return boolean True if the data was successfully cached, false on failure
     * @access public
     */
    public function write($key, $value, $duration = null)
    {
        return $this->memcache->set($key, $value, $this->settings['compress'], $duration);
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
        return $this->memcache->get($key);
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
        return $this->memcache->delete($key);
    }

    /**
     * Delete all keys from the cache
     *
     * @return boolean True if the cache was successfully cleared, false otherwise
     * @access public
     */
    public function clear($check = null)
    {
        return $this->memcache->flush();
    }

    /**
     * Connects to a server in connection pool
     *
     * @param  string  $host host ip address or name
     * @param  integer $port Server port
     * @return boolean True if memcache server was connected
     * @access public
     */
    public function connect($host, $port = 11211)
    {
        if ($this->memcache->getServerStatus($host, $port) === 0) {
            if ($this->memcache->connect($host, $port)) {
                return true;
            }

            return false;
        }

        return true;
    }
}
