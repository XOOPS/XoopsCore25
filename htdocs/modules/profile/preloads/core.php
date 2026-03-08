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
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.4.0
 * @author              trabis <lusopoemas@gmail.com>
 */

use Xmf\Request;

//if (!defined('XOOPS_ROOT_PATH')) {
//    throw new \RuntimeException('XOOPS root path not defined');
//}

/**
 * Profile core preloads
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
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
        if (Request::hasVar('op', 'POST')) {
            $op = Request::getString('op', '', 'POST');
        } elseif (Request::hasVar('op', 'GET')) {
            $op = Request::getString('op', '', 'GET');
        }
        $from = Request::getString('from', '', 'GET');
        if ($op !== 'login' && $from !== 'profile') {
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
        // Disabled: profile module's lostpass used a weak md5-based token.
        // All password resets now go through the secure core flow (htdocs/lostpass.php)
        // which uses random, one-time, expiring tokens via XoopsTokenHandler.
        return;
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
