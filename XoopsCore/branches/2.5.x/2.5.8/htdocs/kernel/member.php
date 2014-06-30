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
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @since           2.0.0
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once $GLOBALS['xoops']->path('kernel/user.php');
require_once $GLOBALS['xoops']->path('kernel/group.php');

/**
 * XOOPS member handler class.
 * This class provides simple interface (a facade class) for handling groups/users/
 * membership data.
 *
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @package kernel
 */

class XoopsMemberHandler
{

    /**#@+
     * holds reference to group handler(DAO) class
     * @access private
     */
    var $_gHandler;

    /**
     * holds reference to user handler(DAO) class
     */
    var $_uHandler;

    /**
     * holds reference to membership handler(DAO) class
     */
    var $_mHandler;

    /**
     * holds temporary user objects
     */
    var $_members = array();
    /**#@-*/

    /**
     * constructor
     *
     */
    function XoopsMemberHandler(&$db)
    {
        $this->_gHandler = new XoopsGroupHandler($db);
        $this->_uHandler = new XoopsUserHandler($db);
        $this->_mHandler = new XoopsMembershipHandler($db);
    }

    /**
     * create a new group
     *
     * @return object XoopsGroup reference to the new group
     */
    function &createGroup()
    {
        $inst =& $this->_gHandler->create();

        return $inst;
    }

    /**
     * create a new user
     *
     * @return object XoopsUser reference to the new user
     */
    function &createUser()
    {
        $inst =& $this->_uHandler->create();

        return $inst;
    }

    /**
     * retrieve a group
     *
     * @param  int    $id ID for the group
     * @return object XoopsGroup reference to the group
     */
    function getGroup($id)
    {
        return $this->_gHandler->get($id);
    }

    /**
     * retrieve a user
     *
     * @param  int    $id ID for the user
     * @return object XoopsUser reference to the user
     */
    function &getUser($id)
    {
        if (!isset($this->_members[$id])) {
            $this->_members[$id] = & $this->_uHandler->get($id);
        }

        return $this->_members[$id];
    }

    /**
     * delete a group
     *
     * @param  object $group reference to the group to delete
     * @return bool   FALSE if failed
     */
    function deleteGroup(&$group)
    {
        $s1 = $this->_mHandler->deleteAll(new Criteria('groupid', $group->getVar('groupid')));
        $s2 = $this->_gHandler->delete($group);

        return ($s1 && $s2) ? true : false;
    }

    /**
     * delete a user
     *
     * @param  object $user reference to the user to delete
     * @return bool   FALSE if failed
     */
    function deleteUser(&$user)
    {
        $s1 = $this->_mHandler->deleteAll(new Criteria('uid', $user->getVar('uid')));
        $s2 = $this->_uHandler->delete($user);

        return ($s1 && $s2) ? true : false;
    }

    /**
     * insert a group into the database
     *
     * @param  object $group reference to the group to insert
     * @return bool   TRUE if already in database and unchanged
     *                      FALSE on failure
     */
    function insertGroup(&$group)
    {
        return $this->_gHandler->insert($group);
    }

    /**
     * insert a user into the database
     *
     * @param object $user  reference to the user to insert
     * @param bool   $force
     *
     * @return bool TRUE if already in database and unchanged
     *              FALSE on failure
     */
    function insertUser(&$user, $force = false)
    {
        return $this->_uHandler->insert($user, $force);
    }

