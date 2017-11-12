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
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

include __DIR__ . '/header.php';
include_once $GLOBALS['xoops']->path('modules/system/constants.php');

$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
if ($uid <= 0) {
    if (is_object($GLOBALS['xoopsUser'])) {
        $uid = $GLOBALS['xoopsUser']->getVar('uid');
    } else {
        header('location: ' . XOOPS_URL);
        exit();
    }
}

/* @var  $gperm_handler XoopsGroupPermHandler */
$gperm_handler = xoops_getHandler('groupperm');
$groups        = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getGroups() : array(XOOPS_GROUP_ANONYMOUS);

if (is_object($GLOBALS['xoopsUser']) && $uid == $GLOBALS['xoopsUser']->getVar('uid')) {
    //disable cache
    $GLOBALS['xoopsConfig']['module_cache'][$GLOBALS['xoopsModule']->getVar('mid')] = 0;
    $GLOBALS['xoopsOption']['template_main']                                                   = 'profile_userinfo.tpl';
    include $GLOBALS['xoops']->path('header.php');

    /* @var $config_handler XoopsConfigHandler  */
    $config_handler             = xoops_getHandler('config');
    $GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);

    $GLOBALS['xoopsTpl']->assign('user_ownpage', true);
    $GLOBALS['xoopsTpl']->assign('lang_editprofile', _US_EDITPROFILE);
    $GLOBALS['xoopsTpl']->assign('lang_changepassword', _PROFILE_MA_CHANGEPASSWORD);
    $GLOBALS['xoopsTpl']->assign('lang_avatar', _US_AVATAR);
    $GLOBALS['xoopsTpl']->assign('lang_inbox', _US_INBOX);
    $GLOBALS['xoopsTpl']->assign('lang_logout', _US_LOGOUT);
    if ($GLOBALS['xoopsConfigUser']['self_delete'] == 1) {
        $GLOBALS['xoopsTpl']->assign('user_candelete', true);
        $GLOBALS['xoopsTpl']->assign('lang_deleteaccount', _US_DELACCOUNT);
    } else {
        $GLOBALS['xoopsTpl']->assign('user_candelete', false);
    }
    $GLOBALS['xoopsTpl']->assign('user_changeemail', $GLOBALS['xoopsConfigUser']['allow_chgmail']);
    $thisUser =& $GLOBALS['xoopsUser'];
} else {
    /* @var $member_handler XoopsMemberHandler */
    $member_handler = xoops_getHandler('member');
    $thisUser       = $member_handler->getUser($uid);

    // Redirect if not a user or not active and the current user is not admin
    if (!is_object($thisUser) || (!$thisUser->isActive() && (!$GLOBALS['xoopsUser'] || !$GLOBALS['xoopsUser']->isAdmin()))) {
        redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n'), 3, _US_SELECTNG);
    }

    /**
     * Access permission check
     *
     * Note:
     * "thisUser" refers to the user whose profile will be accessed; "xoopsUser" refers to the current user $GLOBALS['xoopsUser']
     * "Basic Groups" refer to XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS and XOOPS_GROUP_ANONYMOUS;
     * "Non Basic Groups" refer to all other custom groups
     *
     * Admin groups: If thisUser belongs to admin groups, the xoopsUser has access if and only if one of xoopsUser's groups is allowed to access admin group; else
     * Non basic groups: If thisUser belongs to one or more non basic groups, the xoopsUser has access if and only if one of xoopsUser's groups is allowed to allowed to any of the non basic groups; else
     * User group: If thisUser belongs to User group only, the xoopsUser has access if and only if one of his groups is allowed to access User group
     *
     */
    // Redirect if current user is not allowed to access the user's profile based on group permission
    $groups_basic             = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
    $groups_thisUser          = $thisUser->getGroups();
    $groups_thisUser_nonbasic = array_diff($groups_thisUser, $groups_basic);
    $groups_xoopsUser         = $groups;
    /* @var  $gperm_handler XoopsGroupPermHandler */
    $gperm_handler            = xoops_getHandler('groupperm');
    $groups_accessible        = $gperm_handler->getItemIds('profile_access', $groups_xoopsUser, $GLOBALS['xoopsModule']->getVar('mid'));

    $rejected = false;
    if ($thisUser->isAdmin()) {
        $rejected = !in_array(XOOPS_GROUP_ADMIN, $groups_accessible);
    } elseif ($groups_thisUser_nonbasic) {
        $rejected = !array_intersect($groups_thisUser_nonbasic, $groups_accessible);
    } else {
        $rejected = !in_array(XOOPS_GROUP_USERS, $groups_accessible);
    }

    if ($rejected) {
        redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n'), 3, _NOPERM);
    }

    if (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin()) {
        //disable cache
        $GLOBALS['xoopsConfig']['module_cache'][$GLOBALS['xoopsModule']->getVar('mid')] = 0;
    }
    $GLOBALS['xoopsOption']['template_main'] = 'profile_userinfo.tpl';
    include $GLOBALS['xoops']->path('header.php');
    $GLOBALS['xoopsTpl']->assign('user_ownpage', false);
}

$GLOBALS['xoopsTpl']->assign('user_uid', $thisUser->getVar('uid'));
if (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin()) {
    $GLOBALS['xoopsTpl']->assign('lang_editprofile', _US_EDITPROFILE);
    $GLOBALS['xoopsTpl']->assign('lang_deleteaccount', _US_DELACCOUNT);
    $GLOBALS['xoopsTpl']->assign('userlevel', $thisUser->isActive());
}

