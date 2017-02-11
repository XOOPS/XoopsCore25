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
 * APC storage engine for cache.
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                  1785 E. Sahara Avenue, Suite 490-204
 *                                  Las Vegas, Nevada 89104
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
 * APC storage engine for cache
 *
 * @package    cake
 * @subpackage cake.cake.libs.cache
 */
class XoopsCacheApc extends XoopsCacheEngine
{
    /**
     * Initialize the Cache Engine
     *
     * Called automatically by the cache frontend
     * To reinitialize the settings call Cache::engine('EngineName', [optional] settings = array());
     *
     * @param array $settings array of setting for the engine
     *
     * @return boolean True if the engine has been successfully initialized, false if not
     * @see      CacheEngine::__defaults
     * @access   public
     */
    public function init($settings = array())
    {
        parent::init($settings);

        return function_exists('apc_cache_info');
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
        return apc_store($key, $value, $duration);
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
        return apc_fetch($key);
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
        return apc_delete($key);
    }

    /**
     * Delete all keys from the cache
     *
     * @return boolean True if the cache was successfully cleared, false otherwise
     * @access public
     */
    public function clear($check = null)
    {
        return apc_clear_cache('user');
    }
}
