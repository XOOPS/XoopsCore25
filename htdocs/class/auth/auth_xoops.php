<?php
/**
 * Authentification class for Native XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package kernel
 * @subpackage auth
 * @since 2.0
 * @author Pierre-Eric MENUET <pemphp@free.fr>
 * @version $Id$
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 *
 * @package kernel
 * @subpackage auth
 * @description Authentification class for Native XOOPS
 * @author Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 */
class XoopsAuthXoops extends XoopsAuth
{
    /**
     * Authentication Service constructor
     */
    function XoopsAuthXoops(&$dao)
    {
        $this->_dao = $dao;
        $this->auth_method = 'xoops';
    }

    /**
     * Authenticate user
     *
     * @param string $uname
     * @param string $pwd
     * @return bool
     */
    function authenticate($uname, $pwd = null)
    {
        $member_handler = &xoops_gethandler('member');
        $user =& $member_handler->loginUser($uname, $pwd);
        if ($user == false) {
            $this->setErrors(1, _US_INCORRECTLOGIN);
        }
        return $user;
    }
}
