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
 * @description         Authentification class for Native XOOPS
 * @author              Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsAuthXoops extends XoopsAuth
{
    /**
     * Authentication Service constructor
     * @param XoopsDatabase $dao
     */
    public function __construct(XoopsDatabase $dao = null)
    {
        $this->_dao        = $dao;
        $this->auth_method = 'xoops';
    }

    /**
     * Authenticate user
     *
     * @param  string $uname
     * @param  string $pwd
     * @return bool
     */
    public function authenticate($uname, $pwd = null)
    {
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $user           = $member_handler->loginUser($uname, $pwd);
        if ($user == false) {
            $this->setErrors(1, _US_INCORRECTLOGIN);
        }

        return $user;
    }
}
