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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          auth
 * @since               2.0
 * @author              Pierre-Eric MENUET <pemphp@free.fr>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 *
 * @package             kernel
 * @subpackage          auth
 * @description         Authentification class for Active Directory
 * @author              Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
include_once $GLOBALS['xoops']->path('class/auth/auth_ldap.php');

/**
 * XoopsAuthAds
 *
 * @package
 * @author              John
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @access              public
 */
class XoopsAuthAds extends XoopsAuthLdap
{
    /**
     * Authentication Service constructor
     * @param XoopsDatabase $dao
     */
    public function __construct(XoopsDatabase $dao = null)
    {
        parent::__construct($dao);
    }

    /**
     * Authenticate  user again LDAP directory (Bind)
     *         2 options :
     *         Authenticate directly with uname in the DN
     *         Authenticate with manager, search the dn
     *
     * @param  string $uname Username
     * @param  string $pwd   Password
     * @return bool
     */
    public function authenticate($uname, $pwd = null)
    {
        $authenticated = false;
        if (!extension_loaded('ldap')) {
            $this->setErrors(0, _AUTH_LDAP_EXTENSION_NOT_LOAD);

            return $authenticated;
        }
        $this->_ds = ldap_connect($this->ldap_server, $this->ldap_port);
        if ($this->_ds) {
            ldap_set_option($this->_ds, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version);
            ldap_set_option($this->_ds, LDAP_OPT_REFERRALS, 0);
            if ($this->ldap_use_TLS) { // We use TLS secure connection
                if (!ldap_start_tls($this->_ds)) {
                    $this->setErrors(0, _AUTH_LDAP_START_TLS_FAILED);
                }
            }
            // If the uid is not in the DN we proceed to a search
            // The uid is not always in the dn
            $userUPN = $this->getUPN($uname);
            if (!$userUPN) {
                return false;
            }
            // We bind as user to test the credentials
            $authenticated = ldap_bind($this->_ds, $userUPN, $this->cp1252_to_utf8(stripslashes($pwd)));
            if ($authenticated) {
                // We load the Xoops User database
                $dn = $this->getUserDN($uname);
                if ($dn) {
                    return $this->loadXoopsUser($dn, $uname, $pwd);
                } else {
                    return false;
                }
            } else {
                $this->setErrors(ldap_errno($this->_ds), ldap_err2str(ldap_errno($this->_ds)) . '(' . $userUPN . ')');
            }
        } else {
            $this->setErrors(0, _AUTH_LDAP_SERVER_NOT_FOUND);
        }
        @ldap_close($this->_ds);

        return $authenticated;
    }

    /**
     * Return the UPN = userPrincipalName (Active Directory)
     *         userPrincipalName = guyt@CP.com    Often abbreviated to UPN, and
     *         looks like an email address.  Very useful for logging on especially in
     *         a large Forest.   Note UPN must be unique in the forest.
     *
     * @param $uname
     *
     * @return userDN or false
     */
    public function getUPN($uname)
    {
        $userDN = $uname . '@' . $this->ldap_domain_name;

        return $userDN;
    }
} // end class

