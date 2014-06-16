<?php
/**
 * XOOPS Authentification base class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright           The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          auth
 * @since               2.0
 * @author              Pierre-Eric MENUET <pemphp@free.fr>
 * @version             $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 *
 * @package kernel
 * @subpackage auth
 * @description Authentification class for standard LDAP Server V2 or V3
 * @author Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 */
if (file_exists($file = $GLOBALS['xoops']->path('class/auth/auth_provisionning.php'))) {
    include_once $file;
}

if (!class_exists('XoopsAuthProvisionning')) {
    trigger_error('Required class XoopsAuthProvisionning was not found at line ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);

    return false;
}

/**
 * XoopsAuthLdap
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id$
 * @access public
 */
class XoopsAuthLdap extends XoopsAuth
{
    var $cp1252_map = array("\xc2\x80" => "\xe2\x82\xac" , /**
     * EURO SIGN
     */
    "\xc2\x82" => "\xe2\x80\x9a" , /**
     * SINGLE LOW-9 QUOTATION MARK
     */
    "\xc2\x83" => "\xc6\x92" , /**
     * LATIN SMALL LETTER F WITH HOOK
     */
    "\xc2\x84" => "\xe2\x80\x9e" , /**
     * DOUBLE LOW-9 QUOTATION MARK
     */
    "\xc2\x85" => "\xe2\x80\xa6" , /**
     * HORIZONTAL ELLIPSIS
     */
    "\xc2\x86" => "\xe2\x80\xa0" , /**
     * DAGGER
     */
    "\xc2\x87" => "\xe2\x80\xa1" , /**
     * DOUBLE DAGGER
     */
    "\xc2\x88" => "\xcb\x86" , /**
     * MODIFIER LETTER CIRCUMFLEX ACCENT
     */
    "\xc2\x89" => "\xe2\x80\xb0" , /**
     * PER MILLE SIGN
     */
    "\xc2\x8a" => "\xc5\xa0" , /**
     * LATIN CAPITAL LETTER S WITH CARON
     */
    "\xc2\x8b" => "\xe2\x80\xb9" , /**
     * SINGLE LEFT-POINTING ANGLE QUOTATION
     */
    "\xc2\x8c" => "\xc5\x92" , /**
     * LATIN CAPITAL LIGATURE OE
     */
    "\xc2\x8e" => "\xc5\xbd" , /**
     * LATIN CAPITAL LETTER Z WITH CARON
     */
    "\xc2\x91" => "\xe2\x80\x98" , /**
     * LEFT SINGLE QUOTATION MARK
     */
    "\xc2\x92" => "\xe2\x80\x99" , /**
     * RIGHT SINGLE QUOTATION MARK
     */
    "\xc2\x93" => "\xe2\x80\x9c" , /**
     * LEFT DOUBLE QUOTATION MARK
     */
    "\xc2\x94" => "\xe2\x80\x9d" , /**
     * RIGHT DOUBLE QUOTATION MARK
     */
    "\xc2\x95" => "\xe2\x80\xa2" , /**
     * BULLET
     */
    "\xc2\x96" => "\xe2\x80\x93" , /**
     * EN DASH
     */
    "\xc2\x97" => "\xe2\x80\x94" , /**
     * EM DASH
     */
    "\xc2\x98" => "\xcb\x9c" , /**
     * SMALL TILDE
     */
    "\xc2\x99" => "\xe2\x84\xa2" , /**
     * TRADE MARK SIGN
     */
    "\xc2\x9a" => "\xc5\xa1" , /**
     * LATIN SMALL LETTER S WITH CARON
     */
    "\xc2\x9b" => "\xe2\x80\xba" , /**
     * SINGLE RIGHT-POINTING ANGLE QUOTATION
     */
    "\xc2\x9c" => "\xc5\x93" , /**
     * LATIN SMALL LIGATURE OE
     */
    "\xc2\x9e" => "\xc5\xbe" , /**
     * LATIN SMALL LETTER Z WITH CARON
     */
    "\xc2\x9f" => "\xc5\xb8");
    /**
     * LATIN CAPITAL LETTER Y WITH DIAERESIS
     */

    var $ldap_server;
    var $ldap_port = '389';
    var $ldap_version = '3';
    var $ldap_base_dn;
    var $ldap_loginname_asdn;
    var $ldap_loginldap_attr;
    var $ldap_mail_attr;
    var $ldap_name_attr;
    var $ldap_surname_attr;
    var $ldap_givenname_attr;
    var $ldap_manager_dn;
    var $ldap_manager_pass;
    var $_ds;

    /**
     * Authentication Service constructor
     */
    function XoopsAuthLdap(&$dao)
    {
        $this->_dao = $dao;
        // The config handler object allows us to look at the configuration options that are stored in the database
        $config_handler =& xoops_gethandler('config');
        $config = $config_handler->getConfigsByCat(XOOPS_CONF_AUTH);
        $confcount = count($config);
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * XoopsAuthLdap::cp1252_to_utf8()
     *
     * @param mixed $str
     *
     * @return string
     */
    function cp1252_to_utf8($str)
    {
        return strtr(utf8_encode($str), $this->cp1252_map);
    }

    /**
     * Authenticate  user again LDAP directory (Bind)
     *               2 options :
     * 		Authenticate directly with uname in the DN
     * 		Authenticate with manager, search the dn
     *
     * @param  string $uname Username
     * @param  string $pwd   Password
     * @return bool
     */
    function authenticate($uname, $pwd = null)
    {
        $authenticated = false;
        if (!extension_loaded('ldap')) {
            $this->setErrors(0, _AUTH_LDAP_EXTENSION_NOT_LOAD);

            return $authenticated;
        }
        $this->_ds = ldap_connect($this->ldap_server, $this->ldap_port);
        if ($this->_ds) {
            ldap_set_option($this->_ds, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version);
            if ($this->ldap_use_TLS) { // We use TLS secure connection
                if (!ldap_start_tls($this->_ds)) {
                    $this->setErrors(0, _AUTH_LDAP_START_TLS_FAILED);
                }
            }
            // If the uid is not in the DN we proceed to a search
            // The uid is not always in the dn
            $userDN = $this->getUserDN($uname);
            if (!$userDN) {
                return false;
            }
                // We bind as user to test the credentials
            $authenticated = ldap_bind($this->_ds, $userDN, stripslashes($pwd));
            if ($authenticated) {
                // We load the Xoops User database
                return $this->loadXoopsUser($userDN, $uname, $pwd);
            } else {
                $this->setErrors(ldap_errno($this->_ds), ldap_err2str(ldap_errno($this->_ds)) . '(' . $userDN . ')');
            }
        } else {
            $this->setErrors(0, _AUTH_LDAP_SERVER_NOT_FOUND);
        }
        @ldap_close($this->_ds);

        return $authenticated;
    }

    /**
     * Compose the user DN with the configuration.
     *
     * @param $uname
     * @return userDN or false
     */
    function getUserDN($uname)
    {
        $userDN = false;
        if (!$this->ldap_loginname_asdn) {
            // Bind with the manager
            if (!ldap_bind($this->_ds, $this->ldap_manager_dn, stripslashes($this->ldap_manager_pass))) {
                $this->setErrors(ldap_errno($this->_ds), ldap_err2str(ldap_errno($this->_ds)) . '(' . $this->ldap_manager_dn . ')');

                return false;
            }
            $filter = $this->getFilter($uname);
            $sr = ldap_search($this->_ds, $this->ldap_base_dn, $filter);
            $info = ldap_get_entries($this->_ds, $sr);
            if ($info['count'] > 0) {
                $userDN = $info[0]['dn'];
            } else {
                $this->setErrors(0, sprintf(_AUTH_LDAP_USER_NOT_FOUND, $uname, $filter, $this->ldap_base_dn));
            }
        } else {
            $userDN = $this->ldap_loginldap_attr . '=' . $uname . ',' . $this->ldap_base_dn;
        }

        return $userDN;
    }

    /**
     * Load user from XOOPS Database
     *
     * @param $uname
     * @return XoopsUser object
     */
    function getFilter($uname)
    {
        $filter = '';
        if ($this->ldap_filter_person != '') {
            $filter = str_replace('@@loginname@@', $uname, $this->ldap_filter_person);
        } else {
            $filter = $this->ldap_loginldap_attr . '=' . $uname;
        }

        return $filter;
    }

    /**
     * XoopsAuthLdap::loadXoopsUser()
     *
     * @param  mixed $userdn
     * @param  mixed $uname
     * @param  mixed $pwd
     * @return bool
     */
    function loadXoopsUser($userdn, $uname, $pwd = null)
    {
        $provisHandler = XoopsAuthProvisionning::getInstance($this);
        $sr = ldap_read($this->_ds, $userdn, '(objectclass=*)');
        $entries = ldap_get_entries($this->_ds, $sr);
        if ($entries['count'] > 0) {
            $xoopsUser = $provisHandler->sync($entries[0], $uname, $pwd);
        } else {
            $this->setErrors(0, sprintf('loadXoopsUser - ' . _AUTH_LDAP_CANT_READ_ENTRY, $userdn));
        }

        return $xoopsUser;
    }
} // end class
