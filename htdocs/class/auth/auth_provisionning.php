<?php
/**
 * Authentification provisionning class
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
 * @subpackage          auth
 * @since               2.0
 * @author              Pierre-Eric MENUET <pemphp@free.fr>
 */
if (!defined('XOOPS_ROOT_PATH')) {
    throw new \RuntimeException('Restricted access');
}

/**
 *
 * @package             kernel
 * @subpackage          auth
 * @description         Authentification provisionning class. This class is responsible to
 * provide synchronisation method to Xoops User Database
 * @author              Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 */
class XoopsAuthProvisionning
{
    protected ?XoopsAuth $_auth_instance;
    protected string $ldap_provisionning;
    protected string $ldap_provisionning_upd;
    protected array $ldap_provisionning_group;
    protected string $ldap_field_mapping;
    protected string $default_TZ;
    protected string $theme_set;
    protected string $com_mode;
    protected string $com_order;

    /**
     * XoopsAuthProvisionning::getInstance()
     *
     * @param mixed $auth_instance
     *
     * @return \XoopsAuthProvisionning
     */
    public static function getInstance(?XoopsAuth $auth_instance = null)
    {
        static $provis_instance;
        if (!isset($provis_instance)) {
            $provis_instance = new XoopsAuthProvisionning($auth_instance);
        }

        return $provis_instance;
    }

    /**
     * Authentication Service constructor
     * @param XoopsAuth|null $auth_instance
     */
    public function __construct(?XoopsAuth $auth_instance = null)
    {
        $this->_auth_instance = $auth_instance;
        /** @var XoopsConfigHandler $config_handler */
        $config_handler       = xoops_getHandler('config');
        $config               = $config_handler->getConfigsByCat(XOOPS_CONF_AUTH);
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
        $config_gen       = $config_handler->getConfigsByCat(XOOPS_CONF);
        $this->default_TZ = $config_gen['default_TZ'];
        $this->theme_set  = $config_gen['theme_set'];
        $this->com_mode   = $config_gen['com_mode'];
        $this->com_order  = $config_gen['com_order'];
    }

    /**
     * Return a Xoops User Object
     *
     * @param string $uname
     * @return XoopsUser|false
     */
    public function getXoopsUser($uname)
    {
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        $criteria       = new Criteria('uname', $uname);
        $getuser        = $member_handler->getUsers($criteria);
        if (count($getuser) == 1) {
            return $getuser[0];
        } else {
            return false;
        }
    }

    /**
     * Launch the synchronisation process
     *
     * @param array  $datas
     * @param string $uname
     * @param string|null  $pwd
     * @return XoopsUser|false
     */
    public function sync($datas, $uname, $pwd = null)
    {
        $xoopsUser = $this->getXoopsUser($uname);
        if (!$xoopsUser) { // Xoops User Database not exists
            if ($this->ldap_provisionning) {
                $xoopsUser = $this->add($datas, $uname, $pwd);
            } elseif ($this->_auth_instance) {
                $this->_auth_instance->setErrors(0, sprintf(_AUTH_LDAP_XOOPS_USER_NOTFOUND, $uname));
            } else {
                // If no auth instance, we cannot set errors
                // This is a fallback for legacy code
                trigger_error(sprintf(_AUTH_LDAP_XOOPS_USER_NOTFOUND, $uname), E_USER_WARNING);
            }
        } else { // Xoops User Database exists
            if ($this->ldap_provisionning && $this->ldap_provisionning_upd) {
                $xoopsUser = $this->change($xoopsUser, $datas, $uname, $pwd);
            }
        }

        return $xoopsUser;
    }

    /**
     * Add a new user to the system
     *
     * @param array       $datas
     * @param string      $uname
     * @param string|null $pwd
     * @return XoopsUser|false
     */
    public function add($datas, $uname, $pwd = null)
    {
        $ret = false;
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        // Create XOOPS Database User
        $newuser = $member_handler->createUser();
        $newuser->setVar('uname', $uname);
        if ($pwd === null || trim($pwd) === '') {
            redirect_header(XOOPS_URL . '/user.php', 5, 'Password cannot be empty.');
        }
        $passwordValue = trim($pwd);
        $newuser->setVar('pass', password_hash(stripslashes($passwordValue), PASSWORD_DEFAULT));
        $newuser->setVar('rank', 0);
        $newuser->setVar('level', 1);
        $newuser->setVar('timezone_offset', $this->default_TZ);
        $newuser->setVar('theme', $this->theme_set);
        $newuser->setVar('umode', $this->com_mode);
        $newuser->setVar('uorder', $this->com_order);
        $tab_mapping = explode('|', $this->ldap_field_mapping);
        foreach ($tab_mapping as $mapping) {
            $fields = explode('=', trim($mapping));
            if ($fields[0] && $fields[1]) {
                $newuser->setVar(trim($fields[0]), xoops_utf8_decode($datas[trim($fields[1])][0]));
            }
        }
        if ($member_handler->insertUser($newuser)) {
            foreach ($this->ldap_provisionning_group as $groupid) {
                $member_handler->addUserToGroup($groupid, $newuser->getVar('uid'));
            }
            $newuser->unsetNew();

            return $newuser;
        } else {
            redirect_header(XOOPS_URL . '/user.php', 5, $newuser->getHtmlErrors());
        }

        return $ret;
    }

    /**
     * Modify user information
     *
     * @param XoopsUser   $xoopsUser
     * @param array       $datas
     * @param string      $uname
     * @param string|null $pwd
     * @return XoopsUser|false
     */
    public function change($xoopsUser, $datas, $uname, $pwd = null)
    {
        $ret = false;
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        $xoopsUser->setVar('pass', password_hash(stripcslashes((string)$pwd), PASSWORD_DEFAULT));
        $tab_mapping = explode('|', $this->ldap_field_mapping);
        foreach ($tab_mapping as $mapping) {
            $fields = explode('=', trim($mapping));
            if ($fields[0] && $fields[1]) {
                $xoopsUser->setVar(trim($fields[0]), xoops_utf8_decode($datas[trim($fields[1])][0]));
            }
        }
        if ($member_handler->insertUser($xoopsUser)) {
            return $xoopsUser;
        } else {
            redirect_header(XOOPS_URL . '/user.php', 5, $xoopsUser->getHtmlErrors());
        }

        return $ret;
    }

    /**
     * Modify a user
     *
     * @return bool
     */
    public function delete() {}

    /**
     * Suspend a user
     *
     * @return bool
     */
    public function suspend() {}

    /**
     * Restore a user
     *
     * @return bool
     */
    public function restore() {}

    /**
     * Add a new user to the system
     *
     * @return bool
     */
    public function resetpwd() {}
} // end class
