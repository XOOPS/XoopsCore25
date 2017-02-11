<?php
/**
 * XOOPS user handler
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
 * Class for users
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             kernel
 */
class XoopsUser extends XoopsObject
{
    /**
     * Array of groups that user belongs to
     * @var array
     * @access private
     */
    public $_groups = array();
    /**
     * @var bool is the user admin?
     * @access private
     */
    public $_isAdmin;
    /**
     * @var string user's rank
     * @access private
     */
    public $_rank;
    /**
     * @var bool is the user online?
     * @access private
     */
    public $_isOnline;

    /**
     * constructor
     * @param array|null $id ID of the user to be loaded from the database.
     */
    public function __construct($id = null)
    {
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, true, 25);
        $this->initVar('email', XOBJ_DTYPE_TXTBOX, null, true, 60);
        $this->initVar('url', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('user_avatar', XOBJ_DTYPE_TXTBOX, null, false, 30);
        $this->initVar('user_regdate', XOBJ_DTYPE_INT, null, false);
        $this->initVar('user_icq', XOBJ_DTYPE_TXTBOX, null, false, 15);
        $this->initVar('user_from', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('user_sig', XOBJ_DTYPE_TXTAREA, null, false, null);
        $this->initVar('user_viewemail', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('actkey', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('user_aim', XOBJ_DTYPE_TXTBOX, null, false, 18);
        $this->initVar('user_yim', XOBJ_DTYPE_TXTBOX, null, false, 25);
        $this->initVar('user_msnm', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('pass', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('posts', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attachsig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('rank', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('level', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('theme', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('timezone_offset', XOBJ_DTYPE_OTHER, '0.0', false);
        $this->initVar('last_login', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('umode', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('uorder', XOBJ_DTYPE_INT, 1, false);
        // RMV-NOTIFY
        $this->initVar('notify_method', XOBJ_DTYPE_OTHER, 1, false);
        $this->initVar('notify_mode', XOBJ_DTYPE_OTHER, 0, false);
        $this->initVar('user_occ', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('bio', XOBJ_DTYPE_TXTAREA, null, false, null);
        $this->initVar('user_intrest', XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('user_mailok', XOBJ_DTYPE_INT, 1, false);
        // for backward compatibility
        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                /* @var $member_handler XoopsMemberHandler */
                $member_handler = xoops_getHandler('member');
                $user           = $member_handler->getUser($id);
                foreach ($user->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
            }
        }
    }

    /**
     * check if the user is a guest user
     *
     * @return bool returns false
     *
     */
    public function isGuest()
    {
        return false;
    }

    /**
     * Updated by Catzwolf 11 Jan 2004
     * find the username for a given ID
     *
     * @param  int $userid  ID of the user to find
     * @param  int $usereal switch for usename or realname
     * @return string name of the user. name for 'anonymous' if not found.
     */
    public static function getUnameFromId($userid, $usereal = 0)
    {
        $userid  = (int)$userid;
        $usereal = (int)$usereal;
        if ($userid > 0) {
            /* @var $member_handler XoopsMemberHandler */
            $member_handler = xoops_getHandler('member');
            $user           = $member_handler->getUser($userid);
            if (is_object($user)) {
                $ts = MyTextSanitizer::getInstance();
                if ($usereal) {
                    $name = $user->getVar('name');
                    if ($name != '') {
                        return $ts->htmlSpecialChars($name);
                    } else {
                        return $ts->htmlSpecialChars($user->getVar('uname'));
                    }
                } else {
                    return $ts->htmlSpecialChars($user->getVar('uname'));
                }
            }
        }

        return $GLOBALS['xoopsConfig']['anonymous'];
    }

    /**
     * increase the number of posts for the user
     *
     * @deprecated
     */
    public function incrementPost()
    {
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');

        return $member_handler->updateUserByField($this, 'posts', $this->getVar('posts') + 1);
    }

    /**
     * set the groups for the user
     *
     * @param array $groupsArr Array of groups that user belongs to
     */
    public function setGroups($groupsArr)
    {
        if (is_array($groupsArr)) {
            $this->_groups =& $groupsArr;
        }
    }

    /**
     * get the groups that the user belongs to
     *
     * @return array array of groups
     */
    public function &getGroups()
    {
        if (empty($this->_groups)) {
            /* @var $member_handler XoopsMemberHandler */
            $member_handler = xoops_getHandler('member');
            $this->_groups  = $member_handler->getGroupsByUser($this->getVar('uid'));
        }

        return $this->_groups;
    }

    /**
     * alias for {@link getGroups()}
     * @see getGroups()
     * @return array array of groups
     * @deprecated
     */
    public function &groups()
    {
        $groups =& $this->getGroups();

        return $groups;
    }

    /**
     * Is the user admin ?
     *
     * This method will return true if this user has admin rights for the specified module.<br>
     * - If you don't specify any module ID, the current module will be checked.<br>
     * - If you set the module_id to -1, it will return true if the user has admin rights for at least one module
     *
     * @param  int $module_id check if user is admin of this module
     * @return bool is the user admin of that module?
     */
    public function isAdmin($module_id = null)
    {
        if (null === $module_id) {
            $module_id = (isset($GLOBALS['xoopsModule']) && is_object($GLOBALS['xoopsModule'])) ? $GLOBALS['xoopsModule']->getVar('mid', 'n') : 1;
        } elseif ((int)$module_id < 1) {
            $module_id = 0;
        }
        /* @var $moduleperm_handler XoopsGroupPermHandler  */
        $moduleperm_handler = xoops_getHandler('groupperm');

        return $moduleperm_handler->checkRight('module_admin', $module_id, $this->getGroups());
    }

    /**
     * get the user's rank
     * @return array array of rank ID and title
     */
    public function rank()
    {
        if (!isset($this->_rank)) {
            $this->_rank = xoops_getrank($this->getVar('rank'), $this->getVar('posts'));
        }

        return $this->_rank;
    }

    /**
     * is the user activated?
     * @return bool
     */
    public function isActive()
    {
        return !($this->getVar('level') == 0);
    }

    /**
     * is the user currently logged in?
     * @return bool
     */
    public function isOnline()
    {
        if (!isset($this->_isOnline)) {
            /* @var $online_handler XoopsOnlineHandler  */
            $onlinehandler   = xoops_getHandler('online');
            $this->_isOnline = ($onlinehandler->getCount(new Criteria('online_uid', $this->getVar('uid'))) > 0);// ? true : false;
        }

        return $this->_isOnline;
    }

    /**
     * get the users UID
     * @param  string $format
     * @return int
     */
    public function uid($format = '')
    {
        return $this->getVar('uid', $format);
    }

    /**
     * get the users UID
     * @param  string $format
     * @return int
     */
    public function id($format = 'N')
    {
        return $this->getVar('uid', $format);
    }

    /**
     * get the users name
     * @param  string $format format for the output, see {@link XoopsObject::getVar($format = '')}
     * @return string
     */
    public function name($format = 'S')
    {
        return $this->getVar('name', $format);
    }

    /**
     * get the user's uname
     * @param  string $format format for the output, see {@link XoopsObject::getVar($format = '')}
     * @return string
     */
    public function uname($format = 'S')
    {
        return $this->getVar('uname', $format);
    }

    /**
     * get the user's email
     *
     * @param  string $format format for the output, see {@link XoopsObject::getVar($format = '')}
     * @return string
     */
    public function email($format = 'S')
    {
        return $this->getVar('email', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function url($format = 'S')
    {
        return $this->getVar('url', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_avatar($format = 'S')
    {
        return $this->getVar('user_avatar', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_regdate($format = '')
    {
        return $this->getVar('user_regdate', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_icq($format = 'S')
    {
        return $this->getVar('user_icq', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_from($format = 'S')
    {
        return $this->getVar('user_from', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_sig($format = 'S')
    {
        return $this->getVar('user_sig', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_viewemail($format = '')
    {
        return $this->getVar('user_viewemail', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function actkey($format = '')
    {
        return $this->getVar('actkey', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_aim($format = 'S')
    {
        return $this->getVar('user_aim', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_yim($format = 'S')
    {
        return $this->getVar('user_yim', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_msnm($format = 'S')
    {
        return $this->getVar('user_msnm', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function pass($format = '')
    {
        return $this->getVar('pass', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function posts($format = '')
    {
        return $this->getVar('posts', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function attachsig($format = '')
    {
        return $this->getVar('attachsig', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function level($format = '')
    {
        return $this->getVar('level', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function theme($format = '')
    {
        return $this->getVar('theme', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function timezone($format = '')
    {
        return $this->getVar('timezone_offset', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function umode($format = '')
    {
        return $this->getVar('umode', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function uorder($format = '')
    {
        return $this->getVar('uorder', $format);
    }

    // RMV-NOTIFY
    /**
     * @param string $format
     *
     * @return mixed
     */
    public function notify_method($format = '')
    {
        return $this->getVar('notify_method', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function notify_mode($format = '')
    {
        return $this->getVar('notify_mode', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_occ($format = 'S')
    {
        return $this->getVar('user_occ', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function bio($format = 'S')
    {
        return $this->getVar('bio', $format);
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function user_intrest($format = 'S')
    {
        return $this->getVar('user_intrest', $format);
    }
    /**#@-*/

    /**#@+
     * @deprecated
     */
    public function getProfile()
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }
    /**#@-*/
}

/**
 * Class that represents a guest user
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             kernel
 */
class XoopsGuestUser extends XoopsUser
{
    /**
     * check if the user is a guest user
     *
     * @return bool returns true
     *
     */
    public function isGuest()
    {
        return true;
    }
}

/**
 * XOOPS user handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 * @package kernel
 */
class XoopsUserHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param XoopsDatabase|null| $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'users', 'XoopsUser', 'uid', 'uname');
    }

    /**#@+
     * @deprecated
     * @param bool $uname
     * @param      $pwd
     * @param bool $md5
     * @return bool|object
     */
    public function &loginUser($uname, $pwd, $md5 = false)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param $uid
     *
     * @return bool
     */
    public function updateUserByField($fieldName, $fieldValue, $uid)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }
    /**#@-*/
}