    /**
     * retrieve groups from the database
     *
     * @param  object $criteria  {@link CriteriaElement}
     * @param  bool   $id_as_key use the group's ID as key for the array?
     * @return array  array of {@link XoopsGroup} objects
     */
    function getGroups($criteria = null, $id_as_key = false)
    {
        return $this->_gHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * retrieve users from the database
     *
     * @param  object $criteria  {@link CriteriaElement}
     * @param  bool   $id_as_key use the group's ID as key for the array?
     * @return array  array of {@link XoopsUser} objects
     */
    function getUsers($criteria = null, $id_as_key = false)
    {
        return $this->_uHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * get a list of groupnames and their IDs
     *
     * @param  object $criteria {@link CriteriaElement} object
     * @return array  associative array of group-IDs and names
     */
    function getGroupList($criteria = null)
    {
        $groups = $this->_gHandler->getObjects($criteria, true);
        $ret = array();
        foreach (array_keys($groups) as $i) {
            $ret[$i] = $groups[$i]->getVar('name');
        }

        return $ret;
    }

    /**
     * get a list of usernames and their IDs
     *
     * @param  object $criteria {@link CriteriaElement} object
     * @return array  associative array of user-IDs and names
     */
    function getUserList($criteria = null)
    {
        $users = $this->_uHandler->getObjects($criteria, true);
        $ret = array();
        foreach (array_keys($users) as $i) {
            $ret[$i] = $users[$i]->getVar('uname');
        }

        return $ret;
    }

    /**
     * add a user to a group
     *
     * @param  int    $group_id ID of the group
     * @param  int    $user_id  ID of the user
     * @return object XoopsMembership
     */
    function addUserToGroup($group_id, $user_id)
    {
        $mship =& $this->_mHandler->create();
        $mship->setVar('groupid', $group_id);
        $mship->setVar('uid', $user_id);

        return $this->_mHandler->insert($mship);
    }

    /**
     * remove a list of users from a group
     *
     * @param  int   $group_id ID of the group
     * @param  array $user_ids array of user-IDs
     * @return bool  success?
     */
    function removeUsersFromGroup($group_id, $user_ids = array())
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('groupid', $group_id));
        $criteria2 = new CriteriaCompo();
        foreach ($user_ids as $uid) {
            $criteria2->add(new Criteria('uid', $uid), 'OR');
        }
        $criteria->add($criteria2);

        return $this->_mHandler->deleteAll($criteria);
    }

    /**
     * get a list of users belonging to a group
     *
     * @param  int   $group_id ID of the group
     * @param  bool  $asobject return the users as objects?
     * @param  int   $limit    number of users to return
     * @param  int   $start    index of the first user to return
     * @return array Array of {@link XoopsUser} objects (if $asobject is TRUE)
     *                        or of associative arrays matching the record structure in the database.
     */
    function getUsersByGroup($group_id, $asobject = false, $limit = 0, $start = 0)
    {
        $user_ids = $this->_mHandler->getUsersByGroup($group_id, $limit, $start);
        if (!$asobject) {
            return $user_ids;
        } else {
            $ret = array();
            foreach ($user_ids as $u_id) {
                $user =& $this->getUser($u_id);
                if (is_object($user)) {
                    $ret[] = & $user;
                }
                unset($user);
            }

            return $ret;
        }
    }

    /**
     * get a list of groups that a user is member of
     *
     * @param  int   $user_id  ID of the user
     * @param  bool  $asobject return groups as {@link XoopsGroup} objects or arrays?
     * @return array array of objects or arrays
     */
    function getGroupsByUser($user_id, $asobject = false)
    {
        $group_ids = $this->_mHandler->getGroupsByUser($user_id);
        if (!$asobject) {
            return $group_ids;
        } else {
            foreach ($group_ids as $g_id) {
                $ret[] = $this->getGroup($g_id);
            }

            return $ret;
        }
    }

    /**
     * log in a user
     *
     * @param  string $uname username as entered in the login form
     * @param  string $pwd   password entered in the login form
     * @return object XoopsUser reference to the logged in user. FALSE if failed to log in
     */
    function &loginUser($uname, $pwd)
    {
        $criteria = new CriteriaCompo(new Criteria('uname', $uname));
        $criteria->add(new Criteria('pass', md5($pwd)));
        $user = $this->_uHandler->getObjects($criteria, false);
        if (!$user || count($user) != 1) {
            $user = false;

            return $user;
        }

        return $user[0];
    }

    /**
     * logs in a user with an nd5 encrypted password
     *
     * @param  string $uname  username
     * @param  string $md5pwd password encrypted with md5
     * @return object XoopsUser reference to the logged in user. FALSE if failed to log in
     */
    function &loginUserMd5($uname, $md5pwd)
    {
        $criteria = new CriteriaCompo(new Criteria('uname', $uname));
        $criteria->add(new Criteria('pass', $md5pwd));
        $user = $this->_uHandler->getObjects($criteria, false);
        if (!$user || count($user) != 1) {
            $user = false;

            return $user;
        }

        return $user[0];
    }

    /**
     * count users matching certain conditions
     *
     * @param  object $criteria {@link CriteriaElement} object
     * @return int
     */
    function getUserCount($criteria = null)
    {
        return $this->_uHandler->getCount($criteria);
    }

    /**
     * count users belonging to a group
     *
     * @param  int $group_id ID of the group
     * @return int
     */
    function getUserCountByGroup($group_id)
    {
        return $this->_mHandler->getCount(new Criteria('groupid', $group_id));
    }

