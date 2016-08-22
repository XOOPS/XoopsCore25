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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

require_once $GLOBALS['xoops']->path('kernel/configoption.php');
require_once $GLOBALS['xoops']->path('kernel/configitem.php');

/**
 * XOOPS configuration handling class.
 * This class acts as an interface for handling general configurations of XOOPS
 * and its modules.
 *
 * @author  Kazumi Ono <webmaster@myweb.ne.jp>
 * @todo    Tests that need to be made:
 *          - error handling
 * @access  public
 */
class XoopsConfigHandler
{
    /**
     * holds reference to config item handler(DAO) class
     *
     * @var object
     * @access    private
     */
    public $_cHandler;

    /**
     * holds reference to config option handler(DAO) class
     *
     * @var object
     * @access    private
     */
    public $_oHandler;

    /**
     * holds an array of cached references to config value arrays,
     *  indexed on module id and category id
     *
     * @var array
     * @access  private
     */
    public $_cachedConfigs = array();

    /**
     * Constructor
     *
     * @param XoopsDatabase $db reference to database object
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->_cHandler = new XoopsConfigItemHandler($db);
        $this->_oHandler = new XoopsConfigOptionHandler($db);
    }

    /**
     * Create a config
     *
     * @see     XoopsConfigItem
     * @return XoopsConfigItem reference to the new {@link XoopsConfigItem}
     */
    public function createConfig()
    {
        $instance = $this->_cHandler->create();

        return $instance;
    }

    /**
     * Get a config
     *
     * @param  int  $id          ID of the config
     * @param  bool $withoptions load the config's options now?
     * @return object reference to the {@link XoopsConfig}
     */
    public function getConfig($id, $withoptions = false)
    {
        $config = $this->_cHandler->get($id);
        if ($withoptions === true) {
            $config->setConfOptions($this->getConfigOptions(new Criteria('conf_id', $id)));
        }

        return $config;
    }

    /**
     * insert a new config in the database
     *
     * @param XoopsConfigItem $config reference to the {@link XoopsConfigItem}
     *
     * @return bool
     */
    public function insertConfig(XoopsConfigItem $config)
    {
        if (!$this->_cHandler->insert($config)) {
            return false;
        }
        $options =& $config->getConfOptions();
        $count   = count($options);
        $conf_id = $config->getVar('conf_id');
        for ($i = 0; $i < $count; ++$i) {
            $options[$i]->setVar('conf_id', $conf_id);
            if (!$this->_oHandler->insert($options[$i])) {
                foreach ($options[$i]->getErrors() as $msg) {
                    $config->setErrors($msg);
                }
            }
        }
        if (!empty($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')])) {
            unset($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')]);
        }

        return true;
    }

    /**
     * Delete a config from the database
     *
     * @param XoopsConfigItem $config reference to a {@link XoopsConfigItem}
     *
     * @return bool
     */
    public function deleteConfig(XoopsConfigItem $config)
    {
        if (!$this->_cHandler->delete($config)) {
            return false;
        }
        $options = &$config->getConfOptions();
        $count   = count($options);
        if ($count == 0) {
            $options = $this->getConfigOptions(new Criteria('conf_id', $config->getVar('conf_id')));
            $count   = count($options);
        }
        if (is_array($options) && $count > 0) {
            for ($i = 0; $i < $count; ++$i) {
                $this->_oHandler->delete($options[$i]);
            }
        }
        if (!empty($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')])) {
            unset($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')]);
        }

        return true;
    }

    /**
     * get one or more Configs
     *
     * @param CriteriaElement $criteria     {@link CriteriaElement}
     * @param bool            $id_as_key    Use the configs' ID as keys?
     * @param bool            $with_options get the options now?
     *
     * @return array Array of {@link XoopsConfigItem} objects
     */
    public function getConfigs(CriteriaElement $criteria = null, $id_as_key = false, $with_options = false)
    {
        return $this->_cHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * Count some configs
     *
     * @param CriteriaElement $criteria {@link CriteriaElement}
     *
     * @return int
     */
    public function getConfigCount(CriteriaElement $criteria = null)
    {
        return $this->_cHandler->getCount($criteria);
    }

    /**
     * Get configs from a certain category
     *
     * @param int $category ID of a category
     * @param int $module   ID of a module
     *
     * @return array array of {@link XoopsConfig}s
     */
    public function &getConfigsByCat($category, $module = 0)
    {
        static $_cachedConfigs;
        if (!empty($_cachedConfigs[$module][$category])) {
            return $_cachedConfigs[$module][$category];
        } else {
            $ret      = array();
            $criteria = new CriteriaCompo(new Criteria('conf_modid', (int)$module));
            if (!empty($category)) {
                $criteria->add(new Criteria('conf_catid', (int)$category));
            }
            $configs = $this->getConfigs($criteria, true);
            if (is_array($configs)) {
                foreach (array_keys($configs) as $i) {
                    $ret[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
                }
            }
            $_cachedConfigs[$module][$category] = $ret;

            return $_cachedConfigs[$module][$category];
        }
    }

    /**
     * Make a new {@link XoopsConfigOption}
     *
     * @return XoopsConfigOption {@link XoopsConfigOption}
     */
    public function createConfigOption()
    {
        $inst = $this->_oHandler->create();

        return $inst;
    }

    /**
     * Get a {@link XoopsConfigOption}
     *
     * @param int $id ID of the config option
     *
     * @return XoopsConfigOption {@link XoopsConfigOption}
     */
    public function getConfigOption($id)
    {
        $inst = $this->_oHandler->get($id);

        return $inst;
    }

    /**
     * Get one or more {@link XoopsConfigOption}s
     *
     * @param CriteriaElement $criteria  {@link CriteriaElement}
     * @param bool            $id_as_key Use IDs as keys in the array?
     *
     * @return array Array of {@link XoopsConfigOption}s
     */
    public function getConfigOptions(CriteriaElement $criteria = null, $id_as_key = false)
    {
        return $this->_oHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * Count some {@link XoopsConfigOption}s
     *
     * @param CriteriaElement $criteria {@link CriteriaElement}
     *
     * @return int Count of {@link XoopsConfigOption}s matching $criteria
     */
    public function getConfigOptionsCount(CriteriaElement $criteria = null)
    {
        return $this->_oHandler->getCount($criteria);
    }

    /**
     * Get a list of configs
     *
     * @param int $conf_modid ID of the modules
     * @param int $conf_catid ID of the category
     *
     * @return array Associative array of name=>value pairs.
     */
    public function getConfigList($conf_modid, $conf_catid = 0)
    {
        if (!empty($this->_cachedConfigs[$conf_modid][$conf_catid])) {
            return $this->_cachedConfigs[$conf_modid][$conf_catid];
        } else {
            $criteria = new CriteriaCompo(new Criteria('conf_modid', $conf_modid));
            if (empty($conf_catid)) {
                $criteria->add(new Criteria('conf_catid', $conf_catid));
            }
            $configs   = $this->_cHandler->getObjects($criteria);
            $confcount = count($configs);
            $ret       = array();
            for ($i = 0; $i < $confcount; ++$i) {
                $ret[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
            }
            $this->_cachedConfigs[$conf_modid][$conf_catid] = &$ret;

            return $ret;
        }
    }

    /**#@+
     * @deprecated
     * @param $criteria
     * @return bool
     */
    public function deleteConfigOption(&$criteria)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }
    /**#@-*/
}
