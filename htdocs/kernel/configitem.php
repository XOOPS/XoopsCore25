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

/**#@+
 * Config type
 */
define('XOOPS_CONF', 1);
define('XOOPS_CONF_USER', 2);
define('XOOPS_CONF_METAFOOTER', 3);
define('XOOPS_CONF_CENSOR', 4);
define('XOOPS_CONF_SEARCH', 5);
define('XOOPS_CONF_MAILER', 6);
define('XOOPS_CONF_AUTH', 7);
/**#@-*/

/**
 *
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsConfigItem extends XoopsObject
{
    /**
     * Config options
     *
     * @var array
     * @access    private
     */
    public $_confOptions = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initVar('conf_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_modid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_catid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_name', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('conf_value', XOBJ_DTYPE_TXTAREA);
        $this->initVar('conf_desc', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_formtype', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_valuetype', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_order', XOBJ_DTYPE_INT);
    }

    /**
     * Returns Class Base Variable conf_id
     * @param  string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('conf_id', $format);
    }

    /**
     * Returns Class Base Variable conf_id
     * @param  string $format
     * @return mixed
     */
    public function conf_id($format = '')
    {
        return $this->getVar('conf_id', $format);
    }

    /**
     * Returns Class Base Variable conf_modid
     * @param  string $format
     * @return mixed
     */
    public function conf_modid($format = '')
    {
        return $this->getVar('conf_modid', $format);
    }

    /**
     * Returns Class Base Variable conf_catid
     * @param  string $format
     * @return mixed
     */
    public function conf_catid($format = '')
    {
        return $this->getVar('conf_catid', $format);
    }

    /**
     * Returns Class Base Variable conf_name
     * @param  string $format
     * @return mixed
     */
    public function conf_name($format = '')
    {
        return $this->getVar('conf_name', $format);
    }

    /**
     * Returns Class Base Variable conf_title
     * @param  string $format
     * @return mixed
     */
    public function conf_title($format = '')
    {
        return $this->getVar('conf_title', $format);
    }

    /**
     * Returns Class Base Variable conf_value
     * @param  string $format
     * @return mixed
     */
    public function conf_value($format = '')
    {
        return $this->getVar('conf_value', $format);
    }

    /**
     * Returns Class Base Variable conf_desc
     * @param  string $format
     * @return mixed
     */
    public function conf_desc($format = '')
    {
        return $this->getVar('conf_desc', $format);
    }

    /**
     * Returns Class Base Variable conf_formtype
     * @param  string $format
     * @return mixed
     */
    public function conf_formtype($format = '')
    {
        return $this->getVar('conf_formtype', $format);
    }

    /**
     * Returns Class Base Variable conf_valuetype
     * @param  string $format
     * @return mixed
     */
    public function conf_valuetype($format = '')
    {
        return $this->getVar('conf_valuetype', $format);
    }

    /**
     * Returns Class Base Variable conf_order
     * @param  string $format
     * @return mixed
     */
    public function conf_order($format = '')
    {
        return $this->getVar('conf_order', $format);
    }

    /**
     * Get a config value in a format ready for output
     *
     * @return string
     */
    public function getConfValueForOutput()
    {
        switch ($this->getVar('conf_valuetype')) {
            case 'int':
                return (int)$this->getVar('conf_value', 'N');
                break;
            case 'array':
                $value = @unserialize($this->getVar('conf_value', 'N'));

                return $value ?: array();
            case 'float':
                $value = $this->getVar('conf_value', 'N');

                return (float)$value;
                break;
            case 'textarea':
                return $this->getVar('conf_value');
            default:
                return $this->getVar('conf_value', 'N');
                break;
        }
    }

    /**
     * Set a config value
     *
     * @param mixed &$value Value
     * @param bool  $force_slash
     */
    public function setConfValueForInput(&$value, $force_slash = false)
    {
        switch ($this->getVar('conf_valuetype')) {
            case 'array':
                if (!is_array($value)) {
                    $value = explode('|', trim($value));
                }
                $this->setVar('conf_value', serialize($value), $force_slash);
                break;
            case 'text':
                $this->setVar('conf_value', trim($value), $force_slash);
                break;
            default:
                $this->setVar('conf_value', $value, $force_slash);
                break;
        }
    }

    /**
     * Assign one or more {@link XoopsConfigItemOption}s
     *
     * @param mixed $option either a {@link XoopsConfigItemOption} object or an array of them
     */
    public function setConfOptions($option)
    {
        if (is_array($option)) {
            $count = count($option);
            for ($i = 0; $i < $count; ++$i) {
                $this->setConfOptions($option[$i]);
            }
        } else {
            if (is_object($option)) {
                $this->_confOptions[] =& $option;
            }
        }
    }

    /**
     * Get the {@link XoopsConfigItemOption}s of this Config
     *
     * @return array array of {@link XoopsConfigItemOption}
     */
    public function &getConfOptions()
    {
        return $this->_confOptions;
    }

    /**
     * Clear options from this item
     *
     * @return void
     **/
    public function clearConfOptions()
    {
        $this->_confOptions = array();
    }
}

