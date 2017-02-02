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
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoMemberInfo
 * Version:  1.0
 * Author:     DuGris
 * Purpose:  Get member informations
 * Input:    infos    =    informations to be recovered in the profile of the member
 *                        if empty uname,name,email,user_avatar,url,user_icq,user_aim,user_yim,user_msnm,user_from,
 *                        user_occ, user_intrest, bio, user_sig will be recovered
 *
 *           assign    =    variable to be initialized for the templates
 *
 *            I.e: Get all informations
 *                <{xoMemberInfo assign=member_info}>
 *
 *            I.e: Get uname, avatar and email
 *                <{xoMemberInfo assign=member_info infos="uname|email|avatar"}>
 * -------------------------------------------------------------
 */

/**
 * @param $params
 * @param $smarty
 */
function smarty_function_xoMemberInfo($params, &$smarty)
{
    global $xoopsUser, $xoopsConfig;

    $time        = time();
    $member_info = $_SESSION['xoops_member_info'];
    if (!isset($xoopsUser) || !is_object($xoopsUser)) {
        $member_info['uname'] = $xoopsConfig['anonymous'];
    } else {
        if (@empty($params['infos'])) {
            $params['infos'] = 'uname|name|email|user_avatar|url|user_icq|user_aim|user_yim|user_msnm|posts|user_from|user_occ|user_intrest|bio|user_sig';
        }
        $infos = explode('|', $params['infos']);

        if (!is_array($member_info)) {
            $member_info = array();
        }
        foreach ($infos as $info) {
            if (!array_key_exists($info, $member_info) && @$_SESSION['xoops_member_info'][$info . '_expire'] < $time) {
                $member_info[$info]                               = $xoopsUser->getVar($info, 'E');
                $_SESSION['xoops_member_info'][$info]             = $member_info[$info];
                $_SESSION['xoops_member_info'][$info . '_expire'] = $time + 60;
            }
        }
    }
    if (!@empty($params['assign'])) {
        $smarty->assign($params['assign'], $member_info);
    }
}
