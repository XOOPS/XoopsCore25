<?php
/**
 * XOOPS kernel class
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
 * A Template File
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 * @package             kernel
 **/
class XoopsTplfile extends XoopsObject
{
    /**
     * Constructor
     *
     * @return XoopsTplfile
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('tpl_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tpl_refid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tpl_tplset', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('tpl_file', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('tpl_desc', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('tpl_lastmodified', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tpl_lastimported', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tpl_module', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('tpl_type', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('tpl_source', XOBJ_DTYPE_SOURCE, null, false);
    }

    /**
     * Returns Class Base Variable tpl_id
     * @param  string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('tpl_id', $format);
    }

    /**
     * Returns Class Base Variable tpl_id
     * @param  string $format
     * @return mixed
     */
    public function tpl_id($format = '')
    {
        return $this->getVar('tpl_id', $format);
    }

    /**
     * Returns Class Base Variable tpl_refid
     * @param  string $format
     * @return mixed
     */
    public function tpl_refid($format = '')
    {
        return $this->getVar('tpl_refid', $format);
    }

    /**
     * Returns Class Base Variable tpl_tplset
     * @param  string $format
     * @return mixed
     */
    public function tpl_tplset($format = '')
    {
        return $this->getVar('tpl_tplset', $format);
    }

    /**
     * Returns Class Base Variable tpl_file
     * @param  string $format
     * @return mixed
     */
    public function tpl_file($format = '')
    {
        return $this->getVar('tpl_file', $format);
    }

    /**
     * Returns Class Base Variable tpl_desc
     * @param  string $format
     * @return mixed
     */
    public function tpl_desc($format = '')
    {
        return $this->getVar('tpl_desc', $format);
    }

    /**
     * Returns Class Base Variable tpl_lastmodified
     * @param  string $format
     * @return mixed
     */
    public function tpl_lastmodified($format = '')
    {
        return $this->getVar('tpl_lastmodified', $format);
    }

    /**
     * Returns Class Base Variable tpl_lastimported
     * @param  string $format
     * @return mixed
     */
    public function tpl_lastimported($format = '')
    {
        return $this->getVar('tpl_lastimported', $format);
    }

    /**
     * Returns Class Base Variable tpl_module
     * @param  string $format
     * @return mixed
     */
    public function tpl_module($format = '')
    {
        return $this->getVar('tpl_module', $format);
    }

    /**
     * Returns Class Base Variable tpl_type
     * @param  string $format
     * @return mixed
     */
    public function tpl_type($format = '')
    {
        return $this->getVar('tpl_type', $format);
    }

    /**
     * Returns Class Base Variable tpl_source
     * @param  string $format
     * @return mixed
     */
    public function tpl_source($format = '')
    {
        return $this->getVar('tpl_source', $format);
    }

    /**
     * getSource
     *
     * @return string
     */
    public function getSource()
    {
        return $this->getVar('tpl_source');
    }

    /**
     * getLastModified
     *
     * @return int unixtimestamp
     */
    public function getLastModified()
    {
        return $this->getVar('tpl_lastmodified');
    }
}

/**
 * XOOPS template file handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS template file class objects.
 *
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 *
 * @todo this is not a XoopsPersistableObjectHandler?
 */
class XoopsTplfileHandler extends XoopsObjectHandler
{
    /**
     * create a new Tplfile
     *
     * @see XoopsBlock
     * @param  bool $isNew is the new Tplfile new??
     * @return XoopsTplfile XoopsTplfile reference to the new Tplfile
     **/
    public function create($isNew = true)
    {
        $tplfile = new XoopsTplfile();
        if ($isNew) {
            $tplfile->setNew();
        }

        return $tplfile;
    }

    /**
     * retrieve a specific {@link XoopsTplfile}
     *
     * @see XoopsBlock
     *
     * @param int  $id tpl_id of the block to retrieve
     * @param bool $getsource
     *
     * @return object XoopsTplfile reference to the Tplfile
     */
    public function get($id, $getsource = false)
    {
        $tplfile = false;
        $id      = (int)$id;
        if ($id > 0) {
            if (!$getsource) {
                $sql = 'SELECT * FROM ' . $this->db->prefix('tplfile') . ' WHERE tpl_id=' . $id;
            } else {
                $sql = 'SELECT f.*, s.tpl_source FROM ' . $this->db->prefix('tplfile') . ' f LEFT JOIN ' . $this->db->prefix('tplsource') . ' s  ON s.tpl_id=f.tpl_id WHERE f.tpl_id=' . $id;
            }
            if (!$result = $this->db->query($sql)) {
                return $tplfile;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $tplfile = new XoopsTplfile();
                $tplfile->assignVars($this->db->fetchArray($result));
            }
        }

        return $tplfile;
    }