    /**
     * updates a single field in a users record
     *
     * @param  object $user       reference to the {@link XoopsUser} object
     * @param  string $fieldName  name of the field to update
     * @param  string $fieldValue updated value for the field
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    function updateUserByField(&$user, $fieldName, $fieldValue)
    {
        $user->setVar($fieldName, $fieldValue);

        return $this->insertUser($user);
    }

    /**
     * updates a single field in a users record
     *
     * @param  string $fieldName  name of the field to update
     * @param  string $fieldValue updated value for the field
     * @param  object $criteria   {@link CriteriaElement} object
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    function updateUsersByField($fieldName, $fieldValue, $criteria = null)
    {
        return $this->_uHandler->updateAll($fieldName, $fieldValue, $criteria);
    }

    /**
     * activate a user
     *
     * @param  object $user reference to the {@link XoopsUser} object
     * @return bool   successful?
     */
    function activateUser(&$user)
    {
        if ($user->getVar('level') != 0) {
            return true;
        }
        $user->setVar('level', 1);

        return $this->_uHandler->insert($user, true);
    }

    /**
     * Get a list of users belonging to certain groups and matching criteria
     * Temporary solution
     *
     * @param  array  $groups    IDs of groups
     * @param  object $criteria  {@link CriteriaElement} object
     * @param  bool   $asobject  return the users as objects?
     * @param  bool   $id_as_key use the UID as key for the array if $asobject is TRUE
     * @return array  Array of {@link XoopsUser} objects (if $asobject is TRUE)
     *                          or of associative arrays matching the record structure in the database.
     */
    function getUsersByGroupLink($groups, $criteria = null, $asobject = false, $id_as_key = false)
    {
        $ret = array();
        $criteriaCompo = new CriteriaCompo();
        $select = $asobject ? "u.*" : "u.uid";
        $sql = "SELECT DISTINCT {$select} " . " FROM " . $this->_uHandler->db->prefix("users")
             . " AS u" . " LEFT JOIN " . $this->_mHandler->db->prefix("groups_users_link")
             . " AS m ON m.uid = u.uid WHERE ";
        if (!empty($groups)) {
            $criteriaCompo->add(new Criteria('m.groupid', "(" . implode(", ", $groups) . ")", 'IN'));
        }

        $limit = $start = 0;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $criteriaCompo->add($criteria);
            $sql_criteria = $criteriaCompo->render();
            if ($criteria->getSort() != '') {
                $sql_criteria .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } else {
            $sql_criteria = $criteriaCompo->render();
        }

        if ($sql_criteria) {
            $sql .= $sql_criteria;
        } else {
            $sql .= "1 = 1";
        }

        if (!$result = $this->_uHandler->db->query($sql, $limit, $start)) {
            return $ret;
        }
        while ($myrow = $this->_uHandler->db->fetchArray($result)) {
            if ($asobject) {
                $user = new XoopsUser();
                $user->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $user;
                } else {
                    $ret[$myrow['uid']] =& $user;
                }
                unset($user);
            } else {
                $ret[] = $myrow['uid'];
            }
        }

        return $ret;
    }

    /**
     * Get count of users belonging to certain groups and matching criteria
     * Temporary solution
     *
     * @param  int  $groups   IDs of groups
     * @param  null $criteria
     * @return int  count of users
     */
    function getUserCountByGroupLink($groups, $criteria = null)
    {
        $ret = 0;
        $criteriaCompo = new CriteriaCompo();
        $sql = "SELECT DISTINCT COUNT(u.uid) "
             . " FROM " . $this->_uHandler->db->prefix("users") . " AS u"
             . " LEFT JOIN " . $this->_mHandler->db->prefix("groups_users_link")
             . " AS m ON m.uid = u.uid" . " WHERE ";
        if (!empty($groups)) {
            $criteriaCompo->add(new Criteria('m.groupid', "(" . implode(", ", $groups) . ")", 'IN'));
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $criteriaCompo->add($criteria);
        }
        $sql_criteria = $criteriaCompo->render();

        if ($sql_criteria) {
            $sql .= $sql_criteria;
        } else {
            $sql .= "1 = 1";
        }

        if (!$result = $this->_uHandler->db->query($sql)) {
            return $ret;
        }
        list ($ret) = $this->_uHandler->db->fetchRow($result);

        return $ret;
    }

}
