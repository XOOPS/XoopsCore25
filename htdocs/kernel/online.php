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
 * A handler for "Who is Online?" information
 *
 * @package             kernel
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsOnlineHandler
{
    /**
     * Database connection
     *
     * @var object
     * @access    private
     */
    public $db;

    /**
     * This should be here, since this really should be a XoopsPersistableObjectHandler
     * Here, we fake it for future compatibility
     *
     * @var string table name
     */
    public $table;

    /**
     * Constructor
     *
     * @param XoopsDatabase $db {@link XoopsHandlerFactory}
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->db = $db;
        $this->table = $this->db->prefix('online');
    }

    /**
     * Write online information to the database
     *
     * @param int    $uid    UID of the active user
     * @param string $uname  Username
     * @param int    $time   Timestamp
     * @param int    $module Current module id
     * @param string $ip     User's IP address
     *
     * @internal param string $timestamp
     * @return bool TRUE on success
     */
    public function write($uid, $uname, $time, $module, $ip)
    {
        $uid = (int) $uid;
        $uname = $this->db->quote($uname);
        $time = (int) $time;
        $module = (int) $module;
        $ip = $this->db->quote($ip);

        if ($uid > 0) {
            $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('online') . " WHERE online_uid={$uid}";
        } else {
            $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('online')
                   . " WHERE online_uid={$uid} AND online_ip={$ip}";
        }
        list($count) = $this->db->fetchRow($this->db->queryF($sql));
        if ($count > 0) {
            $sql = 'UPDATE ' . $this->db->prefix('online')
                   . " SET online_updated = {$time}, online_module = {$module} WHERE online_uid = {$uid}";
            if ($uid === 0) {
                $sql .= " AND online_ip={$ip}";
            }
        } else {
            $sql = sprintf(
                'INSERT INTO %s (online_uid, online_uname, online_updated, online_ip, online_module)'
                . ' VALUES (%u, %s, %u, %s, %u)',
                $this->db->prefix('online'),
                $uid,
                $uname,
                $time,
                $ip,
                $module
            );
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Delete online information for a user
     *
     * @param int $uid UID
     *
     * @return bool TRUE on success
     */
    public function destroy($uid)
    {
        $sql = sprintf('DELETE FROM %s WHERE online_uid = %u', $this->db->prefix('online'), $uid);
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Garbage Collection
     *
     * Delete all online information that has not been updated for a certain time
     *
     * @param int $expire Expiration time in seconds
     */
    public function gc($expire)
    {
        $sql = sprintf(
            'DELETE FROM %s WHERE online_updated < %u',
            $this->db->prefix('online'),
            time() - (int)$expire
        );
        $this->db->queryF($sql);
    }

    /**
     * Get an array of online information
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement}
     * @return array  Array of associative arrays of online information
     */
    public function getAll(CriteriaElement $criteria = null)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('online');
        if (is_object($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = $myrow;
            unset($myrow);
        }

        return $ret;
    }

    /**
     * Count the number of online users
     *
     * @param CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement}
     *
     * @return bool
     */
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('online');
        if (is_object($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        list($ret) = $this->db->fetchRow($result);

        return $ret;
    }
}
