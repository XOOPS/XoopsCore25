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
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}

//  Check is active
if (!xoops_getModuleOption('active_users', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

/*********************************************************/
/* Users Functions                                       */
/*********************************************************/
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

//Display form add or edit
//true = add and false = edit
/**
 * @param        $add_or_edit
 * @param string $user
 */
function form_user($add_or_edit, $user = '')
{
    global $xoopsConfig, $xoopsUser;
    $uid = system_CleanVars($_REQUEST, 'uid', 0);

    //RMV-NOTIFY
    include_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/notification.php';
    include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';

    if ($add_or_edit === true) {
        //Add user
        $uid_value        = '';
        $uname_value      = '';
        $name_value       = '';
        $email_value      = '';
        $email_cbox_value = 0;
        $url_value        = '';
        $timezone_value   = $xoopsConfig['default_TZ'];
        $icq_value        = '';
        $aim_value        = '';
        $yim_value        = '';
        $msnm_value       = '';
        $location_value   = '';
        $occ_value        = '';
        $interest_value   = '';
        $sig_value        = '';
        $sig_cbox_value   = 0;
        $umode_value      = $xoopsConfig['com_mode'];
        $uorder_value     = $xoopsConfig['com_order'];
        // RMV-NOTIFY
        $notify_method_value = XOOPS_NOTIFICATION_METHOD_PM;
        $notify_mode_value   = XOOPS_NOTIFICATION_MODE_SENDALWAYS;
        $bio_value           = '';
        $rank_value          = 0;
        $mailok_value        = 0;
        $form_title          = _AM_SYSTEM_USERS_ADDUSER;
        $form_isedit         = false;
        $groups              = array(XOOPS_GROUP_USERS);
    } else {
        //Edit user
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $user           = $member_handler->getUser($uid);
        if (is_object($user)) {
            $uid_value        = $uid;
            $uname_value      = $user->getVar('uname', 'E');
            $name_value       = $user->getVar('name', 'E');
            $email_value      = $user->getVar('email', 'E');
            $email_cbox_value = $user->getVar('user_viewemail') ? 1 : 0;
            $url_value        = $user->getVar('url', 'E');
            $temp             = $user->getVar('theme');
            $timezone_value   = $user->getVar('timezone_offset');
            $icq_value        = $user->getVar('user_icq', 'E');
            $aim_value        = $user->getVar('user_aim', 'E');
            $yim_value        = $user->getVar('user_yim', 'E');
            $msnm_value       = $user->getVar('user_msnm', 'E');
            $location_value   = $user->getVar('user_from', 'E');
            $occ_value        = $user->getVar('user_occ', 'E');
            $interest_value   = $user->getVar('user_intrest', 'E');
            $sig_value        = $user->getVar('user_sig', 'E');
            $sig_cbox_value   = ($user->getVar('attachsig') == 1) ? 1 : 0;
            $umode_value      = $user->getVar('umode');
            $uorder_value     = $user->getVar('uorder');
            // RMV-NOTIFY
            $notify_method_value = $user->getVar('notify_method');
            $notify_mode_value   = $user->getVar('notify_mode');
            $bio_value           = $user->getVar('bio', 'E');
            $rank_value          = $user->rank(false);
            $mailok_value        = $user->getVar('user_mailok', 'E');
            $form_title          = _AM_SYSTEM_USERS_UPDATEUSER . ': ' . $user->getVar('uname');
            $form_isedit         = true;
            $groups              = array_values($user->getGroups());
        }
    }

    //Affichage du formulaire
    $form = new XoopsThemeForm($form_title, 'form_user', 'admin.php', 'post', true);

    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_NICKNAME, 'username', 25, 25, $uname_value), true);
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_NAME, 'name', 30, 60, $name_value));
    $email_tray = new XoopsFormElementTray(_AM_SYSTEM_USERS_EMAIL, '<br>');
    $email_text = new XoopsFormText('', 'email', 30, 60, $email_value);
    $email_tray->addElement($email_text, true);
    $email_cbox = new XoopsFormCheckBox('', 'user_viewemail', $email_cbox_value);
    $email_cbox->addOption(1, _AM_SYSTEM_USERS_AOUTVTEAD);
    $email_tray->addElement($email_cbox);
    $form->addElement($email_tray, true);
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_URL, 'url', 30, 100, $url_value));
    $form->addElement(new XoopsFormSelectTimezone(_AM_SYSTEM_USERS_TIMEZONE, 'timezone_offset', $timezone_value));
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_ICQ, 'user_icq', 15, 15, $icq_value));
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_AIM, 'user_aim', 18, 18, $aim_value));
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_YIM, 'user_yim', 25, 25, $yim_value));
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_MSNM, 'user_msnm', 30, 100, $msnm_value));
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_LOCATION, 'user_from', 30, 100, $location_value));
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_OCCUPATION, 'user_occ', 30, 100, $occ_value));
    $form->addElement(new XoopsFormText(_AM_SYSTEM_USERS_INTEREST, 'user_intrest', 30, 150, $interest_value));
    $sig_tray  = new XoopsFormElementTray(_AM_SYSTEM_USERS_SIGNATURE, '<br>');
    $sig_tarea = new XoopsFormTextArea('', 'user_sig', $sig_value);
    $sig_tray->addElement($sig_tarea);
    $sig_cbox = new XoopsFormCheckBox('', 'attachsig', $sig_cbox_value);
    $sig_cbox->addOption(1, _AM_SYSTEM_USERS_SHOWSIG);
    $sig_tray->addElement($sig_cbox);
    $form->addElement($sig_tray);
    $umode_select = new XoopsFormSelect(_AM_SYSTEM_USERS_CDISPLAYMODE, 'umode', $umode_value);
    $umode_select->addOptionArray(array('nest' => _NESTED, 'flat' => _FLAT, 'thread' => _THREADED));
    $form->addElement($umode_select);
    $uorder_select = new XoopsFormSelect(_AM_SYSTEM_USERS_CSORTORDER, 'uorder', $uorder_value);
    $uorder_select->addOptionArray(array('0' => _OLDESTFIRST, '1' => _NEWESTFIRST));
    $form->addElement($uorder_select);
    // RMV-NOTIFY
    $notify_method_select = new XoopsFormSelect(_NOT_NOTIFYMETHOD, 'notify_method', $notify_method_value);
    $notify_method_select->addOptionArray(array(
                                              XOOPS_NOTIFICATION_METHOD_DISABLE => _NOT_METHOD_DISABLE,
                                              XOOPS_NOTIFICATION_METHOD_PM      => _NOT_METHOD_PM,
                                              XOOPS_NOTIFICATION_METHOD_EMAIL   => _NOT_METHOD_EMAIL));
    $form->addElement($notify_method_select);
    $notify_mode_select = new XoopsFormSelect(_NOT_NOTIFYMODE, 'notify_mode', $notify_mode_value);
    $notify_mode_select->addOptionArray(array(
                                            XOOPS_NOTIFICATION_MODE_SENDALWAYS         => _NOT_MODE_SENDALWAYS,
                                            XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE => _NOT_MODE_SENDONCE,
                                            XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT   => _NOT_MODE_SENDONCEPERLOGIN));
    $form->addElement($notify_mode_select);
    $form->addElement(new XoopsFormTextArea(_AM_SYSTEM_USERS_EXTRAINFO, 'bio', $bio_value));
    $rank_select = new XoopsFormSelect(_AM_SYSTEM_USERS_RANK, 'rank', $rank_value);
    $ranklist    = XoopsLists::getUserRankList();
    if (count($ranklist) > 0) {
        $rank_select->addOption(0, '--------------');
        $rank_select->addOptionArray($ranklist);
    } else {
        $rank_select->addOption(0, _AM_SYSTEM_USERS_NSRID);
    }
    $form->addElement($rank_select);
    // adding a new user requires password fields
    if (!$form_isedit) {
        $form->addElement(new XoopsFormPassword(_AM_SYSTEM_USERS_PASSWORD, 'password', 10, 32), true);
        $form->addElement(new XoopsFormPassword(_AM_SYSTEM_USERS_RETYPEPD, 'pass2', 10, 32), true);
    } else {
        $form->addElement(new XoopsFormPassword(_AM_SYSTEM_USERS_PASSWORD, 'password', 10, 32));
        $form->addElement(new XoopsFormPassword(_AM_SYSTEM_USERS_RETYPEPD, 'pass2', 10, 32));
    }
    $form->addElement(new XoopsFormRadioYN(_AM_SYSTEM_USERS_ACCEPT_EMAIL, 'user_mailok', $mailok_value));

    //Groups administration addition XOOPS 2.0.9: Mith
    /* @var  $gperm_handler XoopsGroupPermHandler */
    $gperm_handler = xoops_getHandler('groupperm');
    //If user has admin rights on groups
    if ($gperm_handler->checkRight('system_admin', XOOPS_SYSTEM_GROUP, $xoopsUser->getGroups(), 1)) {
        //add group selection
        $group_select[] = new XoopsFormSelectGroup(_AM_SYSTEM_USERS_GROUPS, 'groups', false, $groups, 5, true);
    } else {
        //add each user groups
        foreach ($groups as $key => $group) {
            $group_select[] = new XoopsFormHidden('groups[' . $key . ']', $group);
        }
    }
    foreach ($group_select as $group) {
        $form->addElement($group);
        unset($group);
    }

    $form->addElement(new XoopsFormHidden('fct', 'users'));
    $form->addElement(new XoopsFormHidden('op', 'users_save'));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

    if (!empty($uid_value)) {
        $form->addElement(new XoopsFormHidden('uid', $uid_value));
    }
    $form->display();
}

