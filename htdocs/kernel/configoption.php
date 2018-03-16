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

/**
 * A Config-Option
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 * @package             kernel
 */
class XoopsConfigOption extends XoopsObject
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('confop_id', XOBJ_DTYPE_INT, null);
        $this->initVar('confop_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('confop_value', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('conf_id', XOBJ_DTYPE_INT, 0);
    }

    /**
     * Returns Class Base Variable confop_id
     * @param  string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('confop_id', $format);
    }

    /**
     * Returns Class Base Variable confop_id
     * @param  string $format
     * @return mixed
     */
    public function confop_id($format = '')
    {
        return $this->getVar('confop_id', $format);
    }

    /**
     * Returns Class Base Variable confop_name
     * @param  string $format
     * @return mixed
     */
    public function confop_name($format = '')
    {
        return $this->getVar('confop_name', $format);
    }

    /**
     * Returns Class Base Variable confop_value
     * @param  string $format
     * @return mixed
     */
    public function confop_value($format = '')
    {
        return $this->getVar('confop_value', $format);
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
}

/**
 * XOOPS configuration option handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS configuration option class objects.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @author              Kazumi Ono <onokazu@xoops.org>
 *
 * @package             kernel
 * @subpackage          config
 */
class XoopsConfigOptionHandler extends XoopsObjectHandler
{
    /**
     * Create a new option
     *
     * @param bool $isNew Flag the option as "new"?
     *
     * @return XoopsConfigOption {@link XoopsConfigOption}
     */
    public function create($isNew = true)
    {
        $confoption = new XoopsConfigOption();
        if ($isNew) {
            $confoption->setNew();
        }

        return $confoption;
    }

    /**
     * Get an option from the database
     *
     * @param int $id ID of the option
     *
     * @return XoopsConfigOption reference to the {@link XoopsConfigOption}, FALSE on fail
     */
    public function get($id)
    {
        $confoption = false;
        $id         = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('configoption') . ' WHERE confop_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $confoption;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $confoption = new XoopsConfigOption();
                $confoption->assignVars($this->db->fetchArray($result));
            }
        }

        return $confoption;
    }

    /**
     * Insert a new {@link XoopsConfigOption}
     *
     * @param XoopsObject|XoopsConfigOption $confoption a XoopsConfigOption object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $confoption)
    {
        $className = 'XoopsConfigOption';
        if (!($confoption instanceof $className)) {
            return false;
        }
        if (!$confoption->isDirty()) {
            return true;
        }
        if (!$confoption->cleanVars()) {
            return false;
        }

        $confop_id = $confoption->getVar('confop_id');
        $confop_name = $confoption->getVar('confop_name');
        $confop_value = $confoption->getVar('confop_value');
        $conf_id = $confoption->getVar('conf_id');

        if ($confoption->isNew()) {
            $confop_id = $this->db->genId('configoption_confop_id_seq');
            $sql       = sprintf(
                'INSERT INTO %s (confop_id, confop_name, confop_value, conf_id) VALUES (%u, %s, %s, %u)',
                $this->db->prefix('configoption'),
                $confop_id,
                $this->db->quote($confop_name),
                $this->db->quote($confop_value),
                $conf_id
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET confop_name = %s, confop_value = %s WHERE confop_id = %u',
                $this->db->prefix('configoption'),
                $this->db->quote($confop_name),
                $this->db->quote($confop_value),
                $confop_id
            );
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($confop_id)) {
            $confop_id = $this->db->getInsertId();
        }
        $confoption->assignVar('confop_id', $confop_id);

        return $confop_id;
    }

    /**
     * Delete a {@link XoopsConfigOption}
     *
     * @param XoopsObject|XoopsConfigOption $confoption a XoopsConfigOption object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $confoption)
    {
        $className = 'XoopsConfigOption';
        if (!($confoption instanceof $className)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE confop_id = %u', $this->db->prefix('configoption'), $confoption->getVar('confop_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get some {@link XoopsConfigOption}s
     *
     * @param CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement}
     * @param bool            $id_as_key Use the IDs as array-keys?
     *
     * @return array Array of {@link XoopsConfigOption}s
     */
    public function getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('configoption');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere() . ' ORDER BY confop_id ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $confoption = new XoopsConfigOption();
            $confoption->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $confoption;
            } else {
                $ret[$myrow['confop_id']] = &$confoption;
            }
            unset($confoption);
        }

        return $ret;
    }
}