    /**
     * Load template source
     *
     * @param XoopsObject|XoopsTplfile $tplfile a XoopsTplfile object
     *
     * @return bool true on success, otherwise false
     */
    public function loadSource(XoopsObject $tplfile)
    {
        $className = 'XoopsTplfile';
        if (!($tplfile instanceof $className)) {
            return false;
        }

        if (!$tplfile->getVar('tpl_source')) {
            $sql = 'SELECT tpl_source FROM ' . $this->db->prefix('tplsource') . ' WHERE tpl_id=' . $tplfile->getVar('tpl_id');
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $myrow = $this->db->fetchArray($result);
            $tplfile->assignVar('tpl_source', $myrow['tpl_source']);
        }

        return true;
    }

    /**
     * write a new Tplfile into the database
     *
     * @param  XoopsObject|XoopsTplfile $tplfile a XoopsTplfile object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $tplfile)
    {
        $className = 'XoopsTplfile';
        if (!($tplfile instanceof $className)) {
            return false;
        }
        if (!$tplfile->isDirty()) {
            return true;
        }
        if (!$tplfile->cleanVars()) {
            return false;
        }
        foreach ($tplfile->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($tplfile->isNew()) {
            $tpl_id = $this->db->genId('tpltpl_file_id_seq');
            $sql    = sprintf('INSERT INTO %s (tpl_id, tpl_module, tpl_refid, tpl_tplset, tpl_file, tpl_desc, tpl_lastmodified, tpl_lastimported, tpl_type) VALUES (%u, %s, %u, %s, %s, %s, %u, %u, %s)', $this->db->prefix('tplfile'), $tpl_id, $this->db->quoteString($tpl_module), $tpl_refid, $this->db->quoteString($tpl_tplset), $this->db->quoteString($tpl_file), $this->db->quoteString($tpl_desc), $tpl_lastmodified, $tpl_lastimported, $this->db->quoteString($tpl_type));
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            if (empty($tpl_id)) {
                $tpl_id = $this->db->getInsertId();
            }
            if (isset($tpl_source) && $tpl_source != '') {
                $sql = sprintf('INSERT INTO %s (tpl_id, tpl_source) VALUES (%u, %s)', $this->db->prefix('tplsource'), $tpl_id, $this->db->quoteString($tpl_source));
                if (!$result = $this->db->query($sql)) {
                    $this->db->query(sprintf('DELETE FROM %s WHERE tpl_id = %u', $this->db->prefix('tplfile'), $tpl_id));

                    return false;
                }
            }
            $tplfile->assignVar('tpl_id', $tpl_id);
        } else {
            $sql = sprintf('UPDATE %s SET tpl_tplset = %s, tpl_file = %s, tpl_desc = %s, tpl_lastimported = %u, tpl_lastmodified = %u WHERE tpl_id = %u', $this->db->prefix('tplfile'), $this->db->quoteString($tpl_tplset), $this->db->quoteString($tpl_file), $this->db->quoteString($tpl_desc), $tpl_lastimported, $tpl_lastmodified, $tpl_id);
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            if (isset($tpl_source) && $tpl_source != '') {
                $sql = sprintf('UPDATE %s SET tpl_source = %s WHERE tpl_id = %u', $this->db->prefix('tplsource'), $this->db->quoteString($tpl_source), $tpl_id);
                if (!$result = $this->db->query($sql)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Update Tplfile, even if a safe transaction (i.e. http get)
     *
     * @param  XoopsTplfile $tplfile
     * @return bool true on success, otherwise false
     */
    public function forceUpdate(XoopsTplfile $tplfile)
    {
        $className = 'XoopsTplfile';
        if (!($tplfile instanceof $className)) {
            return false;
        }
        if (!$tplfile->isDirty()) {
            return true;
        }
        if (!$tplfile->cleanVars()) {
            return false;
        }
        foreach ($tplfile->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if (!$tplfile->isNew()) {
            $sql = sprintf('UPDATE %s SET tpl_tplset = %s, tpl_file = %s, tpl_desc = %s, tpl_lastimported = %u, tpl_lastmodified = %u WHERE tpl_id = %u', $this->db->prefix('tplfile'), $this->db->quoteString($tpl_tplset), $this->db->quoteString($tpl_file), $this->db->quoteString($tpl_desc), $tpl_lastimported, $tpl_lastmodified, $tpl_id);
            if (!$result = $this->db->queryF($sql)) {
                return false;
            }
            if (isset($tpl_source) && $tpl_source != '') {
                $sql = sprintf('UPDATE %s SET tpl_source = %s WHERE tpl_id = %u', $this->db->prefix('tplsource'), $this->db->quoteString($tpl_source), $tpl_id);
                if (!$result = $this->db->queryF($sql)) {
                    return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * delete a block from the database
     *
     * @param  XoopsObject|XoopsTplfile $tplfile a XoopsTplfile object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $tplfile)
    {
        $className = 'XoopsTplfile';
        if (!($tplfile instanceof $className)) {
            return false;
        }
        $id  = $tplfile->getVar('tpl_id');
        $sql = sprintf('DELETE FROM %s WHERE tpl_id = %u', $this->db->prefix('tplfile'), $id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE tpl_id = %u', $this->db->prefix('tplsource'), $id);
        $this->db->query($sql);

        return true;
    }

    /**
     * retrieve array of {@link XoopsBlock}s meeting certain conditions
     * @param  CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement} with conditions for the blocks
     * @param  bool            $getsource
     * @param  bool            $id_as_key should the blocks' bid be the key for the returned array?
     * @return array           {@link XoopsBlock}s matching the conditions
     */
    public function getObjects(CriteriaElement $criteria = null, $getsource = false, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        if ($getsource) {
            $sql = 'SELECT f.*, s.tpl_source FROM ' . $this->db->prefix('tplfile') . ' f LEFT JOIN ' . $this->db->prefix('tplsource') . ' s ON s.tpl_id=f.tpl_id';
        } else {
            $sql = 'SELECT * FROM ' . $this->db->prefix('tplfile');
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere() . ' ORDER BY tpl_refid';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $tplfile = new XoopsTplfile();
            $tplfile->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $tplfile;
            } else {
                $ret[$myrow['tpl_id']] =& $tplfile;
            }
            unset($tplfile);
        }

        return $ret;
    }

    /**
     * Get count
     *
     * @param  CriteriaElement|CriteriaCompo $criteria
     * @return int
     */
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('tplfile');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * getModuleTplCount
     *
     * @param  string $tplset
     * @return array
     */
    public function getModuleTplCount($tplset)
    {
        $ret    = array();
        $sql    = 'SELECT tpl_module, COUNT(tpl_id) AS count FROM ' . $this->db->prefix('tplfile') . " WHERE tpl_tplset='" . $tplset . "' GROUP BY tpl_module";
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if ($myrow['tpl_module'] != '') {
                $ret[$myrow['tpl_module']] = $myrow['count'];
            }
        }

        return $ret;
    }

    /**
     * Find Template File
     *
     * @param  string       $tplset
     * @param  string|array $type
     * @param  string       $refid
     * @param  string       $module
     * @param  string       $file
     * @param  bool|string  $getsource
     * @return object
     */
    public function find($tplset = null, $type = null, $refid = null, $module = null, $file = null, $getsource = false)
    {
        $criteria = new CriteriaCompo();
        if (isset($tplset)) {
            $criteria->add(new Criteria('tpl_tplset', $tplset));
        }
        if (isset($module)) {
            $criteria->add(new Criteria('tpl_module', $module));
        }
        if (isset($refid)) {
            $criteria->add(new Criteria('tpl_refid', $refid));
        }
        if (isset($file)) {
            $criteria->add(new Criteria('tpl_file', $file));
        }
        if (isset($type)) {
            if (is_array($type)) {
                $criteria2 = new CriteriaCompo();
                foreach ($type as $t) {
                    $criteria2->add(new Criteria('tpl_type', $t), 'OR');
                }
                $criteria->add($criteria2);
            } else {
                $criteria->add(new Criteria('tpl_type', $type));
            }
        }

        return $this->getObjects($criteria, $getsource, false);
    }

    /**
     * Template Exists
     *
     * @param  string $tplname
     * @param  string $tplset_name
     * @return bool true if template exists, otherwise false
     */
    public function templateExists($tplname, $tplset_name)
    {
        $criteria = new CriteriaCompo(new Criteria('tpl_file', trim($tplname)));
        $criteria->add(new Criteria('tpl_tplset', trim($tplset_name)));
        return $this->getCount($criteria) > 0;
    }
}