/**
 * @param $uid
 * @param $type
 */
function synchronize($uid, $type)
{
    global $xoopsDB;

    include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
    include_once XOOPS_ROOT_PATH . '/kernel/module.php';

    $tables = array();
    // Count comments (approved only: com_status == XOOPS_COMMENT_ACTIVE)
    $tables[] = array('table_name' => 'xoopscomments', 'uid_column' => 'com_uid', 'criteria' => new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
    // Count Content posts
    if (XoopsModule::getByDirname('fmcontent')) {
        $tables[] = array('table_name' => 'fmcontent_content', 'uid_column' => 'content_uid');
    }
    // Count forum posts
    if (XoopsModule::getByDirname('newbb')) {
        $tables[] = array('table_name' => 'bb_posts', 'uid_column' => 'uid');
    }

    switch ($type) {
        case 'user':
            $total_posts = 0;
            foreach ($tables as $table) {
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria($table['uid_column'], $uid));
                if (!empty($table['criteria'])) {
                    $criteria->add($table['criteria']);
                }
                $sql = 'SELECT COUNT(*) AS total FROM ' . $xoopsDB->prefix($table['table_name']) . ' ' . $criteria->renderWhere();
                if ($result = $xoopsDB->query($sql)) {
                    if ($row = $xoopsDB->fetchArray($result)) {
                        $total_posts += $row['total'];
                    }
                }
            }
            $sql = 'UPDATE ' . $xoopsDB->prefix('users') . " SET posts = '" . $total_posts . "' WHERE uid = '" . $uid . "'";
            if (!$result = $xoopsDB->queryF($sql)) {
                redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_USERS_CNUUSER);
            }
            break;

        case 'all users':
            $sql = 'SELECT uid FROM ' . $xoopsDB->prefix('users') . '';
            if (!$result = $xoopsDB->query($sql)) {
                redirect_header('admin.php?fct=users', 1, sprintf(_AM_SYSTEM_USERS_CNGUSERID, $uid));
            }

            while (false !== ($data = $xoopsDB->fetchArray($result))) {
                synchronize($data['uid'], 'user');
            }
            break;
    }

    // exit();
}
