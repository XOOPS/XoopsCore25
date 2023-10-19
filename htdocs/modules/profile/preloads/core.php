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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.4.0
 * @author              trabis <lusopoemas@gmail.com>
 */

use Xmf\Request;

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Profile core preloads
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              trabis <lusopoemas@gmail.com>
 */
class ProfileCorePreload extends XoopsPreloadItem
{
    /**
     * @param $args
     */
    public static function eventCoreUserStart($args)
    {
        $op = 'main';
        if (isset($_POST['op'])) {
            $op = Request::getString('op', '', 'POST');
        } elseif (isset($_GET['op'])) {
            $op = Request::getString('op', '', 'GET');
        }
        if ($op !== 'login' && (empty($_GET['from']) || 'profile' !== $_GET['from'])) {
            header('location: ./modules/profile/user.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
            exit();
        }
    }

    /**
     * @param $args
     */
    public static function eventCoreEdituserStart($args)
    {
        header('location: ./modules/profile/edituser.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
        exit();
    }

    /**
     * @param $args
     */
    public static function eventCoreLostpassStart($args)
    {
        $email = Request::getEmail('email', '', 'GET');
        $email = Request::getEmail('email', $email, 'POST');
        header("location: ./modules/profile/lostpass.php?email={$email}" . (empty($_GET['code']) ? '' : '&code=' . Request::getString('code', '', 'GET')));
        exit();
    }

    /**
     * @param $args
     */
    public static function eventCoreRegisterStart($args)
    {
        header('location: ./modules/profile/register.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
        exit();
    }

    /**
     * @param $args
     */
    public static function eventCoreUserinfoStart($args)
    {
        header('location: ./modules/profile/userinfo.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
        exit();
    }
}
