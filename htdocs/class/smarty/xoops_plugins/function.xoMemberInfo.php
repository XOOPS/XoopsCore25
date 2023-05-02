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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
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
 * Author:   DuGris
 * Purpose:  Get member information
 * Input:    infos = list of vars in XoopsUser to be fetched delimited by a '|'
 *                   if infos is not specified the default will be:
 *                   'uname|name|email|user_avatar|url|user_icq|user_aim|user_yim|user_msnm|posts|user_from|user_occ|user_intrest|bio|user_sig'
 *
 *           assign = smarty variable to be initialized for the templates
 *
 * Examples:
 * Get all fields
 *   <{xoMemberInfo assign=member_info}>
 *
 * Get uname, avatar and email
 *   <{xoMemberInfo assign=memberInfo infos="uname|email|avatar"}>
 *   Hello <{$memberInfo.uname}>.
 * -------------------------------------------------------------
 */

/**
 * @param string[] $params
 * @param Smarty   $smarty
 *
 * @return void
 */
function smarty_function_xoMemberInfo($params, $smarty)
{
    global $xoopsUser, $xoopsConfig;

    $member_info = array();
    if (!isset($xoopsUser) || !is_object($xoopsUser)) {
        $member_info['uname'] = $xoopsConfig['anonymous'];
    } else {
        if (@empty($params['infos'])) {
            $params['infos'] = 'uname|name|email|user_avatar|url|user_icq|user_aim|user_yim|user_msnm|posts|user_from|user_occ|user_intrest|bio|user_sig';
        }
        $infos = explode('|', $params['infos']);

        foreach ($infos as $info) {
            $value = $xoopsUser->getVar($info, 'E');
            if (null !== $value) {
                $member_info[$info] = $value;
            }
        }
    }
    if (!@empty($params['assign'])) {
        $smarty->assign($params['assign'], $member_info);
    }
}
