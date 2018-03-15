<?php
/**
 * Object write handler class.
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
 * Object write handler class.
 *
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 * @author Simon Roberts <simon@xoops.org>
 *
 * {@link XoopsModelAbstract}
 */
class XoopsModelWrite extends XoopsModelAbstract
{
    /**
     * Clean values of all variables of the object for storage.
     * also add slashes and quote string wherever needed
     *
     * CleanVars only contains changed and cleaned variables
     * Reference is used for PHP4 compliance
     *
     * @param $object
     *
     * @return bool true if successful
     * @access public
     */
    public function cleanVars(&$object)
    {
        $ts     = MyTextSanitizer::getInstance();
        $errors = array();

        $vars              = $object->getVars();
        $object->cleanVars = array();
        foreach ($vars as $k => $v) {
            if (!$v['changed']) {
                continue;
            }
            $cleanv = $v['value'];
            switch ($v['data_type']) {
                case XOBJ_DTYPE_TIMESTAMP:
                    $cleanv = !is_string($cleanv) && is_numeric($cleanv) ? date(_DBTIMESTAMPSTRING, $cleanv) : date(_DBTIMESTAMPSTRING, strtotime($cleanv));
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;
                case XOBJ_DTYPE_TIME:
                    $cleanv = !is_string($cleanv) && is_numeric($cleanv) ? date(_DBTIMESTRING, $cleanv) : date(_DBTIMESTRING, strtotime($cleanv));
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;
                case XOBJ_DTYPE_DATE:
                    $cleanv = !is_string($cleanv) && is_numeric($cleanv) ? date(_DBDATESTRING, $cleanv) : date(_DBDATESTRING, strtotime($cleanv));
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;
                case XOBJ_DTYPE_UNICODE_TXTBOX:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    $cleanv = xoops_convert_encode($cleanv);
                    if (isset($v['maxlength']) && strlen($cleanv) > (int)$v['maxlength']) {
                        $errors[] = sprintf(_XOBJ_ERR_SHORTERTHAN, $k, (int)$v['maxlength']);
                        continue 2;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;

                case XOBJ_DTYPE_UNICODE_TXTAREA:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    $cleanv = xoops_convert_encode($cleanv);
                    if (!$v['not_gpc']) {
                        if (!empty($vars['dohtml']['value'])) {
                            $cleanv = $ts->textFilter($cleanv);
                        }
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;

                case XOBJ_DTYPE_TXTBOX:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    if (isset($v['maxlength']) && strlen($cleanv) > (int)$v['maxlength']) {
                        $errors[] = sprintf(_XOBJ_ERR_SHORTERTHAN, $k, (int)$v['maxlength']);
                        continue 2;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;

                case XOBJ_DTYPE_TXTAREA:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    if (!$v['not_gpc']) {
                        if (!empty($vars['dohtml']['value'])) {
                            $cleanv = $ts->textFilter($cleanv);
                        }
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;

                case XOBJ_DTYPE_SOURCE:
                    $cleanv = trim($cleanv);
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    } else {
                        $cleanv = $cleanv;
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;
                // Should not be used!
                case XOBJ_DTYPE_UNICODE_EMAIL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote(xoops_convert_encode($cleanv)));
                    break;

                case XOBJ_DTYPE_EMAIL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    if ($cleanv != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $cleanv)) {
                        $errors[] = 'Invalid Email';
                        continue 2;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;

                // Should not be used!
                case XOBJ_DTYPE_UNICODE_URL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                        $cleanv = XOOPS_PROT . $cleanv;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote(xoops_convert_encode($cleanv)));
                    break;
                case XOBJ_DTYPE_URL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue 2;
                    }
                    if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                        $cleanv = XOOPS_PROT . $cleanv;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;

                // Should not be used!
                case XOBJ_DTYPE_UNICODE_OTHER:
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote(xoops_convert_encode($cleanv)));
                    break;

                case XOBJ_DTYPE_OTHER:
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;

                case XOBJ_DTYPE_INT:
                    $cleanv = (int)$cleanv;
                    break;

                case XOBJ_DTYPE_FLOAT:
                    $cleanv = (float)$cleanv;
                    break;

                case XOBJ_DTYPE_DECIMAL:
                    $cleanv = (float)$cleanv;
                    break;

                // Should not be used!
                case XOBJ_DTYPE_UNICODE_ARRAY:
                    if (!$v['not_gpc']) {
                        $cleanv = array_map(array(&$ts, 'stripSlashesGPC'), $cleanv);
                    }
                    foreach (array_keys($cleanv) as $key) {
                        $cleanv[$key] = str_replace('\\"', '"', addslashes($cleanv[$key]));
                    }
                    // TODO: Not encoding safe, should try base64_encode -- phppp
                    $cleanv = "'" . serialize(array_walk($cleanv, 'xoops_aw_encode')) . "'";
                    break;

                case XOBJ_DTYPE_ARRAY:
                    $cleanv = (array)$cleanv;
                    if (!$v['not_gpc']) {
                        $cleanv = array_map(array(&$ts, 'stripSlashesGPC'), $cleanv);
                    }
                    // TODO: Not encoding safe, should try base64_encode -- phppp
                    $cleanv = $this->handler->db->quote(serialize($cleanv));
                    break;

                case XOBJ_DTYPE_STIME:
                case XOBJ_DTYPE_MTIME:
                case XOBJ_DTYPE_LTIME:
                    $cleanv = !is_string($cleanv) ? (int)$cleanv : strtotime($cleanv);
                    break;

                default:
                    $cleanv = str_replace('\\"', '"', $this->handler->db->quote($cleanv));
                    break;
            }
            $object->cleanVars[$k] = $cleanv;
        }
        if (!empty($errors)) {
            $object->setErrors($errors);
        }
        $object->unsetDirty();

        return empty($errors) ? true : false;
    }

