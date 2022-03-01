<?php namespace XoopsModules\Protector;

//  Author: Trabis
//  URL: http://www.xuups.com
//  E-Mail: lusopoemas@gmail.com

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class Registry
 */
class Registry
{
    /**
     * @var array
     */
    public $_entries;
    /**
     * @var array
     */
    public $_locks;

    /**
     * Registry constructor.
     */
    protected function __construct()
    {
        $this->_entries = array();
        $this->_locks   = array();
    }

    /**
     * @return Registry
     */
    public static function getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new Registry();
        }

        return $instance;
    }

    /**
     * @param $key
     * @param $item
     *
     * @return bool
     */
    public function setEntry($key, $item)
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
    public function unsetEntry($key)
    {
        unset($this->_entries[$key]);
    }

    /**
     * @param $key
     *
     * @return mixed|string|null
     */
    public function getEntry($key)
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
    public function isEntry($key)
    {
        return ($this->getEntry($key) !== null);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function lockEntry($key)
    {
        $this->_locks[$key] = true;

        return true;
    }

    /**
     * @param $key
     */
    public function unlockEntry($key)
    {
        unset($this->_locks[$key]);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function isLocked($key)
    {
        return (isset($this->_locks[$key]) == true);
    }

    public function unsetAll()
    {
        $this->_entries = array();
        $this->_locks   = array();
    }
}