/**
 * XOOPS configuration handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS configuration class objects.
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsConfigItemHandler extends XoopsObjectHandler
{
    /**
     * Create a new {@link XoopsConfigItem}
     *
     * @see     XoopsConfigItem
     * @param  bool $isNew Flag the config as "new"?
     * @return XoopsConfigItem reference to the new config
     */
    public function create($isNew = true)
    {
        $config = new XoopsConfigItem();
        if ($isNew) {
            $config->setNew();
        }

        return $config;
    }

    /**
     * Load a config from the database
     *
     * @param  int $id ID of the config
     * @return XoopsConfigItem reference to the config, FALSE on fail
     */
    public function get($id)
    {
        $config = false;
        $id     = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('config') . ' WHERE conf_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $config;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $myrow  = $this->db->fetchArray($result);
                $config = new XoopsConfigItem();
                $config->assignVars($myrow);
            }
        }

        return $config;
    }

    /**
     * Write a config to the database
     *
     * @param XoopsObject|XoopsConfigItem $config a XoopsConfigCategory object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $config)
    {
        $className = 'XoopsConfigItem';
        if (!($config instanceof $className)) {
            return false;
        }
        if (!$config->isDirty()) {
            return true;
        }
        if (!$config->cleanVars()) {
            return false;
        }

        $conf_id = $config->getVar('conf_id', 'n');
        $conf_modid = $config->getVar('conf_modid', 'n');
        $conf_catid = $config->getVar('conf_catid', 'n');
        $conf_name = $config->getVar('conf_name', 'n');
        $conf_title = $config->getVar('conf_title', 'n');
        $conf_value = $config->getVar('conf_value', 'n');
        $conf_desc = $config->getVar('conf_desc', 'n');
        $conf_formtype = $config->getVar('conf_formtype', 'n');
        $conf_valuetype = $config->getVar('conf_valuetype', 'n');
        $conf_order = $config->getVar('conf_order', 'n');

        if ($config->isNew()) {
            $conf_id = $this->db->genId('config_conf_id_seq');
            $sql     = sprintf('INSERT INTO %s (conf_id, conf_modid, conf_catid, conf_name, conf_title, conf_value, conf_desc, conf_formtype, conf_valuetype, conf_order) VALUES (%u, %u, %u, %s, %s, %s, %s, %s, %s, %u)', $this->db->prefix('config'), $conf_id, $conf_modid, $conf_catid, $this->db->quoteString($conf_name), $this->db->quoteString($conf_title), $this->db->quoteString($conf_value), $this->db->quoteString($conf_desc), $this->db->quoteString($conf_formtype), $this->db->quoteString($conf_valuetype), $conf_order);
        } else {
            $sql = sprintf('UPDATE %s SET conf_modid = %u, conf_catid = %u, conf_name = %s, conf_title = %s, conf_value = %s, conf_desc = %s, conf_formtype = %s, conf_valuetype = %s, conf_order = %u WHERE conf_id = %u', $this->db->prefix('config'), $conf_modid, $conf_catid, $this->db->quoteString($conf_name), $this->db->quoteString($conf_title), $this->db->quoteString($conf_value), $this->db->quoteString($conf_desc), $this->db->quoteString($conf_formtype), $this->db->quoteString($conf_valuetype), $conf_order, $conf_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($conf_id)) {
            $conf_id = $this->db->getInsertId();
        }
        $config->assignVar('conf_id', $conf_id);

        return true;
    }

    /**
     * Delete a config from the database
     *
     * @param XoopsObject|XoopsConfigItem $config a XoopsConfigCategory object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $config)
    {
        $className = 'XoopsConfigItem';
        if (!($config instanceof $className)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE conf_id = %u', $this->db->prefix('config'), $config->getVar('conf_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get configs from the database
     *
     * @param  CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement}
     * @param  bool            $id_as_key return the config's id as key?
     * @return array           Array of {@link XoopsConfigItem} objects
     */
    public function getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('config');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' ORDER BY conf_order ASC';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $config = new XoopsConfigItem();
            $config->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $config;
            } else {
                $ret[$myrow['conf_id']] = &$config;
            }
            unset($config);
        }

        return $ret;
    }

    /**
     * Count configs
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement}
     * @return int             Count of configs matching $criteria
     */
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql   = 'SELECT * FROM ' . $this->db->prefix('config');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }
}
