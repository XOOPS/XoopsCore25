<?php
/**
 * Extended User Profile
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         profile
 * @since           2.4.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id$
 */

// defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * Profile core preloads
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          trabis <lusopoemas@gmail.com>
 */
class ProfileCorePreload extends XoopsPreloadItem
{
    /**
     * @param $args
     */
    function eventCoreUserStart($args)
    {
        $op = 'main';
        if (isset($_POST['op'])) {
            $op = trim($_POST['op']);
        } else if (isset($_GET['op'])) {
            $op = trim($_GET['op']);
        }
        if ($op != 'login' && (empty($_GET['from']) || 'profile' != $_GET['from'])) {
            header("location: ./modules/profile/user.php" . (empty($_SERVER['QUERY_STRING']) ? "" : "?" . $_SERVER['QUERY_STRING']));
            exit();
        }
    }

    /**
     * @param $args
     */
    function eventCoreEdituserStart($args)
    {
        header("location: ./modules/profile/edituser.php" . (empty($_SERVER['QUERY_STRING']) ? "" : "?" . $_SERVER['QUERY_STRING']));
        exit();
    }

    /**
     * @param $args
     */
    function eventCoreLostpassStart($args)
    {
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : $email;
        header("location: ./modules/profile/lostpass.php?email={$email}" . (empty($_GET['code']) ? "" : "&" . $_GET['code']));
        exit();
    }

    /**
     * @param $args
     */
    function eventCoreRegisterStart($args)
    {
        header("location: ./modules/profile/register.php" . (empty($_SERVER['QUERY_STRING']) ? "" : "?" . $_SERVER['QUERY_STRING']) );
        exit();
    }

    /**
     * @param $args
     */
    function eventCoreUserinfoStart($args)
    {
        header("location: ./modules/profile/userinfo.php" . (empty($_SERVER['QUERY_STRING']) ? "" : "?" . $_SERVER['QUERY_STRING']) );
        exit();
    }

}
