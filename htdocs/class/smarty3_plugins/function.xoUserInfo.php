<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    2000-2020 XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoUserInfo
 * Version:  1.0
 * Author:   DuGris
 * Purpose:  Get data for a specified user id and assign it to a template variable.
 *           For the input uid, return an array of related information, specifically
 *           these fields from the users table:
 *           uname,name,email,user_avatar,url,posts,user_from,user_occ,user_intrest,bio,user_sig
 * Input:    uid    = uid of user for which information is requested
 *           assign = smarty variable to be initialized for the template. default 'userInfo'
 *
 * Example:
 * Get user data
 *   <{xoUserInfo uid=42 assign=memberInfo}>
 * -------------------------------------------------------------
 */

/**
 * @param $params
 * @param $smarty
 */
function smarty_function_xoUserInfo($params, &$smarty)
{
    global $xoopsUser, $xoopsConfig;

    /** @var array $usersInfo uid indexed cache of user data */
    static $usersInfo = array();

    $uid = 0;
    if (!empty($params['uid'])) {
        $uid = (int)$params['uid'];
    } elseif (isset($xoopsUser) && is_object($xoopsUser)) {
        $uid = $xoopsUser->getVar('uid');
    }

    $assign = empty($params['assign']) ? 'userInfo' : $params['assign'];

    $infoFields = array(
        'uname', 'name', 'email', 'user_avatar', 'url', 'posts',
        'user_from', 'user_occ', 'user_intrest', 'bio', 'user_sig'
    );

    if (!isset($usersInfo[0])) {
        $usersInfo[0] = array('uname' => $xoopsConfig['anonymous']);
    }

    $userHandler = xoops_getHandler('user');
    if (isset($usersInfo[$uid])) {
        $userData = $usersInfo[$uid];
    } elseif ($userObject = $userHandler->get($uid)) {
        $userData =  array();
        foreach ($infoFields as $field) {
            $userData[$field] = $userObject->getVar($field, 'E');
        }
        $usersInfo[$uid] = $userData;
    } else {
        $userData = $usersInfo[0];
    }
    $smarty->assign($assign, $userData);
}
