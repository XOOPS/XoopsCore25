<?php
/**
 * Object render handler class.
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
 * @subpackage          model
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Object render handler class.
 *
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 *
 * {@link XoopsModelAbstract}
 */
class XoopsModelRead extends XoopsModelAbstract
{
    /**
     * get all objects matching a condition
     *
     * @param  CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement} to match
     * @param  array           $fields    variables to fetch
     * @param  bool            $asObject  flag indicating as object, otherwise as array
     * @param  bool            $id_as_key use the ID as key for the array
     * @return array           of objects/array {@link XoopsObject}
     */
    public function &getAll(CriteriaElement $criteria = null, $fields = null, $asObject = true, $id_as_key = true)
    {
        if (is_array($fields) && count($fields) > 0) {
            if (!in_array($this->handler->keyName, $fields)) {
                $fields[] = $this->handler->keyName;
            }
            $select = '`' . implode('`, `', $fields) . '`';
        } else {
            $select = '*';
        }
        $limit = null;
        $start = null;
        $sql   = "SELECT {$select} FROM `{$this->handler->table}`";
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($groupby = $criteria->getGroupby()) {
                $sql .= $groupby;
            }
            if ($sort = $criteria->getSort()) {
                $sql .= " ORDER BY {$sort} " . $criteria->getOrder();
                $orderSet = true;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (empty($orderSet)) {
            //$sql .= " ORDER BY `{$this->handler->keyName}` DESC";
        }
        $result = $this->handler->db->query($sql, $limit, $start);
        $ret    = array();
        if ($asObject) {
            while (false !== ($myrow = $this->handler->db->fetchArray($result))) {
                $object = $this->handler->create(false);
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow[$this->handler->keyName]] = $object;
                } else {
                    $ret[] = $object;
                }
                unset($object);
            }
        } else {
            $object = $this->handler->create(false);
            while (false !== ($myrow = $this->handler->db->fetchArray($result))) {
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow[$this->handler->keyName]] = $object->getValues(array_keys($myrow));
                } else {
                    $ret[] = $object->getValues(array_keys($myrow));
                }
            }
            unset($object);
        }

        return $ret;
    }

    /**
     * retrieve objects from the database
     *
     * For performance consideration, getAll() is recommended
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement} conditions to be met
     * @param  bool            $id_as_key use the ID as key for the array
     * @param  bool            $as_object return an array of objects?
     * @return array
     */
    public function &getObjects(CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $objects =& $this->getAll($criteria, null, $as_object, $id_as_key);

        return $objects;
    }

    /**
     * Retrieve a list of objects data
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} conditions to be met
     * @param  int             $limit    Max number of objects to fetch
     * @param  int             $start    Which record to start at
     * @return array
     */
    public function getList(CriteriaElement $criteria = null, $limit = 0, $start = 0)
    {
        $ret = array();
        if ($criteria == null) {
            $criteria = new CriteriaCompo();
        }

        $sql = "SELECT `{$this->handler->keyName}`";
        if (!empty($this->handler->identifierName)) {
            $sql .= ", `{$this->handler->identifierName}`";
        }
        $sql .= " FROM `{$this->handler->table}`";
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($sort = $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->handler->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }

        $myts = MyTextSanitizer::getInstance();
        while (false !== ($myrow = $this->handler->db->fetchArray($result))) {
            // identifiers should be textboxes, so sanitize them like that
            $ret[$myrow[$this->handler->keyName]] = empty($this->handler->identifierName) ? 1 : $myts->htmlSpecialChars($myrow[$this->handler->identifierName]);
        }

        return $ret;
    }

    /**
     * get IDs of objects matching a condition
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement} to match
     * @return array           of object IDs
     */
    public function &getIds(CriteriaElement $criteria = null)
    {
        $ret   = array();
        $sql   = "SELECT `{$this->handler->keyName}` FROM `{$this->handler->table}`";
        $limit = $start = null;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (!$result = $this->handler->db->query($sql, $limit, $start)) {
            return $ret;
        }
        while (false !== ($myrow = $this->handler->db->fetchArray($result))) {
            $ret[] = $myrow[$this->handler->keyName];
        }

        return $ret;
    }

    /**
     * get a limited list of objects matching a condition
     *
     * {@link CriteriaCompo}
     *
     * @param  int             $limit    Max number of objects to fetch
     * @param  int             $start    Which record to start at
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @param  array           $fields   variables to fetch
     * @param  bool            $asObject flag indicating as object, otherwise as array
     * @return array           of objects    {@link XoopsObject}
     */
    public function &getByLimit($limit = 0, $start = 0, CriteriaElement $criteria = null, $fields = null, $asObject = true)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__CLASS__ . '::' . __FUNCTION__ . '() is deprecated, please use getAll instead.');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $criteria->setLimit($limit);
            $criteria->setStart($start);
        } elseif (!empty($limit)) {
            $criteria = new CriteriaCompo();
            $criteria->setLimit($limit);
            $criteria->setStart($start);
        }
        $ret = $this->handler->getAll($criteria, $fields, $asObject);

        return $ret;
    }

    /**
     * Convert a database resultset to a returnable array
     *
     * @param  object $result    database resultset
     * @param  bool   $id_as_key - should NOT be used with joint keys
     * @param  bool   $as_object
     * @return array
     */
    public function convertResultSet($result, $id_as_key = false, $as_object = true)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__CLASS__ . '::' . __FUNCTION__ . '() is deprecated.');
        $ret = array();
        while (false !== ($myrow = $this->handler->db->fetchArray($result))) {
            $obj = $this->handler->create(false);
            $obj->assignVars($myrow);
            if (!$id_as_key) {
                if ($as_object) {
                    $ret[] = $obj;
                } else {
                    $row  = array();
                    $vars = $obj->getVars();
                    foreach (array_keys($vars) as $i) {
                        $row[$i] = $obj->getVar($i);
                    }
                    $ret[] = $row;
                }
            } else {
                if ($as_object) {
                    $ret[$myrow[$this->handler->keyName]] =& $obj;
                } else {
                    $row  = array();
                    $vars = $obj->getVars();
                    foreach (array_keys($vars) as $i) {
                        $row[$i] = $obj->getVar($i);
                    }
                    $ret[$myrow[$this->handler->keyName]] = $row;
                }
            }
            unset($obj);
        }

        return $ret;
    }
}