$xoopsOption['xoops_pagetitle'] = sprintf(_US_ALLABOUT, $thisUser->getVar('uname'));

// Dynamic User Profiles
$thisUsergroups     = $thisUser->getGroups();
$visibility_handler = xoops_getModuleHandler('visibility');
//search for visible Fields or null for none
$field_ids_visible = $visibility_handler->getVisibleFields($thisUsergroups, $groups);

$profile_handler = xoops_getModuleHandler('profile');
$fields          = $profile_handler->loadFields();
$cat_handler     = xoops_getModuleHandler('category');
$cat_crit        = new CriteriaCompo();
$cat_crit->setSort('cat_weight');
$cats = $cat_handler->getObjects($cat_crit, true, false);
unset($cat_crit);

$avatar = '';
if ($thisUser->getVar('user_avatar') && 'blank.gif' !== $thisUser->getVar('user_avatar')) {
    $avatar = XOOPS_UPLOAD_URL . '/' . $thisUser->getVar('user_avatar');
}

$email = '';
if ($thisUser->getVar('user_viewemail') == 1) {
    $email = $thisUser->getVar('email', 'E');
} elseif (is_object($GLOBALS['xoopsUser'])) {
    // Module admins will be allowed to see emails
    if ($GLOBALS['xoopsUser']->isAdmin() || ($GLOBALS['xoopsUser']->getVar('uid') == $thisUser->getVar('uid'))) {
        $email = $thisUser->getVar('email', 'E');
    }
}
foreach (array_keys($cats) as $i) {
    $categories[$i] = $cats[$i];
}

$profile_handler = xoops_getModuleHandler('profile');
$profile         = $profile_handler->get($thisUser->getVar('uid'));
// Add dynamic fields
foreach (array_keys($fields) as $i) {
    //If field is not visible, skip
    //if ( $field_ids_visible && !in_array($fields[$i]->getVar('field_id'), $field_ids_visible) ) continue;
    if (!in_array($fields[$i]->getVar('field_id'), $field_ids_visible)) {
        continue;
    }
    $cat_id = $fields[$i]->getVar('cat_id');
    $value  = $fields[$i]->getOutputValue($thisUser, $profile);
    if (is_array($value)) {
        $value = implode('<br>', array_values($value));
    }
    if ($value) {
        $categories[$cat_id]['fields'][] = array('title' => $fields[$i]->getVar('field_title'), 'value' => $value);
        $weights[$cat_id][]              = $fields[$i]->getVar('cat_id');
    }
}

$GLOBALS['xoopsTpl']->assign('categories', $categories);
// Dynamic user profiles end

if ($GLOBALS['xoopsModuleConfig']['profile_search']) {
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $criteria       = new CriteriaCompo(new Criteria('hassearch', 1));
    $criteria->add(new Criteria('isactive', 1));
    $modules = $module_handler->getObjects($criteria, true);
    $mids    = array_keys($modules);

    $myts         = MyTextSanitizer::getInstance();
    $allowed_mids = $gperm_handler->getItemIds('module_read', $groups);
    if (count($mids) > 0 && count($allowed_mids) > 0) {
        foreach ($mids as $mid) {
            if (in_array($mid, $allowed_mids)) {
                $results = $modules[$mid]->search('', '', 5, 0, $thisUser->getVar('uid'));
                $count = 0;
                if(is_array($results)) {
                    $count = count($results);
                }
                if (is_array($results) && $count > 0) {
                    for ($i = 0; $i < $count; ++$i) {
                        if (isset($results[$i]['image']) && $results[$i]['image'] != '') {
                            $results[$i]['image'] = XOOPS_URL . '/modules/' . $modules[$mid]->getVar('dirname', 'n') . '/' . $results[$i]['image'];
                        } else {
                            $results[$i]['image'] = XOOPS_URL . '/images/icons/posticon2.gif';
                        }
                        if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                            $results[$i]['link'] = XOOPS_URL . '/modules/' . $modules[$mid]->getVar('dirname', 'n') . '/' . $results[$i]['link'];
                        }
                        $results[$i]['title'] = $myts->htmlspecialchars($results[$i]['title']);
                        $results[$i]['time']  = isset($results[$i]['time']) ? formatTimestamp($results[$i]['time']) : '';
                    }
                    $showall_link = '';
                    if ($count == 5) {
                        $showall_link = '<a href="' . XOOPS_URL . '/search.php?action=showallbyuser&amp;mid=' . $mid . '&amp;uid=' . $thisUser->getVar('uid') . '">' . _US_SHOWALL . '</a>';
                    }
                    $GLOBALS['xoopsTpl']->append('modules', array(
                        'name'         => $modules[$mid]->getVar('name'),
                        'results'      => $results,
                        'showall_link' => $showall_link));
                }
                unset($modules[$mid]);
            }
        }
    }
}

//User info
$GLOBALS['xoopsTpl']->assign('uname', $thisUser->getVar('uname'));
$GLOBALS['xoopsTpl']->assign('email', $email);
$GLOBALS['xoopsTpl']->assign('avatar', $avatar);
$GLOBALS['xoopsTpl']->assign('recent_activity', _PROFILE_MA_RECENTACTIVITY);
$xoBreadcrumbs[] = array('title' => _PROFILE_MA_USERINFO);
include __DIR__ . '/footer.php';
