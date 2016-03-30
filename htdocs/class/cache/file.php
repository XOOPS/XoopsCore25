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
 * File Storage engine for cache
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                       1785 E. Sahara Avenue, Suite 490-204
 *                                       Las Vegas, Nevada 89104
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
 * File Storage engine for cache
 *
 * @todo       use the File and Folder classes (if it's not a too big performance hit)
 * @package    cake
 * @subpackage cake.cake.libs.cache
 */
class XoopsCacheFile extends XoopsCacheEngine
{
    /**
     * Instance of File class
     *
     * @var object
     * @access private
     */
    private $file;

    /**
     * settings
     *                path = absolute path to cache directory, default => CACHE
     *                prefix = string prefix for filename, default => xoops_
     *                lock = enable file locking on write, default => false
     *                serialize = serialize the data, default => false
     *
     * @var array
     * @see    CacheEngine::__defaults
     * @access public
     */
    public $settings = array();

    /**
     * Set to true if FileEngine::init(); and FileEngine::active(); do not fail.
     *
     * @var boolean
     * @access private
     */
    private $active = false;

    /**
     * True unless FileEngine::active(); fails
     *
     * @var boolean
     * @access private
     */
    private $init = true;

    /**
     * Initialize the Cache Engine
     *
     * Called automatically by the cache frontend
     * To reinitialize the settings call Cache::engine('EngineName', [optional] settings = array());
     *
     * @param  array $settings array of setting for the engine
     * @return boolean True if the engine has been successfully initialized, false if not
     * @access   public
     */
    public function init($settings = array())
    {
        parent::init($settings);
        $defaults       = array(
            'path'      => XOOPS_VAR_PATH . '/caches/xoops_cache',
            'extension' => '.php',
            'prefix'    => 'xoops_',
            'lock'      => false,
            'serialize' => false,
            'duration'  => 31556926);
        $this->settings = array_merge($defaults, $this->settings);
        if (!isset($this->file)) {
            XoopsLoad::load('XoopsFile');
            $this->file = XoopsFile::getHandler('file', $this->settings['path'] . '/index.html', true);
        }
        $this->settings['path'] = $this->file->folder->cd($this->settings['path']);
        if (empty($this->settings['path'])) {
            return false;
        }

        return $this->active();
    }

    /**
     * Garbage collection. Permanently remove all expired and deleted data
     *
     * @return boolean True if garbage collection was successful, false on failure
     * @access public
     */
    public function gc()
    {
        return $this->clear(true);
    }

    /**
     * Write data for key into cache
     *
     * @param  string $key      Identifier for the data
     * @param  mixed  $data     Data to be cached
     * @param  mixed  $duration How long to cache the data, in seconds
     * @return boolean True if the data was successfully cached, false on failure
     * @access public
     */
    public function write($key, $data = null, $duration = null)
    {
        if (!isset($data) || !$this->init) {
            return false;
        }

        if ($this->setKey($key) === false) {
            return false;
        }

        if ($duration == null) {
            $duration = $this->settings['duration'];
        }
        $windows   = false;
        $lineBreak = "\n";

        if (substr(PHP_OS, 0, 3) === 'WIN') {
            $lineBreak = "\r\n";
            $windows   = true;
        }
        $expires = time() + $duration;
        if (!empty($this->settings['serialize'])) {
            if ($windows) {
                $data = str_replace('\\', '\\\\\\\\', serialize($data));
            } else {
                $data = serialize($data);
            }
            $contents = $expires . $lineBreak . $data . $lineBreak;
        } else {
            $contents = $expires . $lineBreak . 'return ' . var_export($data, true) . ';' . $lineBreak;
        }

        if ($this->settings['lock']) {
            $this->file->lock = true;
        }
        $success = $this->file->write($contents);
        $this->file->close();

        return $success;
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
        if ($this->setKey($key) === false || !$this->init) {
            return false;
        }
        if ($this->settings['lock']) {
            $this->file->lock = true;
        }
        $cachetime = $this->file->read(11);

        if ($cachetime !== false && (int)$cachetime < time()) {
            $this->file->close();
            $this->file->delete();

            return false;
        }

        $data = $this->file->read(true);
        if (!empty($data) && !empty($this->settings['serialize'])) {
            $data = stripslashes($data);
            // $data = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $data);
            $data = preg_replace_callback('!s:(\d+):"(.*?)";!s', function ($m) { return 's:' . strlen($m[2]) . ':"' . $m[2] . '";'; }, $data);
            $data = unserialize($data);
            if (is_array($data)) {
                XoopsLoad::load('XoopsUtility');
                $data = XoopsUtility::recursive('stripslashes', $data);
            }
        } elseif ($data && empty($this->settings['serialize'])) {
            $data = eval($data);
        }
        $this->file->close();

        return $data;
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
        if ($this->setKey($key) === false || !$this->init) {
            return false;
        }

        return $this->file->delete();
    }

    /**
     * Delete all values from the cache
     *
     * @param  boolean $check Optional - only delete expired cache items
     * @return boolean True if the cache was successfully cleared, false otherwise
     * @access public
     */
    public function clear($check = true)
    {
        if (!$this->init) {
            return false;
        }
        $dir = dir($this->settings['path']);
        if ($check) {
            $now       = time();
            $threshold = $now - $this->settings['duration'];
        }
        while (($entry = $dir->read()) !== false) {
            if ($this->setKey(str_replace($this->settings['prefix'], '', $entry)) === false) {
                continue;
            }
            if ($check) {
                $mtime = $this->file->lastChange();

                if ($mtime === false || $mtime > $threshold) {
                    continue;
                }

                $expires = $this->file->read(11);
                $this->file->close();

                if ($expires > $now) {
                    continue;
                }
            }
            $this->file->delete();
        }
        $dir->close();

        return true;
    }

    /**
     * Get absolute file for a given key
     *
     * @param  string $key The key
     * @return mixed  Absolute cache file for the given key or false if erroneous
     * @access private
     */
    private function setKey($key)
    {
        $this->file->folder->cd($this->settings['path']);
        $this->file->name   = $this->settings['prefix'] . $key . $this->settings['extension'];
        $this->file->handle = null;
        $this->file->info   = null;
        if (!$this->file->folder->inPath($this->file->pwd(), true)) {
            return false;
        }
        return null;
    }

    /**
     * Determine is cache directory is writable
     *
     * @return boolean
     * @access private
     */
    private function active()
    {
        if (!$this->active && $this->init && !is_writable($this->settings['path'])) {
            $this->init = false;
            trigger_error(sprintf('%s is not writable', $this->settings['path']), E_USER_WARNING);
        } else {
            $this->active = true;
        }

        return true;
    }
}
