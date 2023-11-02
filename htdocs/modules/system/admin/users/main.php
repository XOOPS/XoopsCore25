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
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */
/** @var XoopsUser $xoopsUser */
/** @var XoopsModule $xoopsModule */
use Xmf\Request;

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}

include_once XOOPS_ROOT_PATH . '/modules/system/admin/users/users.php';
// Get Action type
$op = Request::getString('op', 'default');
/** @var XoopsMemberHandler $member_handler */
$member_handler = xoops_getHandler('member');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_users.tpl';
// Call Header
xoops_cp_header();

$myts = \MyTextSanitizer::getInstance();
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
// Define scripts
$xoTheme->addScript('modules/system/js/admin.js');
// Define Breadcrumb and tips
$xoBreadCrumb->addLink(_AM_SYSTEM_USERS_NAV_MAIN, system_adminVersion('users', 'adminpath'));

$uid = Request::getInt('uid', 0);
switch ($op) {

    // Edit user
    case 'users_edit':
        // Assign Breadcrumb menu
        $xoBreadCrumb->addHelp(system_adminVersion('users', 'help') . '#edit');
        $xoBreadCrumb->addLink(_AM_SYSTEM_USERS_NAV_EDIT_USER);
        $xoBreadCrumb->render();
        form_user(false, $uid);
        break;

    // Add user
    case 'users_add':
        // Assign Breadcrumb menu
        $xoBreadCrumb->addHelp(system_adminVersion('users', 'help') . '#add');
        $xoBreadCrumb->addLink(_AM_SYSTEM_USERS_NAV_ADD_USER);
        $xoBreadCrumb->render();
        form_user(true);
        break;

    // Delete user
    case 'users_delete':
        $xoBreadCrumb->render();
        $user = $member_handler->getUser($uid);
        if (Request::getInt('ok', 0) === 1) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('admin.php?fct=users', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
            }

            $groups = $user->getGroups();
            if (in_array(XOOPS_GROUP_ADMIN, $groups)) {
                xoops_error(sprintf(_AM_SYSTEM_USERS_NO_ADMINSUPP, $user->getVar('uname')));
            } elseif (!$member_handler->deleteUser($user)) {
                xoops_error(sprintf(_AM_SYSTEM_USERS_NO_SUPP, $user->getVar('uname')));
            } else {
                /** @var XoopsOnlineHandler $online_handler */
                $online_handler = xoops_getHandler('online');
                $online_handler->destroy($uid);
                // RMV-NOTIFY
                xoops_notification_deletebyuser($uid);
                redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_DBUPDATED);
            }
        } else {
            //Assign Breadcrumb menu
            $xoBreadCrumb->addHelp(system_adminVersion('users', 'help') . '#delete');
            $xoBreadCrumb->addLink(_AM_SYSTEM_USERS_NAV_DELETE_USER);
            $xoBreadCrumb->render();
            xoops_confirm(array(
                              'ok'  => 1,
                              'uid' => $uid,
                              'op'  => 'users_delete'), $_SERVER['REQUEST_URI'], sprintf(_AM_SYSTEM_USERS_FORM_SURE_DEL, $user->getVar('uname')));
        }
        break;

    // Delete users
    case 'action_group':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=users', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        if (Request::hasVar('memberslist_id')) {
            $xoBreadCrumb->render();
            $error = '';
            foreach (Request::getArray('memberslist_id', array()) as $del) {
                $del    = (int)$del;
                $user   = $member_handler->getUser($del);
                $groups = $user->getGroups();
                if (in_array(XOOPS_GROUP_ADMIN, $groups)) {
                    $error .= sprintf(_AM_SYSTEM_USERS_NO_ADMINSUPP, $user->getVar('uname'));
                } elseif (!$member_handler->deleteUser($user)) {
                    $error .= sprintf(_AM_SYSTEM_USERS_NO_SUPP, $user->getVar('uname'));
                } else {
                    /** @var XoopsOnlineHandler $online_handler */
                    $online_handler = xoops_getHandler('online');
                    $online_handler->destroy($del);
                    // RMV-NOTIFY
                    xoops_notification_deletebyuser($del);
                }
            }
            if ($error !== '') {
                redirect_header('admin.php?fct=users', 3, sprintf(_AM_SYSTEM_USERS_ERROR, $error));
            } else {
                redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_DBUPDATED);
            }
        }
        break;

    // Save user
    case 'users_save':
        global $xoopsConfig, $xoopsModule, $xoopsUser;

        if (Request::hasVar('uid')) {
            //Update user
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('admin.php?fct=users', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            // RMV-NOTIFY
            $user_avatar = $theme = null;
            if (!Request::hasVar('attachsig')) {
                $attachsig = null;
            }
            if (!Request::hasVar('user_viewemail')) {
                $user_viewemail = null;
            }

            $edituser = $member_handler->getUser($uid);
            if ($edituser->getVar('uname', 'n') != Request::getString('username') && $member_handler->getUserCount(new Criteria('uname', $myts->addSlashes(Request::getString('user_uname')))) > 0) {
                xoops_cp_header();
                xoops_error(sprintf(_AM_SYSTEM_USERS_PSEUDO_ERROR, htmlspecialchars(Request::getString('user_uname'), ENT_QUOTES)));
                xoops_cp_footer();
            } elseif ($edituser->getVar('email', 'n') != Request::getEmail('email') && $member_handler->getUserCount(new Criteria('email', $myts->addSlashes(Request::getEmail('email')))) > 0) {
                xoops_cp_header();
                xoops_error(sprintf(_AM_SYSTEM_USERS_MAIL_ERROR, htmlspecialchars(Request::getEmail('email'), ENT_QUOTES)));
                xoops_cp_footer();
            } else {
                $edituser->setVar('name', Request::getString('name'));
                $edituser->setVar('uname', Request::getString('user_uname'));
                $edituser->setVar('email', Request::getEmail('email'));
                $url = formatURL(Request::getUrl('url'));
                $edituser->setVar('url', $url);
                $edituser->setVar('user_icq', Request::getString('user_icq'));
                $edituser->setVar('user_from', Request::getString('user_from'));
                $edituser->setVar('user_sig', Request::getString('user_sig'));
                $user_viewemail = (Request::hasVar('user_viewemail') && Request::getInt('user_viewemail') == 1) ? 1 : 0;
                $edituser->setVar('user_viewemail', $user_viewemail);
                $edituser->setVar('user_aim', Request::getString('user_aim'));
                $edituser->setVar('user_yim', Request::getString('user_yim'));
                $edituser->setVar('user_msnm', Request::getString('user_msnm'));
                $attachsig = (Request::hasVar('attachsig') && Request::getInt('attachsig') == 1) ? 1 : 0;
                $edituser->setVar('attachsig', $attachsig);
                $edituser->setVar('timezone_offset', Request::getString('timezone_offset'));
                $edituser->setVar('uorder', Request::getString('uorder'));
                $edituser->setVar('umode', Request::getString('umode'));
                // RMV-NOTIFY
                $edituser->setVar('notify_method', Request::getString('notify_method'));
                $edituser->setVar('notify_mode', Request::getString('notify_mode'));
                $edituser->setVar('bio', Request::getString('bio'));
                $edituser->setVar('rank', Request::getString('rank'));
                $edituser->setVar('user_occ', Request::getString('user_occ'));
                $edituser->setVar('user_intrest', Request::getString('user_intrest'));
                $edituser->setVar('user_mailok', Request::getString('user_mailok'));
                if ('' !== Request::getString('pass2d')) {
                    if (Request::getString('password') != Request::getString('pass2')) {
                        xoops_cp_header();
                        echo '
                        <strong>' . _AM_SYSTEM_USERS_STNPDNM . '</strong>';
                        xoops_cp_footer();
                        exit();
                    }
                    $edituser->setVar('pass', password_hash(Request::getString('password'), PASSWORD_DEFAULT));
                }
                if (!$member_handler->insertUser($edituser)) {
                    xoops_cp_header();
                    echo $edituser->getHtmlErrors();
                    xoops_cp_footer();
                } else {
                    $groups = Request::getArray('groups', array());
                    if (!empty($groups)) {
                        global $xoopsUser;
                        $oldgroups = $edituser->getGroups();
                        //If the edited user is the current user and the current user WAS in the webmaster's group and is NOT in the new groups array
                        if ($edituser->getVar('uid') == $xoopsUser->getVar('uid') && in_array(XOOPS_GROUP_ADMIN, $oldgroups) && !in_array(XOOPS_GROUP_ADMIN, $groups)) {
                            //Add the webmaster's group to the groups array to prevent accidentally removing oneself from the webmaster's group
                            $groups[] = XOOPS_GROUP_ADMIN;
                            $_REQUEST['groups'] = $groups;  // Update the global variable
                        }
                         /** @var XoopsMemberHandler $member_handler */
                        $member_handler = xoops_getHandler('member');
                        foreach ($oldgroups as $groupid) {
                            $member_handler->removeUsersFromGroup($groupid, array($edituser->getVar('uid')));
                        }
                        foreach ($groups as $groupid) {
                            $member_handler->addUserToGroup($groupid, $edituser->getVar('uid'));
                        }
                    }
                    redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_DBUPDATED);
                }
            }
            exit();
        } else {
            //Add user
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('admin.php?fct=users', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if (!Request::getString('username') || !Request::getString('email') || !Request::getString('password')) {
                $adduser_errormsg = _AM_SYSTEM_USERS_YMCACF;
            } else {
                /* @var XoopsMemberHandler $member_handler */
                $member_handler = xoops_getHandler('member');
                // make sure the username doesnt exist yet
                if ($member_handler->getUserCount(new Criteria('uname', $myts->addSlashes(Request::getString('username')))) > 0) {
                    $adduser_errormsg = 'User name ' . htmlspecialchars(Request::getString('username'), ENT_QUOTES) . ' already exists';
                } else {
                    $newuser = $member_handler->createUser();
                    if (isset($user_viewemail)) {
                        $newuser->setVar('user_viewemail', Request::getString('user_viewemail'));
                    }
                    if (isset($attachsig)) {
                        $newuser->setVar('attachsig', Request::getString('attachsig'));
                    }
                    $newuser->setVar('name', Request::getString('name'));
                    $newuser->setVar('uname', Request::getString('user_uname'));
                    $newuser->setVar('email', Request::getEmail('email'));
                    $newuser->setVar('url', formatURL(Request::getUrl('url')));
                    $newuser->setVar('user_avatar', 'avatars/blank.gif');
                    $newuser->setVar('user_regdate', time());
                    $newuser->setVar('user_icq', Request::getString('user_icq'));
                    $newuser->setVar('user_from', Request::getString('user_from'));
                    $newuser->setVar('user_sig', Request::getString('user_sig'));
                    $newuser->setVar('user_aim', Request::getString('user_aim'));
                    $newuser->setVar('user_yim', Request::getString('user_yim'));
                    $newuser->setVar('user_msnm', Request::getString('user_msnm'));
                    if ('' !== Request::getString('pass2d')) {
                        if (Request::getString('password') != Request::getString('pass2')) {
                            xoops_cp_header();
                            echo '<strong>' . _AM_SYSTEM_USERS_STNPDNM . '</strong>';
                            xoops_cp_footer();
                            exit();
                        }
                        $newuser->setVar('pass', password_hash(Request::getString('password'), PASSWORD_DEFAULT));
                    }
                    $newuser->setVar('timezone_offset', Request::getString('timezone_offset'));
                    $newuser->setVar('uorder', Request::getString('uorder'));
                    $newuser->setVar('umode', Request::getString('umode'));
                    // RMV-NOTIFY
                    $newuser->setVar('notify_method', Request::getString('notify_method'));
                    $newuser->setVar('notify_mode', Request::getString('notify_mode'));
                    $newuser->setVar('bio', Request::getString('bio'));
                    $newuser->setVar('rank', Request::getString('rank'));
                    $newuser->setVar('level', 1);
                    $newuser->setVar('user_occ', Request::getString('user_occ'));
                    $newuser->setVar('user_intrest', Request::getString('user_intrest'));
                    $newuser->setVar('user_mailok', Request::getString('user_mailok'));
                    if (!$member_handler->insertUser($newuser)) {
                        $adduser_errormsg = _AM_SYSTEM_USERS_CNRNU;
                    } else {
                        $groups_failed = array();
                        $groups = Request::getArray('groups', array());
                        if (!empty($groups)) {
                            foreach ($groups as $group) {
                            $group = (int)$group;
                            if (!$member_handler->addUserToGroup($group, $newuser->getVar('uid'))) {
                                $groups_failed[] = $group;
                            }
                        }
                        }
                        if (!empty($groups_failed)) {
                            $group_names      = $member_handler->getGroupList(new Criteria('groupid', '(' . implode(', ', $groups_failed) . ')', 'IN'));
                            $adduser_errormsg = sprintf(_AM_SYSTEM_USERS_CNRNU2, implode(', ', $group_names));
                        } else {
                            xoops_load('XoopsUserUtility');
                            XoopsUserUtility::sendWelcome($newuser);
                            redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_DBUPDATED);
                        }
                    }
                }
            }
            xoops_error($adduser_errormsg);
        }
        break;

    // Activ member
    case 'users_active':
        if (Request::hasVar('uid')) {
            $obj = $member_handler->getUser($uid);
            //echo $_REQUEST["uid"];
            //print_r($obj);
        }
        $obj->setVar('level', 1);
        if ($member_handler->insertUser($obj, true)) {
            redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_DBUPDATED);
        }
        echo $obj->getHtmlErrors();
        break;

    // Synchronize
    case 'users_synchronize':
        if (Request::hasVar('status') && Request::getString('status') == 1) {
            synchronize($uid, 'user');
        } elseif (Request::hasVar('status') && Request::getString('status')== 2) {
            synchronize('', 'all users');
        }
        redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_DBUPDATED);
        break;

    default:
        // Search and Display
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        //table sorting does not work with select boxes
        //$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.tablesorter.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        //Recherche approfondie

        if (Request::hasVar('complet_search')) {
            // Assign Breadcrumb menu
            $xoBreadCrumb->addLink(_AM_SYSTEM_USERS_NAV_ADVANCED_SEARCH);
            $xoBreadCrumb->addHelp(system_adminVersion('users', 'help'));
            $xoBreadCrumb->addTips(_AM_SYSTEM_USERS_NAV_TIPS);
            $xoBreadCrumb->render();

            $acttotal   = $member_handler->getUserCount(new Criteria('level', 0, '>'));
            $inacttotal = $member_handler->getUserCount(new Criteria('level', 0));

            //$group_select = new XoopsFormSelectGroup(_AM_SYSTEM_USERS_GROUPS, "selgroups", null, false, 1, false);
            $group_select = new XoopsFormSelect(_AM_SYSTEM_USERS_GROUPS, 'selgroups');
            /** @var XoopsGroupHandler $group_handler */
            $group_handler = xoops_getHandler('group');
            $group_arr     = $group_handler->getObjects();
            $group_select->addOption('', '--------------');
            foreach (array_keys($group_arr) as $i) {
                if ($group_arr[$i]->getVar('groupid') != XOOPS_GROUP_ANONYMOUS) {
                    $group_select->addOption('' . $group_arr[$i]->getVar('groupid') . '', '' . $group_arr[$i]->getVar('name') . '');
                }
            }

            $uname_text  = new XoopsFormText('', 'user_uname', 30, 60);
            $uname_match = new XoopsFormSelectMatchOption('', 'user_uname_match');
            $uname_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_UNAME, '&nbsp;');
            $uname_tray->addElement($uname_match);
            $uname_tray->addElement($uname_text);
            $name_text  = new XoopsFormText('', 'user_name', 30, 60);
            $name_match = new XoopsFormSelectMatchOption('', 'user_name_match');
            $name_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_REALNAME, '&nbsp;');
            $name_tray->addElement($name_match);
            $name_tray->addElement($name_text);
            $email_text  = new XoopsFormText('', 'user_email', 30, 60);
            $email_match = new XoopsFormSelectMatchOption('', 'user_email_match');
            $email_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_EMAIL, '&nbsp;');
            $email_tray->addElement($email_match);
            $email_tray->addElement($email_text);
            $url_text  = new XoopsFormText(_AM_SYSTEM_USERS_URLC, 'user_url', 30, 100);
            $icq_text  = new XoopsFormText('', 'user_icq', 30, 100);
            $icq_match = new XoopsFormSelectMatchOption('', 'user_icq_match');
            $icq_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_ICQ, '&nbsp;');
            $icq_tray->addElement($icq_match);
            $icq_tray->addElement($icq_text);
            $aim_text  = new XoopsFormText('', 'user_aim', 30, 100);
            $aim_match = new XoopsFormSelectMatchOption('', 'user_aim_match');
            $aim_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_AIM, '&nbsp;');
            $aim_tray->addElement($aim_match);
            $aim_tray->addElement($aim_text);
            $yim_text  = new XoopsFormText('', 'user_yim', 30, 100);
            $yim_match = new XoopsFormSelectMatchOption('', 'user_yim_match');
            $yim_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_YIM, '&nbsp;');
            $yim_tray->addElement($yim_match);
            $yim_tray->addElement($yim_text);
            $msnm_text  = new XoopsFormText('', 'user_msnm', 30, 100);
            $msnm_match = new XoopsFormSelectMatchOption('', 'user_msnm_match');
            $msnm_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_MSNM, '&nbsp;');
            $msnm_tray->addElement($msnm_match);
            $msnm_tray->addElement($msnm_text);
            $location_text   = new XoopsFormText(_AM_SYSTEM_USERS_LOCATIONC, 'user_from', 30, 100);
            $occupation_text = new XoopsFormText(_AM_SYSTEM_USERS_OCCUPATIONC, 'user_occ', 30, 100);
            $interest_text   = new XoopsFormText(_AM_SYSTEM_USERS_INTERESTC, 'user_intrest', 30, 100);

            $lastlog_more = new XoopsFormText(_AM_SYSTEM_USERS_LASTLOGMORE, 'user_lastlog_more', 10, 5);
            $lastlog_less = new XoopsFormText(_AM_SYSTEM_USERS_LASTLOGLESS, 'user_lastlog_less', 10, 5);
            $reg_more     = new XoopsFormText(_AM_SYSTEM_USERS_REGMORE, 'user_reg_more', 10, 5);
            $reg_less     = new XoopsFormText(_AM_SYSTEM_USERS_REGLESS, 'user_reg_less', 10, 5);
            $posts_more   = new XoopsFormText(_AM_SYSTEM_USERS_POSTSMORE, 'user_posts_more', 10, 5);
            $posts_less   = new XoopsFormText(_AM_SYSTEM_USERS_POSTSLESS, 'user_posts_less', 10, 5);
            $mailok_radio = new XoopsFormRadio(_AM_SYSTEM_USERS_SHOWMAILOK, 'user_mailok', 'both');
            $mailok_radio->addOptionArray(array(
                                              'mailok' => _AM_SYSTEM_USERS_MAILOK,
                                              'mailng' => _AM_SYSTEM_USERS_MAILNG,
                                              'both' => _AM_SYSTEM_USERS_BOTH));
            $type_radio = new XoopsFormRadio(_AM_SYSTEM_USERS_SHOWTYPE, 'user_type', 'actv');
            $type_radio->addOptionArray(array(
                                            'actv' => _AM_SYSTEM_USERS_ACTIVE,
                                            'inactv' => _AM_SYSTEM_USERS_INACTIVE,
                                            'both' => _AM_SYSTEM_USERS_BOTH));
            $sort_select = new XoopsFormSelect(_AM_SYSTEM_USERS_SORT, 'user_sort');
            $sort_select->addOptionArray(array(
                                             'uname' => _AM_SYSTEM_USERS_UNAME,
                                             'email' => _AM_SYSTEM_USERS_EMAIL,
                                             'last_login' => _AM_SYSTEM_USERS_LASTLOGIN,
                                             'user_regdate' => _AM_SYSTEM_USERS_REGDATE,
                                             'posts' => _AM_SYSTEM_USERS_POSTS));
            $order_select = new XoopsFormSelect(_AM_SYSTEM_USERS_ORDER, 'user_order');
            $order_select->addOptionArray(array('ASC' => _AM_SYSTEM_USERS_ASC, 'DESC' => _AM_SYSTEM_USERS_DESC));
            $limit_text    = new XoopsFormText(_AM_SYSTEM_USERS_LIMIT, 'user_limit', 6, 2, 20);
            $submit_button = new XoopsFormButton('', 'user_submit', _SUBMIT, 'submit');

            $form = new XoopsThemeForm(_AM_SYSTEM_USERS_FINDUS, 'user_findform', 'admin.php?fct=users', 'post', true);
            $form->addElement($uname_tray);
            $form->addElement($name_tray);
            $form->addElement($email_tray);
            $form->addElement($group_select);
            $form->addElement($icq_tray);
            $form->addElement($aim_tray);
            $form->addElement($yim_tray);
            $form->addElement($msnm_tray);
            $form->addElement($url_text);
            $form->addElement($location_text);
            $form->addElement($occupation_text);
            $form->addElement($interest_text);
            $form->addElement($lastlog_more);
            $form->addElement($lastlog_less);
            $form->addElement($reg_more);
            $form->addElement($reg_less);
            $form->addElement($posts_more);
            $form->addElement($posts_less);
            $form->addElement($mailok_radio);
            $form->addElement($type_radio);
            $form->addElement($sort_select);
            $form->addElement($order_select);
            //$form->addElement($fct_hidden);
            $form->addElement($limit_text);
            //$form->addElement($op_hidden);

            // if this is to find users for a specific group
            if (!empty($_GET['group']) && Request::getInt('group', 0, 'GET') > 0) {
                $group_hidden = new XoopsFormHidden('group', Request::getInt('group', 0, 'GET') );
                $form->addElement($group_hidden);
            }
            $form->addElement($submit_button);
            $form->display();
        } else {
            //Display data
            // Assign Breadcrumb menu
            $xoBreadCrumb->addHelp(system_adminVersion('users', 'help'));
            $xoBreadCrumb->addTips(_AM_SYSTEM_USERS_NAV_TIPS);
            $xoBreadCrumb->render();

            $requete_search  = '<br><br><strong>See search request: </strong><br><br>';
            $requete_pagenav = '';

            $user_uname = Request::getString('user_uname');
            $user_uname_match = Request::getInt('user_uname_match', 0);

                       $criteria = new CriteriaCompo();
            if (!empty($user_uname)) {
                $match = (!empty($user_uname_match)) ? $user_uname_match: XOOPS_MATCH_START;
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria('uname', $myts->addSlashes($user_uname) . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria('uname', '%' . $myts->addSlashes($user_uname), 'LIKE'));
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria('uname', $myts->addSlashes($user_uname)));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria('uname', '%' . $myts->addSlashes($user_uname) . '%', 'LIKE'));
                        break;
                }
                $requete_pagenav .= '&amp;user_uname=' . htmlspecialchars($user_uname, ENT_QUOTES) . '&amp;user_uname_match=' . htmlspecialchars($user_uname_match, ENT_QUOTES);
                $requete_search .= 'uname : ' . $user_uname . ' et user_uname_match=' . $user_uname_match . '<br>';
            }
            $user_name = Request::getString('user_name');
            $user_name_match = Request::getInt('user_name_match', 0);
            if (!empty($user_name)) {
                $match = Request::getString('user_name_match', XOOPS_MATCH_START);
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria('name', $myts->addSlashes($user_name) . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria('name', '%' . $myts->addSlashes($user_name)), 'LIKE');
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria('name', $myts->addSlashes($user_name)));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria('name', '%' . $myts->addSlashes(Request::getString('user_name', '', 'POST')) . '%', 'LIKE'));
                        break;
                }
                $requete_pagenav .= '&amp;user_name=' . htmlspecialchars($user_name, ENT_QUOTES) . '&amp;user_name_match=' . htmlspecialchars(Request::getString('user_name_match'), ENT_QUOTES);
                $requete_search .= 'name : ' . $user_name . ' et user_name_match=' . $user_name_match . '<br>';
            }
            $user_email = Request::getString('user_email');
            $user_email_match = Request::getInt('user_email_match', 0);
            if (!empty($user_email)) {
                $match = Request::getString('user_email_match', XOOPS_MATCH_START);
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria('email', $myts->addSlashes($user_email) . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria('email', '%' . $myts->addSlashes($user_email), 'LIKE'));
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria('email', $myts->addSlashes($user_email)));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria('email', '%' . $myts->addSlashes($user_email) . '%', 'LIKE'));
                        break;
                }
                $requete_pagenav .= '&amp;user_email=' . htmlspecialchars($user_email, ENT_QUOTES) . '&amp;user_email_match=' . htmlspecialchars($user_email_match, ENT_QUOTES);
                $requete_search .= 'email : ' . $user_email . ' et user_email_match=' . $user_email_match . '<br>';
            }
            $user_url = Request::getString('user_url');
            $user_url_match = Request::getInt('user_url_match', 0);
            if (Request::hasVar('user_url')) {
                $url = formatURL(Request::getUrl('user_url'));
                $criteria->add(new Criteria('url', '%' . $myts->addSlashes($url) . '%', 'LIKE'));
                $requete_pagenav .= '&amp;user_url=' . htmlspecialchars(Request::getString('user_url'), ENT_QUOTES);
                $requete_search .= 'url : ' . Request::getString('user_url') . '<br>';
            }
            $user_icq = Request::getString('user_icq');
            $user_icq_match = Request::getString('user_icq_match');
            if (!empty($user_icq)) {
                $match = Request::getString('user_icq_match', XOOPS_MATCH_START);
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria('user_icq', $myts->addSlashes($user_icq) . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria('user_icq', '%' . $myts->addSlashes($user_icq), 'LIKE'));
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria('user_icq', $myts->addSlashes($user_icq)));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria('user_icq', '%' . $myts->addSlashes($user_icq) . '%', 'LIKE'));
                        break;
                }
                $requete_pagenav .= '&amp;user_icq=' . htmlspecialchars($user_icq, ENT_QUOTES) . '&amp;user_icq_match=' . htmlspecialchars($user_icq_match, ENT_QUOTES);
                $requete_search .= 'icq : ' . $user_icq . ' et user_icq_match=' . $user_icq_match . '<br>';
            }

            $user_aim = Request::getString('user_aim');
            $user_aim_match = Request::getString('user_aim_match');
            if (!empty($user_aim)) {
                $match = Request::getString('user_aim_match', XOOPS_MATCH_START);
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria('user_aim', $myts->addSlashes($user_aim) . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria('user_aim', '%' . $myts->addSlashes($user_aim), 'LIKE'));
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria('user_aim', $myts->addSlashes($user_aim)));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria('user_aim', '%' . $myts->addSlashes($user_aim) . '%', 'LIKE'));
                        break;
                }
                $requete_pagenav .= '&amp;user_aim=' . htmlspecialchars($user_aim, ENT_QUOTES) . '&amp;user_aim_match=' . htmlspecialchars($user_aim_match, ENT_QUOTES);
                $requete_search .= 'aim : ' . $user_aim . ' et user_aim_match=' . $user_aim_match . '<br>';
            }
            $user_yim = Request::getString('user_yim');
            $user_yim_match = Request::getString('user_yim_match');
            if (!empty($user_yim)) {
                $match = Request::getString('user_yim_match', XOOPS_MATCH_START);
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria('user_yim', $myts->addSlashes(Request::getString('user_yim')) . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria('user_yim', '%' . $myts->addSlashes(Request::getString('user_yim')), 'LIKE'));
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria('user_yim', $myts->addSlashes(Request::getString('user_yim'))));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria('user_yim', '%' . $myts->addSlashes(Request::getString('user_yim')) . '%', 'LIKE'));
                        break;
                }
                $requete_pagenav .= '&amp;user_yim=' . htmlspecialchars(Request::getString('user_yim'), ENT_QUOTES) . '&amp;user_yim_match=' . htmlspecialchars(Request::getString('user_yim_match'), ENT_QUOTES);
                $requete_search .= 'yim : ' . Request::getString('user_yim') . ' et user_yim_match=' . Request::getString('user_yim_match') . '<br>';
            }

            $user_msnm = Request::getString('user_msnm');
            $user_msnm_match = Request::getString('user_msnm_match');
            if (!empty($user_msnm)) {
                $match = Request::getString('user_msnm_match', XOOPS_MATCH_START);
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria('user_msnm', $myts->addSlashes($user_msnm) . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria('user_msnm', '%' . $myts->addSlashes($user_msnm), 'LIKE'));
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria('user_msnm', $myts->addSlashes($user_msnm)));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria('user_msnm', '%' . $myts->addSlashes($user_msnm) . '%', 'LIKE'));
                        break;
                }
                $requete_pagenav .= '&amp;user_msnm=' . htmlspecialchars($user_msnm . '&amp;user_msnm_match=' . htmlspecialchars($user_msnm_match, ENT_QUOTES), ENT_QUOTES);
                $requete_search .= 'msn : ' . $user_msnm . ' et user_msnm_match=' . $user_msnm_match . '<br>';
            }

            if (Request::hasVar('user_from')) {
                $criteria->add(new Criteria('user_from', '%' . $myts->addSlashes(Request::getString('user_from')) . '%', 'LIKE'));
                $requete_pagenav .= '&amp;user_from=' . htmlspecialchars(Request::getString('user_from'), ENT_QUOTES);
                $requete_search .= 'from : ' . Request::getString('user_from') . '<br>';
            }

            if (Request::hasVar('user_intrest')) {
                $criteria->add(new Criteria('user_intrest', '%' . $myts->addSlashes(Request::getString('user_intrest')) . '%', 'LIKE'));
                $requete_pagenav .= '&amp;user_intrest=' . htmlspecialchars(Request::getString('user_intrest'), ENT_QUOTES);
                $requete_search .= 'interet : ' . Request::getString('user_intrest') . '<br>';
            }

            if (Request::hasVar('user_occ')) {
                $criteria->add(new Criteria('user_occ', '%' . $myts->addSlashes(Request::getString('user_occ')) . '%', 'LIKE'));
                $requete_pagenav .= '&amp;user_occ=' . htmlspecialchars(Request::getString('user_occ'), ENT_QUOTES);
                $requete_search .= 'location : ' . Request::getString('user_occ') . '<br>';
            }

            if (Request::hasVar('user_name_match')) {
                $f_user_lastlog_more = Request::getString('user_name_match', XOOPS_MATCH_START);
                $time                = time() - (60 * 60 * 24 * $f_user_lastlog_more);
                if ($time > 0) {
                    $criteria->add(new Criteria('last_login', $time, '<'));
                }
                $requete_pagenav .= '&amp;user_lastlog_more=' . htmlspecialchars(Request::getString('user_lastlog_more'), ENT_QUOTES);
                $requete_search .= 'derniere connexion apres : ' . Request::getString('user_lastlog_more') . '<br>';
            }

            if (Request::hasVar('user_name_match')) {
                $f_user_lastlog_less = Request::getString('user_name_match', XOOPS_MATCH_START);
                $time                = time() - (60 * 60 * 24 * $f_user_lastlog_less);
                if ($time > 0) {
                    $criteria->add(new Criteria('last_login', $time, '>'));
                }
                $requete_pagenav .= '&amp;user_lastlog_less=' . htmlspecialchars(Request::getString('user_lastlog_less'), ENT_QUOTES);
                $requete_search .= 'derniere connexion avant : ' . Request::getString('user_lastlog_less') . '<br>';
            }

            if (Request::hasVar('user_reg_more') && is_numeric(Request::getString('user_reg_more'))) {
                $f_user_reg_more = (int)Request::getString('user_reg_more');
                $time            = time() - (60 * 60 * 24 * $f_user_reg_more);
                if ($time > 0) {
                    $criteria->add(new Criteria('user_regdate', $time, '<'));
                }
                $requete_pagenav .= '&amp;user_regdate=' . htmlspecialchars(Request::getString('user_regdate'), ENT_QUOTES);
                $requete_search .= 'enregistre apres : ' . Request::getString('user_reg_more') . '<br>';
            }


            if (Request::hasVar('user_reg_less') && is_numeric(Request::getString('user_reg_less'))) {
                $f_user_reg_less = (int)Request::getString('user_reg_less');
                $time            = time() - (60 * 60 * 24 * $f_user_reg_less);
                if ($time > 0) {
                    $criteria->add(new Criteria('user_regdate', $time, '>'));
                }
                $requete_pagenav .= '&amp;user_reg_less=' . htmlspecialchars(Request::getString('user_reg_less'), ENT_QUOTES);
                $requete_search .= 'enregistre avant : ' . Request::getString('user_reg_less') . '<br>';
            }

            if (Request::hasVar('user_posts_more') && is_numeric(Request::getString('user_posts_more'))) {
                $criteria->add(new Criteria('posts', (int)Request::getString('user_posts_more'), '>'));
                $requete_pagenav .= '&amp;user_posts_more=' . htmlspecialchars(Request::getString('user_posts_more'), ENT_QUOTES);
                $requete_search .= 'posts plus de : ' . Request::getString('user_posts_more') . '<br>';
            }

            if (Request::hasVar('user_posts_less') && is_numeric(Request::getString('user_posts_less'))) {
                $criteria->add(new Criteria('posts', (int)Request::getString('user_posts_less'), '<'));
                $requete_pagenav .= '&amp;user_posts_less=' . htmlspecialchars(Request::getString('user_posts_less'), ENT_QUOTES);
                $requete_search .= 'post moins de : ' . Request::getString('user_posts_less') . '<br>';
            }

            if (Request::hasVar('user_mailok')) {
                if (Request::getString('user_mailok') === 'mailng') {
                    $criteria->add(new Criteria('user_mailok', 0));
                } elseif (Request::getString('user_mailok') === 'mailok') {
                    $criteria->add(new Criteria('user_mailok', 1));
                } else {
                    $criteria->add(new Criteria('user_mailok', 0, '>='));
                }
                $requete_pagenav .= '&amp;user_mailok=' . htmlspecialchars(Request::getString('user_mailok'), ENT_QUOTES);
                $requete_search .= 'accept email : ' . Request::getString('user_mailok') . '<br>';
            }

            if (Request::hasVar('user_type')) {
                if (Request::getString('user_type') === 'inactv') {
                    $criteria->add(new Criteria('level', 0, '='));
                    $user_type = 'inactv';
                    $requete_search .= 'actif ou inactif : inactif<br>';
                } elseif (Request::getString('user_type') === 'actv') {
                    $criteria->add(new Criteria('level', 0, '>'));
                    $user_type = 'actv';
                    $requete_search .= 'actif ou inactif : actif<br>';
                }
                $requete_pagenav .= '&amp;user_type=' . htmlspecialchars(Request::getString('user_type'), ENT_QUOTES);
            } else {
                $criteria->add(new Criteria('level', 0, '>='));
                $user_type = '';
                $requete_search .= 'actif ou inactif : admin et user<br>';
            }

            $validsort = array('uname', 'email', 'last_login', 'user_regdate', 'posts');
            if (Request::hasVar('user_sort')) {
                $userSort = Request::getString('user_sort');
                $sort = (!in_array($userSort, $validsort)) ? 'uid' : $userSort;
                $requete_pagenav .= '&amp;user_sort=' . htmlspecialchars($userSort, ENT_QUOTES);
                $requete_search .= 'order by : ' . $sort . '<br>';
            } else {
                $sort = 'uid';
                $requete_pagenav .= '&amp;user_sort=uid';
                $requete_search .= 'order by : ' . $sort . '<br>';
            }

            $order = 'DESC';
            if (Request::hasVar('user_order') && Request::getString('user_order') === 'ASC') {
                $requete_pagenav .= '&amp;user_order=ASC';
                $requete_search .= 'tris : ' . $order . '<br>';
            } else {
                //$order = "ASC";
                $requete_pagenav .= '&amp;user_order=DESC';
                $requete_search .= 'tris : ' . $order . '<br>';
            }

            $user_limit = (int)xoops_getModuleOption('users_pager', 'system');
            if (Request::hasVar('user_limit')) {
                $user_limit = Request::getInt('user_limit');
                $requete_pagenav .= '&amp;user_limit=' . htmlspecialchars(Request::getString('user_limit'), ENT_QUOTES);
                $requete_search .= 'limit : ' . $user_limit . '<br>';
            } else {
                $requete_pagenav .= '&amp;user_limit=' . xoops_getModuleOption('users_pager', 'system');
                $requete_search .= 'limit : ' . $user_limit . '<br>';
            }

            $start = Request::getInt('start');
                $groups = array();
            if (Request::hasVar('selgroups')) {
                $selgroups = Request::getArray('selgroups', array()); // Default to an empty array if 'selgroups' is not set
                if (empty($selgroups)) {
                    // If 'selgroups' is an empty array, try to get it as an integer
                    $selgroupsInt = Request::getInt('selgroups', 0);
                    if ($selgroupsInt != 0) {
                        $groups = array($selgroupsInt);
                }
            } else {
                    $groups = array_map('intval', $selgroups);
                }
                $requete_pagenav .= '&amp;selgroups=' . htmlspecialchars(implode(',', $selgroups), ENT_QUOTES);
            }
            //print_r($groups);
            /** @var XoopsMemberHandler $member_handler */
            $member_handler = xoops_getHandler('member');

            if (empty($groups)) {
                $users_count = $member_handler->getUserCount($criteria);
            } else {
                $users_count = $member_handler->getUserCountByGroupLink($groups, $criteria);
            }
            if ($start < $users_count) {
                echo sprintf(_AM_SYSTEM_USERS_USERSFOUND, $users_count) . '<br>';
                $criteria->setSort($sort);
                $criteria->setOrder($order);
                $criteria->setLimit($user_limit);
                $criteria->setStart($start);
                $users_arr = $member_handler->getUsersByGroupLink($groups, $criteria, true);
                $ucount    = 0;
            }

            $xoopsTpl->assign('users_count', $users_count);
            $xoopsTpl->assign('users_display', true);

            //User limit
            $user_limit = Request::getInt('user_limit',  20);
            //User type
            $user_type = Request::getString('user_type');
            //selgroups
            $selgroups = Request::getString('selgroups'); //TODO should it be an array?
            $user_uname = Request::getString('user_uname');

            //Form tris
            $form          = '<form action="admin.php?fct=users" method="post">
                    ' . _AM_SYSTEM_USERS_SEARCH_USER . '<input type="text" name="user_uname" value="' . $myts->htmlSpecialChars($user_uname) . '" size="15">
                    <select name="selgroups">
                        <option value="" selected>' . _AM_SYSTEM_USERS_ALLGROUP . '</option>';
            /* @var XoopsGroupHandler $group_handler */
            $group_handler = xoops_getHandler('group');
            $group_arr     = $group_handler->getObjects();
            foreach (array_keys($group_arr) as $i) {
                if ($group_arr[$i]->getVar('groupid') != XOOPS_GROUP_ANONYMOUS) {
                    $form .= '<option value="' . $group_arr[$i]->getVar('groupid') . '"  ' . ($selgroups == $group_arr[$i]->getVar('groupid') ? ' selected' : '') . '>' . $group_arr[$i]->getVar('name') . '</option>';
                }
            }
            $form .= '</select>&nbsp;
                <select name="user_type">
                    <option value="" ' . ($user_type === '' ? ' selected' : '') . '>' . _AM_SYSTEM_USERS_ALLUSER . '</option>
                    <option value="actv" ' . ($user_type === 'actv' ? ' selected' : '') . '>' . _AM_SYSTEM_USERS_ACTIVEUSER . '</option>
                    <option value="inactv" ' . ($user_type === 'inactv' ? ' selected' : '') . '>' . _AM_SYSTEM_USERS_INACTIVEUSER . '</option>
                </select>&nbsp;
                <select name="user_limit">
                    <option value="20" ' . ($user_limit == 20 ? ' selected' : '') . '>20</option>
                    <option value="50" ' . ($user_limit == 50 ? ' selected' : '') . '>50</option>
                    <option value="100" ' . ($user_limit == 100 ? ' selected' : '') . '>100</option>
                </select>&nbsp;
                <input type="hidden" name="user_uname_match" value="XOOPS_MATCH_START" />
                <input type="submit" value="' . _AM_SYSTEM_USERS_SEARCH . '" name="speed_search">&nbsp;
                <input type="submit" value="' . _AM_SYSTEM_USERS_ADVANCED_SEARCH . '" name="complet_search"></form>
                ';

            //select groupe
            $form_select_groups = '<select  name="selgroups" id="selgroups"   style="display:none;"><option value="">---------</option>';
            //$module_array[0] = _AM_SYSTEM_USERS_COMMENTS_FORM_ALL_MODS;
            $group_handler = xoops_getHandler('group');
            $group_arr     = $group_handler->getObjects();
            foreach (array_keys($group_arr) as $i) {
                if ($group_arr[$i]->getVar('groupid') != XOOPS_GROUP_ANONYMOUS) {
                    $form_select_groups .= '<option value="' . $group_arr[$i]->getVar('groupid') . '"  ' . ($selgroups == $group_arr[$i]->getVar('groupid') ? ' selected' : '') . '>' . $group_arr[$i]->getVar('name') . '</option>';
                }
            }
            $form_select_groups .= '</select><input type="hidden" name="op" value="users_add_delete_group">';

            $xoopsTpl->assign('form_sort', $form);
            $xoopsTpl->assign('form_select_groups', $form_select_groups);

            // add token to render in template
            $tokenElement = new XoopsFormHiddenToken();
            $token = $tokenElement->render();
            $xoopsTpl->assign('form_token', $token);

            //echo $requete_search;
            if ($users_count > 0) {
                //echo $requete_search;
                foreach (array_keys($users_arr) as $i) {
                    //Display group
                    $user_group = $member_handler->getGroupsByUser($users_arr[$i]->getVar('uid'));
                    if (in_array(XOOPS_GROUP_ADMIN, $user_group)) {
                        $users['group'] = system_AdminIcons('xoops/group_1.png');
                        //$users['icon'] = '<img src="'.XOOPS_URL.'/modules/system/images/icons/admin.png" alt="'._AM_SYSTEM_USERS_ADMIN.'" title="'._AM_SYSTEM_USERS_ADMIN.'" />';
                        $users['checkbox_user'] = false;
                    } else {
                        $users['group'] = system_AdminIcons('xoops/group_2.png');
                        //$users['icon'] = '<img src="'.XOOPS_URL.'/modules/system/images/icons/user.png" alt="'._AM_SYSTEM_USERS_USER.'" title="'._AM_SYSTEM_USERS_USER.'" />';
                        $users['checkbox_user'] = true;
                    }
                    $users['uid']         = $users_arr[$i]->getVar('uid');
                    $users['name']        = $users_arr[$i]->getVar('name');
                    $users['uname']       = $users_arr[$i]->getVar('uname');
                    $users['email']       = $users_arr[$i]->getVar('email');
                    $users['url']         = $users_arr[$i]->getVar('url');
                    $users['user_avatar'] = ($users_arr[$i]->getVar('user_avatar') === 'blank.gif') ? system_AdminIcons('anonymous.png') : XOOPS_URL . '/uploads/' . $users_arr[$i]->getVar('user_avatar');
                    $users['reg_date']    = formatTimestamp($users_arr[$i]->getVar('user_regdate'), 'm');
                    if ($users_arr[$i]->getVar('last_login') > 0) {
                        $users['last_login'] = formatTimestamp($users_arr[$i]->getVar('last_login'), 'm');
                    } else {
                        $users['last_login'] = _AM_SYSTEM_USERS_NOT_CONNECT;
                    }
                    $users['user_level'] = $users_arr[$i]->getVar('level');
                    $users['user_icq']   = $users_arr[$i]->getVar('user_icq');
                    $users['user_aim']   = $users_arr[$i]->getVar('user_aim');
                    $users['user_yim']   = $users_arr[$i]->getVar('user_yim');
                    $users['user_msnm']  = $users_arr[$i]->getVar('user_msnm');

                    $users['posts'] = $users_arr[$i]->getVar('posts');

                    $xoopsTpl->appendByRef('users', $users);
                    $xoopsTpl->appendByRef('users_popup', $users);
                    unset($users);
                }
            } else {
                $xoopsTpl->assign('users_no_found', true);
            }

            if ($users_count > $user_limit) {
                include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
                $nav = new XoopsPageNav($users_count, $user_limit, $start, 'start', 'fct=users&amp;op=default' . $requete_pagenav);
                $xoopsTpl->assign('nav', $nav->renderNav());
            }
        }
        break;
}
// Call Footer
xoops_cp_footer();
