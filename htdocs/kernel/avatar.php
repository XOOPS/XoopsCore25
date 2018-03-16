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
 * A Avatar
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 * @package             kernel
 */
class XoopsAvatar extends XoopsObject
{
    public $_userCount;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('avatar_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('avatar_file', XOBJ_DTYPE_OTHER, null, false, 30);
        $this->initVar('avatar_name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('avatar_mimetype', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('avatar_created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('avatar_display', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('avatar_weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('avatar_type', XOBJ_DTYPE_OTHER, 0, false);
    }

    /**
     * Returns Class Base Variable avatar_id
     * @param string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('avatar_id', $format);
    }

    /**
     * Returns Class Base Variable avatar_id
     * @param string $format
     * @return mixed
     */
    public function avatar_id($format = '')
    {
        return $this->getVar('avatar_id', $format);
    }

    /**
     * Returns Class Base Variable avatar_file
     * @param string $format
     * @return mixed
     */
    public function avatar_file($format = '')
    {
        return $this->getVar('avatar_file', $format);
    }

    /**
     * Returns Class Base Variable avatar_name
     * @param string $format
     * @return mixed
     */
    public function avatar_name($format = '')
    {
        return $this->getVar('avatar_name', $format);
    }

    /**
     * Returns Class Base Variable avatar_mimetype
     * @param string $format
     * @return mixed
     */
    public function avatar_mimetype($format = '')
    {
        return $this->getVar('avatar_mimetype', $format);
    }

    /**
     * Returns Class Base Variable avatar_created
     * @param string $format
     * @return mixed
     */
    public function avatar_created($format = '')
    {
        return $this->getVar('avatar_created', $format);
    }

    /**
     * Returns Class Base Variable avatar_display
     * @param string $format
     * @return mixed
     */
    public function avatar_display($format = '')
    {
        return $this->getVar('avatar_display', $format);
    }

    /**
     * Returns Class Base Variable avatar_weight
     * @param string $format
     * @return mixed
     */
    public function avatar_weight($format = '')
    {
        return $this->getVar('avatar_weight', $format);
    }

    /**
     * Returns Class Base Variable avatar_type
     * @param string $format
     * @return mixed
     */
    public function avatar_type($format = '')
    {
        return $this->getVar('avatar_type', $format);
    }

    /**
     * Set User Count
     *
     * @param int $value
     */
    public function setUserCount($value)
    {
        $this->_userCount = (int)$value;
    }

    /**
     * Get User Count
     *
     * @return int
     */
    public function getUserCount()
    {
        return $this->_userCount;
    }
}

/**
 * XOOPS avatar handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             kernel
 * @subpackage          block
 */
class XoopsAvatarHandler extends XoopsObjectHandler
{
    /**
     * Create new Object
     *
     * @param  bool $isNew
     * @return XoopsAvatar
     */
    public function create($isNew = true)
    {
        $avatar = new XoopsAvatar();
        if ($isNew) {
            $avatar->setNew();
        }

        return $avatar;
    }

    /**
     * Egt Object
     *
     * @param  int $id
     * @return XoopsAvatar
     */
    public function get($id)
    {
        $avatar = false;
        $id     = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('avatar') . ' WHERE avatar_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $avatar = new XoopsAvatar();
                $avatar->assignVars($this->db->fetchArray($result));

                return $avatar;
            }
        }

        return $avatar;
    }

    /**
     * Insert and Object into the database
     *
     * @param  XoopsObject|XoopsAvatar $avatar a XoopsAvatar object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $avatar)
    {
        $className = 'XoopsAvatar';
        if (!($avatar instanceof $className)) {
            return false;
        }
        if (!$avatar->isDirty()) {
            return true;
        }
        if (!$avatar->cleanVars()) {
            return false;
        }
        foreach ($avatar->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($avatar->isNew()) {
            $avatar_id = $this->db->genId('avatar_avatar_id_seq');
            $sql       = sprintf('INSERT INTO %s (avatar_id, avatar_file, avatar_name, avatar_created, avatar_mimetype, avatar_display, avatar_weight, avatar_type) VALUES (%u, %s, %s, %u, %s, %u, %u, %s)', $this->db->prefix('avatar'), $avatar_id, $this->db->quoteString($avatar_file), $this->db->quoteString($avatar_name), time(), $this->db->quoteString($avatar_mimetype), $avatar_display, $avatar_weight, $this->db->quoteString($avatar_type));
        } else {
            $sql = sprintf('UPDATE %s SET avatar_file = %s, avatar_name = %s, avatar_created = %u, avatar_mimetype= %s, avatar_display = %u, avatar_weight = %u, avatar_type = %s WHERE avatar_id = %u', $this->db->prefix('avatar'), $this->db->quoteString($avatar_file), $this->db->quoteString($avatar_name), $avatar_created, $this->db->quoteString($avatar_mimetype), $avatar_display, $avatar_weight, $this->db->quoteString($avatar_type), $avatar_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($avatar_id)) {
            $avatar_id = $this->db->getInsertId();
        }
        $avatar->assignVar('avatar_id', $avatar_id);

        return true;
    }

    /**
     * Delete an object from the database
     *
     * @param  XoopsObject|XoopsAvatar $avatar a XoopsAvatar object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $avatar)
    {
        $className = 'XoopsAvatar';
        if (!($avatar instanceof $className)) {
            return false;
        }

        $id  = $avatar->getVar('avatar_id');
        $sql = sprintf('DELETE FROM %s WHERE avatar_id = %u', $this->db->prefix('avatar'), $id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql    = sprintf('DELETE FROM %s WHERE avatar_id = %u', $this->db->prefix('avatar_user_link'), $id);
        $result = $this->db->query($sql);

        return true;
    }

    /**
     * Fetch a row of objects from the database
     *
     * @param  CriteriaElement|CriteriaCompo $criteria
     * @param  bool  $id_as_key
     * @return object
     */
    public function &getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT a.*, COUNT(u.user_id) AS count FROM ' . $this->db->prefix('avatar') . ' a LEFT JOIN ' . $this->db->prefix('avatar_user_link') . ' u ON u.avatar_id=a.avatar_id';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' GROUP BY a.avatar_id ORDER BY avatar_weight, avatar_id';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $avatar = new XoopsAvatar();
            $avatar->assignVars($myrow);
            $avatar->setUserCount($myrow['count']);
            if (!$id_as_key) {
                $ret[] = &$avatar;
            } else {
                $ret[$myrow['avatar_id']] = &$avatar;
            }
            unset($avatar);
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


        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('avatar');
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
     * Add user
     *
     * @param  int $avatar_id
     * @param  int $user_id
     * @return bool
     */
    public function addUser($avatar_id, $user_id)
    {
        $avatar_id = (int)$avatar_id;
        $user_id   = (int)$user_id;
        if ($avatar_id < 1 || $user_id < 1) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE user_id = %u', $this->db->prefix('avatar_user_link'), $user_id);
        $this->db->query($sql);
        $sql = sprintf('INSERT INTO %s (avatar_id, user_id) VALUES (%u, %u)', $this->db->prefix('avatar_user_link'), $avatar_id, $user_id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get User
     *
     * @param  XoopsAvatar $avatar
     * @return array
     */
    public function getUser(XoopsAvatar $avatar)
    {
        $ret = array();
        /**
         * @TODO: Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
         */
        if (!is_a($avatar, 'xoopsavatar')) {
            return false;
        }
        $sql = 'SELECT user_id FROM ' . $this->db->prefix('avatar_user_link') . ' WHERE avatar_id=' . $avatar->getVar('avatar_id');
        if (!$result = $this->db->query($sql)) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = &$myrow['user_id'];
        }

        return $ret;
    }

    /**
     * Get a list of Avatars
     *
     * @param  string $avatar_type
     * @param  string $avatar_display
     * @return array
     */
    public function getList($avatar_type = null, $avatar_display = null)
    {
        $criteria = new CriteriaCompo();
        if (isset($avatar_type)) {
            $avatar_type = ($avatar_type === 'C') ? 'C' : 'S';
            $criteria->add(new Criteria('avatar_type', $avatar_type));
        }
        if (isset($avatar_display)) {
            $criteria->add(new Criteria('avatar_display', (int)$avatar_display));
        }
        $avatars = &$this->getObjects($criteria, true);
        $ret     = array(
            'blank.gif' => _NONE);
        foreach (array_keys($avatars) as $i) {
            $ret[$avatars[$i]->getVar('avatar_file')] = $avatars[$i]->getVar('avatar_name');
        }

        return $ret;
    }
}
