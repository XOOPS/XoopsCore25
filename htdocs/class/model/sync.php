<?php
/**
 * Object synchronization handler class.
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
 * Object synchronization handler class.
 *
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 *
 * {@link XoopsModelAbstract}
 */

/**
 * Usage of methods provided by XoopsModelSync:
 *
 * Step #1: set linked table and adjoint fields through XoopsPersistableObjectHandler:
 *                  $handler->table_link = $handler->db->prefix("the_linked_table"); // full name of the linked table that is used for the query
 *                  $handler->field_link = "the_linked_field"; // name of field in linked table that will be used to link the linked table with current table
 *                  $handler->field_object = "the_object_field"; // name of field in current table that will be used to link the linked table with current table; linked field name will be used if the field name is not set
 * Step #2: perform query
 */
class XoopsModelSync extends XoopsModelAbstract
{
    /**
     * Clean orphan objects against linked objects
     *
     * @param  string $table_link   table of linked object for JOIN; deprecated, for backward compatibility
     * @param  string $field_link   field of linked object for JOIN; deprecated, for backward compatibility
     * @param  string $field_object field of current object for JOIN; deprecated, for backward compatibility
     * @return bool   true on success
     */
    public function cleanOrphan($table_link = '', $field_link = '', $field_object = '')
    {
        if (!empty($table_link)) {
            $this->handler->table_link = $table_link;
        }
        if (!empty($field_link)) {
            $this->handler->field_link = $field_link;
        }
        if (!empty($field_object)) {
            $this->handler->field_object = $field_object;
        }

        if (empty($this->handler->field_object) || empty($this->handler->table_link) || empty($this->handler->field_link)) {
            trigger_error("The link information is not set for '" . get_class($this->handler) . "' yet.", E_USER_WARNING);

            return null;
        }

        /**
         * for MySQL 4.1+
         */
        if (version_compare(mysqli_get_server_info($this->handler->db->conn), '4.1.0', 'ge')) {
            $sql = "DELETE FROM `{$this->handler->table}`" . " WHERE (`{$this->handler->field_object}` NOT IN ( SELECT DISTINCT `{$this->handler->field_link}` FROM `{$this->handler->table_link}`) )";
        } else {
            // for 4.0+
            $sql = "DELETE `{$this->handler->table}` FROM `{$this->handler->table}`" . " LEFT JOIN `{$this->handler->table_link}` AS aa ON `{$this->handler->table}`.`{$this->handler->field_object}` = aa.`{$this->handler->field_link}`" . " WHERE (aa.`{$this->handler->field_link}` IS NULL)";
        }
        if (!$result = $this->handler->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Synchronizing objects
     * @deprecated
     *
     * @return bool true on success
     */
    public function synchronization()
    {
        $retval = $this->cleanOrphan();

        return $retval;
    }
}