    /**
     * insert an object into the database
     *
     * @param  object $object {@link XoopsObject} reference to object
     * @param  bool   $force  flag to force the query execution despite security settings
     * @return mixed  object ID
     */
    public function insert(&$object, $force = true)
    {
        if (!$object->isDirty()) {
            trigger_error("Data entry is not inserted - the object '" . get_class($object) . "' is not dirty", E_USER_NOTICE);

            return $object->getVar($this->handler->keyName);
        }
        if (!$this->cleanVars($object)) {
            trigger_error("Insert failed in method 'cleanVars' of object '" . get_class($object) . "'", E_USER_WARNING);

            return $object->getVar($this->handler->keyName);
        }
        $queryFunc = empty($force) ? 'query' : 'queryF';

        if ($object->isNew()) {
            $sql = 'INSERT INTO `' . $this->handler->table . '`';
            if (!empty($object->cleanVars)) {
                $keys = array_keys($object->cleanVars);
                $vals = array_values($object->cleanVars);
                $sql .= ' (`' . implode('`, `', $keys) . '`) VALUES (' . implode(',', $vals) . ')';
            } else {
                trigger_error("Data entry is not inserted - no variable is changed in object of '" . get_class($object) . "'", E_USER_NOTICE);

                return $object->getVar($this->handler->keyName);
            }
            if (!$result = $this->handler->db->{$queryFunc}($sql)) {
                return false;
            }
            if (!$object->getVar($this->handler->keyName) && $object_id = $this->handler->db->getInsertId()) {
                $object->assignVar($this->handler->keyName, $object_id);
            }
        } elseif (!empty($object->cleanVars)) {
            $keys = array();
            foreach ($object->cleanVars as $k => $v) {
                $keys[] = " `{$k}` = {$v}";
            }
            $sql = 'UPDATE `' . $this->handler->table . '` SET ' . implode(',', $keys) . ' WHERE `' . $this->handler->keyName . '` = ' . $this->handler->db->quote($object->getVar($this->handler->keyName));
            if (!$result = $this->handler->db->{$queryFunc}($sql)) {
                return false;
            }
        }

        return $object->getVar($this->handler->keyName);
    }

    /**
     * delete an object from the database
     *
     * @param  object $object {@link XoopsObject} reference to the object to delete
     * @param  bool   $force
     * @return bool   FALSE if failed.
     */
    public function delete(&$object, $force = false)
    {
        if (is_array($this->handler->keyName)) {
            $clause = array();
            $thishandlerkeyNameCount = count($this->handler->keyName);
            for ($i = 0; $i < $thishandlerkeyNameCount; ++$i) {
                $clause[] = '`' . $this->handler->keyName[$i] . '` = ' . $this->handler->db->quote($object->getVar($this->handler->keyName[$i]));
            }
            $whereclause = implode(' AND ', $clause);
        } else {
            $whereclause = '`' . $this->handler->keyName . '` = ' . $this->handler->db->quote($object->getVar($this->handler->keyName));
        }
        $sql       = 'DELETE FROM `' . $this->handler->table . '` WHERE ' . $whereclause;
        $queryFunc = empty($force) ? 'query' : 'queryF';
        $result    = $this->handler->db->{$queryFunc}($sql);

        return empty($result) ? false : true;
    }

    /**
     * delete all objects matching the conditions
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement} with conditions to meet
     * @param  bool   $force    force to delete
     * @param  bool   $asObject delete in object way: instantiate all objects and delete one by one
     * @return bool
     */
    public function deleteAll(CriteriaElement $criteria = null, $force = true, $asObject = false)
    {
        if ($asObject) {
            $objects = $this->handler->getAll($criteria);
            $num     = 0;
            foreach (array_keys($objects) as $key) {
                $num += $this->delete($objects[$key], $force) ? 1 : 0;
            }
            unset($objects);

            return $num;
        }
        $queryFunc = empty($force) ? 'query' : 'queryF';
        $sql       = 'DELETE FROM ' . $this->handler->table;
        if (!empty($criteria)) {
            if (is_subclass_of($criteria, 'criteriaelement')) {
                $sql .= ' ' . $criteria->renderWhere();
            } else {
                return false;
            }
        }
        if (!$this->handler->db->{$queryFunc}($sql)) {
            return false;
        }

        return $this->handler->db->getAffectedRows();
    }

    /**
     * Change a field for objects with a certain criteria
     *
     * @param  string $fieldname  Name of the field
     * @param  mixed  $fieldvalue Value to write
     * @param  CriteriaElement|CriteriaCompo  $criteria   {@link CriteriaElement}
     * @param  bool   $force      force to query
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue, CriteriaElement $criteria = null, $force = false)
    {
        $set_clause = "`{$fieldname}` = ";
        if (is_numeric($fieldvalue)) {
            $set_clause .= $fieldvalue;
        } elseif (is_array($fieldvalue)) {
            $set_clause .= $this->handler->db->quote(implode(',', $fieldvalue));
        } else {
            $set_clause .= $this->handler->db->quote($fieldvalue);
        }
        $sql = 'UPDATE `' . $this->handler->table . '` SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $queryFunc = empty($force) ? 'query' : 'queryF';
        $result    = $this->handler->db->{$queryFunc}($sql);

        return empty($result) ? false : true;
    }
}
