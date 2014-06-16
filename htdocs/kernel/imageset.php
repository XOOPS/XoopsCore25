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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @since           2.0.0
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000 XOOPS.org
 **/

/**
 * XOOPS Image Sets
 *
 * @package     kernel
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   (c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsImageset extends XoopsObject
{

    function XoopsImageset()
    {
        $this->XoopsObject();
        $this->initVar('imgset_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('imgset_name', XOBJ_DTYPE_TXTBOX, null, true, 50);
        $this->initVar('imgset_refid', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Returns Class Base Variable imgset_id
     */
    function id($format = 'N')
    {
        return $this->getVar('imgset_id', $format);
    }

    /**
     * Returns Class Base Variable imgset_id
     */
    function imgset_id($format = '')
    {
        return $this->getVar('imgset_id', $format);
    }

    /**
     * Returns Class Base Variable imgset_name
     */
    function imgset_name($format = '')
    {
        return $this->getVar('imgset_name', $format);
    }

    /**
     * Returns Class Base Variable imgset_refid
     */
    function imgset_refid($format = '')
    {
        return $this->getVar('imgset_refid', $format);
    }

}

/**
 * XOOPS imageset handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS imageset class objects.
 *
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 */
class XoopsImagesetHandler extends XoopsObjectHandler
{
    /**
     * Create a new {@link XoopsImageSet}
     *
     * @param  boolean $isNew Flag the object as "new"
     * @return object
     **/
    function &create($isNew = true)
    {
        $imgset = new XoopsImageset();
        if ($isNew) {
            $imgset->setNew();
        }

        return $imgset;
    }

    /**
     * Load a {@link XoopsImageSet} object from the database
     *
     * @param int $id ID
     *
     * @internal param bool $getbinary
     * @return object {@link XoopsImageSet}, FALSE on fail
     */
    function &get($id)
    {
        $id = intval($id);
        $imgset = false;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('imgset') . ' WHERE imgset_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $imgset;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $imgset = new XoopsImageset();
                $imgset->assignVars($this->db->fetchArray($result));
            }
        }

        return $imgset;
    }

    /**
     * Write a {@link XoopsImageSet} object to the database
     *
     * @param  object $imgset
     * @internal param object $image {@link XoopsImageSet}
     * @return bool
     */
    function insert(&$imgset)
    {
        /**
         * @TODO: Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
         */
        if (!is_a($imgset, 'xoopsimageset')) {
            return false;
        }

        if (!$imgset->isDirty()) {
            return true;
        }
        if (!$imgset->cleanVars()) {
            return false;
        }
        foreach ($imgset->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($imgset->isNew()) {
            $imgset_id = $this->db->genId('imgset_imgset_id_seq');
            $sql = sprintf("INSERT INTO %s (imgset_id, imgset_name, imgset_refid) VALUES (%u, %s, %u)", $this->db->prefix('imgset'), $imgset_id, $this->db->quoteString($imgset_name), $imgset_refid);
        } else {
            $sql = sprintf("UPDATE %s SET imgset_name = %s, imgset_refid = %u WHERE imgset_id = %u", $this->db->prefix('imgset'), $this->db->quoteString($imgset_name), $imgset_refid, $imgset_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($imgset_id)) {
            $imgset_id = $this->db->getInsertId();
        }
        $imgset->assignVar('imgset_id', $imgset_id);

        return true;
    }

    /**
     * Delete an XoopsImageSet from the database
     *
     * @param  object $imgset
     * @internal param object $image {@link XoopsImageSet}
     * @return bool
     */
    function delete(&$imgset)
    {
        /**
         * @TODO: Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
         */
        if (!is_a($imgset, 'xoopsimageset')) {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE imgset_id = %u", $this->db->prefix('imgset'), $imgset->getVar('imgset_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE imgset_id = %u", $this->db->prefix('imgset_tplset_link'), $imgset->getVar('imgset_id'));
        $this->db->query($sql);

        return true;
    }

    /**
     * Load {@link XoopsImageSet}s from the database
     *
     * @param  object  $criteria  {@link CriteriaElement}
     * @param  boolean $id_as_key Use the ID as key into the array
     * @internal param bool $getbinary
     * @return array   Array of {@link XoopsImageSet} objects
     */
    function getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT i.* FROM ' . $this->db->prefix('imgset') . ' i LEFT JOIN ' . $this->db->prefix('imgset_tplset_link') . ' l ON l.imgset_id=i.imgset_id';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $imgset = new XoopsImageset();
            $imgset->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $imgset;
            } else {
                $ret[$myrow['imgset_id']] =& $imgset;
            }
            unset($imgset);
        }

        return $ret;
    }

    /**
     * Load {@link XoopsImage ThemeSet}s into a Database
     *
     * @param  int    $imgset_id
     * @param  string $tplset_name
     * @return array
     */
    function linkThemeset($imgset_id, $tplset_name)
    {
        $imgset_id = intval($imgset_id);
        $tplset_name = trim($tplset_name);
        if ($imgset_id <= 0 || $tplset_name == '') {
            return false;
        }
        if (!$this->unlinkThemeset($imgset_id, $tplset_name)) {
            return false;
        }
        $sql = sprintf("INSERT INTO %s (imgset_id, tplset_name) VALUES (%u, %s)", $this->db->prefix('imgset_tplset_link'), $imgset_id, $this->db->quoteString($tplset_name));
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * Load {@link XoopsImage ThemeSet}s into a Database
     *
     * @param  int    $imgset_id
     * @param  string $tplset_name
     * @return array
     */
    function unlinkThemeset($imgset_id, $tplset_name)
    {
        $imgset_id = intval($imgset_id);
        $tplset_name = trim($tplset_name);
        if ($imgset_id <= 0 || $tplset_name == '') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE imgset_id = %u AND tplset_name = %s", $this->db->prefix('imgset_tplset_link'), $imgset_id, $this->db->quoteString($tplset_name));
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * Get a list of XoopsImageSet
     *
     * @param  null  $refid
     * @param  null  $tplset
     * @internal param int $imgcat_id
     * @internal param bool $image_display
     * @return array Array of {@link XoopsImage} objects
     */
    function getList($refid = null, $tplset = null)
    {
        $criteria = new CriteriaCompo();
        if (isset($refid)) {
            $criteria->add(new Criteria('imgset_refid', intval($refid)));
        }
        if (isset($tplset)) {
            $criteria->add(new Criteria('tplset_name', $tplset));
        }
        $imgsets = $this->getObjects($criteria, true);
        $ret = array();
        foreach (array_keys($imgsets) as $i) {
            $ret[$i] = $imgsets[$i]->getVar('imgset_name');
        }

        return $ret;
    }
}
