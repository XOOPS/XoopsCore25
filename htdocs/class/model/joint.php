<?php
/**
 * Object joint handler class.
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          model
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Object joint handler class.
 *
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 *
 * {@link XoopsModelAbstract}
 */

/**
 * Usage of methods provided by XoopsModelJoint:
 *
 * Step #1: set linked table and adjoint fields through XoopsPersistableObjectHandler:
 *             $handler->table_link = $handler->db->prefix("the_linked_table"); // full name of the linked table that is used for the query
 *             $handler->field_link = "the_linked_field"; // name of field in linked table that will be used to link the linked table with current table
 *             $handler->field_object = "the_object_field"; // name of field in current table that will be used to link the linked table with current table; linked field name will be used if the field name is not set
 * Step #2: fetch data
 */
class XoopsModelJoint extends XoopsModelAbstract
{
    /**
     * Validate information for the link
     *
     * @access private
     */
    public function validateLinks()
    {
        if (empty($this->handler->table_link) || empty($this->handler->field_link)) {
            trigger_error('The linked table is not set yet.', E_USER_WARNING);

            return null;
        }
        if (empty($this->handler->field_object)) {
            $this->handler->field_object = $this->handler->field_link;
        }

        return true;
    }

    /**
     * get a list of objects matching a condition joint with another related object
     *
     * @param CriteriaElement|CriteriaCompo $criteria
     * @param  array          $fields       variables to fetch
     * @param  bool           $asObject     flag indicating as object, otherwise as array
     * @param  string         $field_link   field of linked object for JOIN; deprecated, for backward compatibility
     * @param  string         $field_object field of current object for JOIN; deprecated, for backward compatibility
     * @return array of objects <a href='psi_element://XoopsObject'>XoopsObject</a>
     * @internal param CriteriaElement $object <a href='psi_element://CriteriaElement'>CriteriaElement</a> to match to match
     */
    public function getByLink(?CriteriaElement $criteria = null, $fields = null, $asObject = true, $field_link = null, $field_object = null)
    {
        $ret = [];
        if (!empty($field_link)) {
            $this->handler->field_link = $field_link;
        }
        if (!empty($field_object)) {
            $this->handler->field_object = $field_object;
        }
        if (!$this->validateLinks()) {
            return $ret;
        }

        if (!empty($fields) && \is_array($fields)) {
            if (!in_array('o.' . $this->handler->keyName, $fields)) {
                $fields[] = 'o.' . $this->handler->keyName;
            }
            $select = implode(',', $fields);
        } else {
            $select = 'o.*, l.*';
        }
        $limit = null;
        $start = null;
        // $field_object = empty($field_object) ? $field_link : $field_object;
        $sql = " SELECT {$select}" . " FROM {$this->handler->table} AS o" . " LEFT JOIN {$this->handler->table_link} AS l ON o.{$this->handler->field_object} = l.{$this->handler->field_link}";
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($sort = $criteria->getSort()) {
                $sql .= " ORDER BY {$sort} " . $criteria->getOrder();
                $orderSet = true;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (empty($orderSet)) {
            $sql .= " ORDER BY o.{$this->handler->keyName} DESC";
        }
        $result = $this->handler->db->query($sql, $limit, $start);
        if (!$this->handler->db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $this->handler->db->error(),
                E_USER_ERROR,
            );
        }
        $ret    = [];
        if ($asObject) {
            while (false !== ($myrow = $this->handler->db->fetchArray($result))) {
                $object = $this->handler->create(false);
                $object->assignVars($myrow);
                $ret[$myrow[$this->handler->keyName]] = $object;
                unset($object);
            }
        } else {
            $object = $this->handler->create(false);
            while (false !== ($myrow = $this->handler->db->fetchArray($result))) {
                $object->assignVars($myrow);
                $ret[$myrow[$this->handler->keyName]] = $object->getValues(array_keys($myrow));
            }
            unset($object);
        }

        return $ret;
    }

    /**
     * Count of objects matching a condition
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement} to match
     * @return int|false    count of objects
     */
    public function getCountByLink(?CriteriaElement $criteria = null)
    {
        if (!$this->validateLinks()) {
            return null;
        }

        $sql = " SELECT COUNT(DISTINCT o.{$this->handler->keyName}) AS count" . " FROM {$this->handler->table} AS o" . " LEFT JOIN {$this->handler->table_link} AS l ON o.{$this->handler->field_object} = l.{$this->handler->field_link}";
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->handler->db->query($sql);
        if (!$this->handler->db->isResultSet($result)) {
            return false;
        }
        $myrow = $this->handler->db->fetchArray($result);

        return (int)$myrow['count'];
    }

    /**
     * array of count of objects matching a condition of, groupby linked object keyname
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement} to match
     * @return int|false|array|null    count of objects
     */
    public function getCountsByLink(?CriteriaElement $criteria = null)
    {
        if (!$this->validateLinks()) {
            return null;
        }
        $sql = " SELECT l.{$this->handler->field_link}, COUNT(*)" . " FROM {$this->handler->table} AS o" . " LEFT JOIN {$this->handler->table_link} AS l ON o.{$this->handler->field_object} = l.{$this->handler->field_link}";
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $sql .= " GROUP BY l.{$this->handler->field_link}";
        $result = $this->handler->db->query($sql);
        if (!$this->handler->db->isResultSet($result)) {
            return false;
        }
        $ret = [];
        while (false !== ([$id, $count] = $this->handler->db->fetchRow($result))) {
            $ret[$id] = $count;
        }

        return $ret;
    }

    /**
     * update objects matching a condition against linked objects
     *
     * @param  array  $data     array of key => value
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement} to match
     * @return int|null    count of objects
     */
    public function updateByLink($data, ?CriteriaElement $criteria = null)
    {
        if (!$this->validateLinks()) {
            return null;
        }
        $set = [];
        foreach ($data as $key => $val) {
            $set[] = "o.{$key}=" . $this->handler->db->quoteString($val);
        }
        $sql = " UPDATE {$this->handler->table} AS o" . ' SET ' . implode(', ', $set) . " LEFT JOIN {$this->handler->table_link} AS l ON o.{$this->handler->field_object} = l.{$this->handler->field_link}";
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        return $this->handler->db->query($sql);
    }

    /**
     * Delete objects matching a condition against linked objects
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement} to match
     * @return int|null    count of objects
     */
    public function deleteByLink(?CriteriaElement $criteria = null)
    {
        if (!$this->validateLinks()) {
            return null;
        }
        $sql = "DELETE FROM {$this->handler->table} AS o " . " LEFT JOIN {$this->handler->table_link} AS l ON o.{$this->handler->field_object} = l.{$this->handler->field_link}";
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        return $this->handler->db->query($sql);
    }
}
