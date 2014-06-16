<?php
//  Author: Trabis
//  URL: http://www.xuups.com
//  E-Mail: lusopoemas@gmail.com

if (!defined("XOOPS_ROOT_PATH")) {
    die("XOOPS root path not defined");
}

/**
 * Class ProtectorRegistry
 */
class ProtectorRegistry
{
    var $_entries;
    var $_locks;

    function ProtectorRegistry()
    {
        $this->_entries = array();
        $this->_locks = array();
    }

    /**
     * @return ProtectorRegistry
     */
    static function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new ProtectorRegistry();
        }

        return $instance;
    }

    /**
     * @param $key
     * @param $item
     *
     * @return bool
     */
    function setEntry($key, $item)
    {
        if ($this->isLocked($key) == true) {
            trigger_error('Unable to set entry `' . $key . '`. Entry is locked.', E_USER_WARNING);

            return false;
        }

        $this->_entries[$key] = $item;

        return true;
    }

    /**
     * @param $key
     */
    function unsetEntry($key)
    {
        unset($this->_entries[$key]);
    }

    /**
     * @param $key
     *
     * @return null
     */
    function getEntry($key)
    {
        if (isset($this->_entries[$key]) == false) {
            return null;
        }

        return $this->_entries[$key];
    }

    /**
     * @param $key
     *
     * @return bool
     */
    function isEntry($key)
    {
        return ($this->getEntry($key) !== null);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    function lockEntry($key)
    {
        $this->_locks[$key] = true;

        return true;
    }

    /**
     * @param $key
     */
    function unlockEntry($key)
    {
        unset($this->_locks[$key]);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    function isLocked($key)
    {
        return (isset($this->_locks[$key]) == true);
    }

    function unsetAll()
    {
        $this->_entries = array();
        $this->_locks = array();
    }
}
