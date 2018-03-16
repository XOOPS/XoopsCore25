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
 * Private Messages
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 * @package             kernel
 **/
class XoopsPrivmessage extends XoopsObject
{
    /**
     * constructor
     **/
    public function __construct()
    {
        parent::__construct();
        $this->initVar('msg_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('msg_image', XOBJ_DTYPE_OTHER, null, false, 100);
        $this->initVar('subject', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('from_userid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('to_userid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('msg_time', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('msg_text', XOBJ_DTYPE_TXTAREA, null, true);
        $this->initVar('read_msg', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Returns Class Base Variable msg_id
     * @param string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('msg_id', $format);
    }

    /**
     * Returns Class Base Variable msg_id
     * @param string $format
     * @return mixed
     */
    public function msg_id($format = '')
    {
        return $this->getVar('msg_id', $format);
    }

    /**
     * Returns Class Base Variable msg_image
     * @param string $format
     * @return mixed
     */
    public function msg_image($format = '')
    {
        return $this->getVar('msg_image', $format);
    }

    /**
     * Returns Class Base Variable subject
     * @param string $format
     * @return mixed
     */
    public function subject($format = '')
    {
        return $this->getVar('subject', $format);
    }

    /**
     * Returns Class Base Variable not_id
     * @param string $format
     * @return mixed
     */
    public function from_userid($format = '')
    {
        return $this->getVar('from_userid', $format);
    }

    /**
     * Returns Class Base Variable to_userid
     * @param string $format
     * @return mixed
     */
    public function to_userid($format = '')
    {
        return $this->getVar('to_userid', $format);
    }

    /**
     * Returns Class Base Variable msg_time
     * @param string $format
     * @return mixed
     */
    public function msg_time($format = '')
    {
        return $this->getVar('msg_time', $format);
    }

    /**
     * Returns Class Base Variable msg_text
     * @param string $format
     * @return mixed
     */
    public function msg_text($format = '')
    {
        return $this->getVar('msg_text', $format);
    }

    /**
     * Returns Class Base Variable read_msg
     * @param string $format
     * @return mixed
     */
    public function read_msg($format = '')
    {
        return $this->getVar('read_msg', $format);
    }
}

/**
 * XOOPS private message handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS private message class objects.
 *
 * @package             kernel
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 *
 * @todo Why is this not a XoopsPersistableObjectHandler?
 */
class XoopsPrivmessageHandler extends XoopsObjectHandler
{
    /**
     * Create a new {@link XoopsPrivmessage} object
     * @param  bool $isNew Flag as "new"?
     * @return XoopsPrivmessage
     **/
    public function create($isNew = true)
    {
        $pm = new XoopsPrivmessage();
        if ($isNew) {
            $pm->setNew();
        }

        return $pm;
    }

    /**
     * Load a {@link XoopsPrivmessage} object
     * @param  int $id ID of the message
     * @return XoopsPrivmessage
     **/
    public function get($id)
    {
        $pm = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('priv_msgs') . ' WHERE msg_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $pm;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $pm = new XoopsPrivmessage();
                $pm->assignVars($this->db->fetchArray($result));
            }
        }

        return $pm;
    }

    /**
     * Insert a message in the database
     *
     * @param  XoopsPrivmessage $pm    {@link XoopsPrivmessage} object
     * @param  bool   $force flag to force the query execution skip request method check, which might be required in some situations
     * @param  XoopsObject|XoopsPrivmessage $pm a XoopsMembership object
     *
     * @return bool true on success, otherwise false
     **/
    public function insert(XoopsObject $pm, $force = false)
    {
        $className = 'XoopsPrivmessage';
        if (!($pm instanceof $className)) {
            return false;
        }

        if (!$pm->isDirty()) {
            return true;
        }
        if (!$pm->cleanVars()) {
            return false;
        }
        foreach ($pm->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($pm->isNew()) {
            $msg_id = $this->db->genId('priv_msgs_msg_id_seq');
            $sql    = sprintf('INSERT INTO %s (msg_id, msg_image, subject, from_userid, to_userid, msg_time, msg_text, read_msg) VALUES (%u, %s, %s, %u, %u, %u, %s, %u)', $this->db->prefix('priv_msgs'), $msg_id, $this->db->quoteString($msg_image), $this->db->quoteString($subject), $from_userid, $to_userid, time(), $this->db->quoteString($msg_text), 0);
        } else {
            $sql = sprintf('UPDATE %s SET msg_image = %s, subject = %s, from_userid = %u, to_userid = %u, msg_text = %s, read_msg = %u WHERE msg_id = %u', $this->db->prefix('priv_msgs'), $this->db->quoteString($msg_image), $this->db->quoteString($subject), $from_userid, $to_userid, $this->db->quoteString($msg_text), $read_msg, $msg_id);
        }
        $queryFunc = empty($force) ? 'query' : 'queryF';
        if (!$result = $this->db->{$queryFunc}($sql)) {
            return false;
        }
        if (empty($msg_id)) {
            $msg_id = $this->db->getInsertId();
        }
        $pm->assignVar('msg_id', $msg_id);

        return true;
    }

    /**
     * Delete from the database
     * @param  XoopsPrivmessage $pm {@link XoopsPrivmessage} object
     * @return bool
     **/
    public function delete(XoopsObject $pm)
    {
        $className = 'XoopsPrivmessage';
        if (!($pm instanceof $className)) {
            return false;
        }

        if (!$result = $this->db->query(sprintf('DELETE FROM %s WHERE msg_id = %u', $this->db->prefix('priv_msgs'), $pm->getVar('msg_id')))) {
            return false;
        }

        return true;
    }

    /**
     * Load messages from the database
     * @param  CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement} object
     * @param  bool   $id_as_key use ID as key into the array?
     * @return array  Array of {@link XoopsPrivmessage} objects
     **/
    public function getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('priv_msgs');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sort = !in_array($criteria->getSort(), array(
                'msg_id',
                'msg_time',
                'from_userid')) ? 'msg_id' : $criteria->getSort();
            $sql .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $pm = new XoopsPrivmessage();
            $pm->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $pm;
            } else {
                $ret[$myrow['msg_id']] =& $pm;
            }
            unset($pm);
        }

        return $ret;
    }

    /**
     * Count message
     * @param  CriteriaElement|CriteriaCompo $criteria = null     {@link CriteriaElement} object
     * @return int
     **/
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('priv_msgs');
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
     * Mark a message as read
     * @param  XoopsPrivmessage $pm {@link XoopsPrivmessage} object
     * @return bool
     **/
    public function setRead(XoopsPrivmessage $pm)
    {
        /**
         * @TODO: Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
         */
        if (!is_a($pm, 'xoopsprivmessage')) {
            return false;
        }

        $sql = sprintf('UPDATE %s SET read_msg = 1 WHERE msg_id = %u', $this->db->prefix('priv_msgs'), $pm->getVar('msg_id'));
        if (!$this->db->queryF($sql)) {
            return false;
        }

        return true;
    }
}
