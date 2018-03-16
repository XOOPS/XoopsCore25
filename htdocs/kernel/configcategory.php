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
 * A category of configs
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 * @package             kernel
 */
class XoopsConfigCategory extends XoopsObject
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('confcat_id', XOBJ_DTYPE_INT, null);
        $this->initVar('confcat_name', XOBJ_DTYPE_OTHER, null);
        $this->initVar('confcat_order', XOBJ_DTYPE_INT, 0);
    }

    /**
     * Returns Class Base Variable confcat_id
     * @param  string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('confcat_id', $format);
    }

    /**
     * Returns Class Base Variable confcat_id
     * @param  string $format
     * @return mixed
     */
    public function confcat_id($format = '')
    {
        return $this->getVar('confcat_id', $format);
    }

    /**
     * Returns Class Base Variable confcat_name
     * @param  string $format
     * @return mixed
     */
    public function confcat_name($format = '')
    {
        return $this->getVar('confcat_name', $format);
    }

    /**
     * Returns Class Base Variable confcat_order
     * @param  string $format
     * @return mixed
     */
    public function confcat_order($format = '')
    {
        return $this->getVar('confcat_order', $format);
    }
}

/**
 * XOOPS configuration category handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS configuration category class objects.
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 * @package             kernel
 * @subpackage          config
 */
class XoopsConfigCategoryHandler extends XoopsObjectHandler
{
    /**
     * Create a new category
     *
     * @param bool $isNew Flag the new object as "new"?
     *
     * @return XoopsConfigCategory New {@link XoopsConfigCategory}
     */
    public function create($isNew = true)
    {
        $confcat = new XoopsConfigCategory();
        if ($isNew) {
            $confcat->setNew();
        }

        return $confcat;
    }

    /**
     * Retrieve a {@link XoopsConfigCategory}
     *
     * @param int $id ID
     *
     * @return XoopsConfigCategory {@link XoopsConfigCategory}, FALSE on fail
     */
    public function get($id)
    {
        $confcat = false;
        $id      = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('configcategory') . ' WHERE confcat_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $confcat;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $confcat = new XoopsConfigCategory();
                $confcat->assignVars($this->db->fetchArray($result));
            }
        }

        return $confcat;
    }

    /**
     * Store a {@link XoopsConfigCategory}
     *
     * @param XoopsObject|XoopsConfigCategory $confcat a XoopsConfigCategory object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $confcat)
    {
        $className = 'XoopsConfigCategory';
        if (!($confcat instanceof $className)) {
            return false;
        }
        if (!$confcat->isDirty()) {
            return true;
        }
        if (!$confcat->cleanVars()) {
            return false;
        }
        foreach ($confcat->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($confcat->isNew()) {
            $confcat_id = $this->db->genId('configcategory_confcat_id_seq');
            $sql        = sprintf('INSERT INTO %s (confcat_id, confcat_name, confcat_order) VALUES (%u, %s, %u)', $this->db->prefix('configcategory'), $confcat_id, $this->db->quoteString($confcat_name), $confcat_order);
        } else {
            $sql = sprintf('UPDATE %s SET confcat_name = %s, confcat_order = %u WHERE confcat_id = %u', $this->db->prefix('configcategory'), $this->db->quoteString($confcat_name), $confcat_order, $confcat_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($confcat_id)) {
            $confcat_id = $this->db->getInsertId();
        }
        $confcat->assignVar('confcat_id', $confcat_id);

        return $confcat_id;
    }

    /**
     * Delete a {@link XoopsConfigCategory}
     *
     * @param XoopsObject|XoopsConfigCategory $confcat a XoopsConfigCategory object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $confcat)
    {
        $className = 'XoopsConfigCategory';
        if (!($confcat instanceof $className)) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE confcat_id = %u', $this->db->prefix('configcategory'), $configcategory->getVar('confcat_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get some {@link XoopsConfigCategory}s
     *
     * @param CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement}
     * @param bool            $id_as_key Use the IDs as keys to the array?
     *
     * @return array Array of {@link XoopsConfigCategory}s
     */
    public function getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('configcategory');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sort = !in_array($criteria->getSort(), array(
                'confcat_id',
                'confcat_name',
                'confcat_order')) ? 'confcat_order' : $criteria->getSort();
            $sql .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $confcat = new XoopsConfigCategory();
            $confcat->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $confcat;
            } else {
                $ret[$myrow['confcat_id']] = &$confcat;
            }
            unset($confcat);
        }

        return $ret;
    }

    /**#@+
     * @deprecated
     * @param int $modid
     * @return bool
     */
    public function getCatByModule($modid = 0)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }
    /**#@-*/
}
