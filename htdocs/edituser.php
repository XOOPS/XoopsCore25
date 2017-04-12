<?php
/**
 *  Xoops Edit User
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
 * @since               2.0.0
 */

include __DIR__ . '/mainfile.php';

XoopsLoad::load('XoopsRequest');

$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.edituser.start');

xoops_loadLanguage('user');
include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

// If not a user, redirect
if (!is_object($xoopsUser)) {
    redirect_header('index.php', 3, _US_NOEDITRIGHT);
}

// initialize $op variable
$op = XoopsRequest::getCmd('op', 'editprofile');
/* @var $config_handler XoopsConfigHandler  */
$config_handler  = xoops_getHandler('config');
$xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
$myts            = MyTextSanitizer::getInstance();
if ($op === 'saveuser') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT . '<br>' . implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $uid = XoopsRequest::getInt('uid', 0);
    if (empty($uid) || $xoopsUser->getVar('uid') != $uid) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT);
    }
    $errors = array();
    if ($xoopsConfigUser['allow_chgmail'] == 1) {
        $email = XoopsRequest::getEmail('email', '');
        if (empty($email)) {
            $errors[] = _US_INVALIDMAIL;
        }
    }
    $password = XoopsRequest::getString('password', '');
    if (!empty($password)) {
        if (strlen($password) < $xoopsConfigUser['minpass']) {
            $errors[] = sprintf(_US_PWDTOOSHORT, $xoopsConfigUser['minpass']);
        } else {
            $vpass = XoopsRequest::getString('vpass', '');
            if ($password != $vpass) {
                $errors[] = _US_PASSNOTSAME;
            }
        }
    }
    if (count($errors) > 0) {
        include $GLOBALS['xoops']->path('header.php');
        echo '<div>';
        foreach ($errors as $er) {
            echo '<span class="red bold">' . $er . '</span><br>';
        }
        echo '</div><br>';
        $op = 'editprofile';
    } else {
        $member_handler = xoops_getHandler('member');
        $edituser       = $member_handler->getUser($uid);
        $edituser->setVar('name', XoopsRequest::getString('name', ''));
        if ($xoopsConfigUser['allow_chgmail'] == 1) {
            $edituser->setVar('email', $email, true);
        }
        if ($password != '') {
            $edituser->setVar('pass', password_hash($password, PASSWORD_DEFAULT));
            //$edituser->setVar('last_pass_change', time());
        }
        $edituser->setVar('url', XoopsRequest::getUrl('url', ''));
        $edituser->setVar('user_icq', XoopsRequest::getString('user_icq', ''));
        $edituser->setVar('user_from', XoopsRequest::getString('user_from', ''));
        $edituser->setVar('user_sig', xoops_substr(XoopsRequest::getString('user_sig', ''), 0, 255));
        $edituser->setVar('user_viewemail', XoopsRequest::getBool('user_viewemail', 0));
        $edituser->setVar('user_aim', XoopsRequest::getString('user_aim', ''));
        $edituser->setVar('user_yim', XoopsRequest::getString('user_yim', ''));
        $edituser->setVar('user_msnm', XoopsRequest::getString('user_msnm', ''));
        $edituser->setVar('attachsig', XoopsRequest::getBool('attachsig', 0));
        $edituser->setVar('timezone_offset', XoopsRequest::getFloat('timezone_offset', 0.0));
        $edituser->setVar('uorder', XoopsRequest::getInt('uorder', 0));
        $edituser->setVar('umode', XoopsRequest::getString('umode', 'flat'));
        $edituser->setVar('notify_method', XoopsRequest::getInt('notify_method', 1));
        $edituser->setVar('notify_mode', XoopsRequest::getInt('notify_mode', 1));
        $edituser->setVar('bio', substr(XoopsRequest::getString('bio', ''), 0, 255));
        $edituser->setVar('user_occ', XoopsRequest::getString('user_occ', ''));
        $edituser->setVar('user_intrest', XoopsRequest::getString('user_intrest', ''));
        $edituser->setVar('user_mailok', XoopsRequest::getBool('user_mailok', 0));
        if (!$member_handler->insertUser($edituser)) {
            include $GLOBALS['xoops']->path('header.php');
            echo $edituser->getHtmlErrors();
            include $GLOBALS['xoops']->path('footer.php');
        } else {
            redirect_header('userinfo.php?uid=' . $uid, 1, _US_PROFUPDATED);
        }
        exit();
    }
}

if ($op === 'editprofile') {
    include_once $GLOBALS['xoops']->path('header.php');
    include_once $GLOBALS['xoops']->path('include/comment_constants.php');
    include_once $GLOBALS['xoops']->path('include/xoopscodes.php');
    echo '<a href="userinfo.php?uid=' . $xoopsUser->getVar('uid') . '" title="">' . _US_PROFILE . '</a>&nbsp;<span class="bold">&raquo;</span>&nbsp;' . _US_EDITPROFILE . '<br><br>';
    $form        = new XoopsThemeForm(_US_EDITPROFILE, 'userinfo', 'edituser.php', 'post', true);
    $uname_label = new XoopsFormLabel(_US_NICKNAME, $xoopsUser->getVar('uname'));
    $form->addElement($uname_label);
    $name_text = new XoopsFormText(_US_REALNAME, 'name', 30, 60, $xoopsUser->getVar('name', 'E'));
    $form->addElement($name_text);
    $email_tray = new XoopsFormElementTray(_US_EMAIL, '<br>');
    if ($xoopsConfigUser['allow_chgmail'] == 1) {
        $email_text = new XoopsFormText('', 'email', 30, 60, $xoopsUser->getVar('email'));
    } else {
        $email_text = new XoopsFormLabel('', $xoopsUser->getVar('email'));
    }
    $email_tray->addElement($email_text);
    $email_cbox_value = $xoopsUser->user_viewemail() ? 1 : 0;
    $email_cbox       = new XoopsFormCheckBox('', 'user_viewemail', $email_cbox_value);
    $email_cbox->addOption(1, _US_ALLOWVIEWEMAIL);
    $email_tray->addElement($email_cbox);
    $form->addElement($email_tray);
    $url_text = new XoopsFormText(_US_WEBSITE, 'url', 30, 100, $xoopsUser->getVar('url', 'E'));
    $form->addElement($url_text);

    $timezone_select = new XoopsFormSelectTimezone(_US_TIMEZONE, 'timezone_offset', $xoopsUser->getVar('timezone_offset'));
    $icq_text        = new XoopsFormText(_US_ICQ, 'user_icq', 15, 15, $xoopsUser->getVar('user_icq', 'E'));
    $aim_text        = new XoopsFormText(_US_AIM, 'user_aim', 18, 18, $xoopsUser->getVar('user_aim', 'E'));
    $yim_text        = new XoopsFormText(_US_YIM, 'user_yim', 25, 25, $xoopsUser->getVar('user_yim', 'E'));
    $msnm_text       = new XoopsFormText(_US_MSNM, 'user_msnm', 30, 100, $xoopsUser->getVar('user_msnm', 'E'));
    $location_text   = new XoopsFormText(_US_LOCATION, 'user_from', 30, 100, $xoopsUser->getVar('user_from', 'E'));
    $occupation_text = new XoopsFormText(_US_OCCUPATION, 'user_occ', 30, 100, $xoopsUser->getVar('user_occ', 'E'));
    $interest_text   = new XoopsFormText(_US_INTEREST, 'user_intrest', 30, 150, $xoopsUser->getVar('user_intrest', 'E'));
    $sig_tray        = new XoopsFormElementTray(_US_SIGNATURE, '<br>');
    $sig_tarea       = new XoopsFormDhtmlTextArea('', 'user_sig', $xoopsUser->getVar('user_sig', 'E'));
    $sig_tray->addElement($sig_tarea);
    $sig_cbox_value = $xoopsUser->getVar('attachsig') ? 1 : 0;
    $sig_cbox       = new XoopsFormCheckBox('', 'attachsig', $sig_cbox_value);
    $sig_cbox->addOption(1, _US_SHOWSIG);
    $sig_tray->addElement($sig_cbox);
    $umode_select = new XoopsFormSelect(_US_CDISPLAYMODE, 'umode', $xoopsUser->getVar('umode'));
    $umode_select->addOptionArray(array(
                                      'nest'   => _NESTED,
                                      'flat'   => _FLAT,
                                      'thread' => _THREADED));
    $uorder_select = new XoopsFormSelect(_US_CSORTORDER, 'uorder', $xoopsUser->getVar('uorder'));
    $uorder_select->addOptionArray(array(
                                       XOOPS_COMMENT_OLD1ST => _OLDESTFIRST,
                                       XOOPS_COMMENT_NEW1ST => _NEWESTFIRST));
    // RMV-NOTIFY
    // TODO: add this to admin user-edit functions...
    include_once $GLOBALS['xoops']->path('language/' . $xoopsConfig['language'] . '/notification.php');
    include_once $GLOBALS['xoops']->path('include/notification_constants.php');
    $notify_method_select = new XoopsFormSelect(_NOT_NOTIFYMETHOD, 'notify_method', $xoopsUser->getVar('notify_method'));
    $notify_method_select->addOptionArray(array(
                                              XOOPS_NOTIFICATION_METHOD_DISABLE => _NOT_METHOD_DISABLE,
                                              XOOPS_NOTIFICATION_METHOD_PM      => _NOT_METHOD_PM,
                                              XOOPS_NOTIFICATION_METHOD_EMAIL   => _NOT_METHOD_EMAIL));
    $notify_mode_select = new XoopsFormSelect(_NOT_NOTIFYMODE, 'notify_mode', $xoopsUser->getVar('notify_mode'));
    $notify_mode_select->addOptionArray(array(
                                            XOOPS_NOTIFICATION_MODE_SENDALWAYS         => _NOT_MODE_SENDALWAYS,
                                            XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE => _NOT_MODE_SENDONCE,
                                            XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT   => _NOT_MODE_SENDONCEPERLOGIN));
    $bio_tarea          = new XoopsFormTextArea(_US_EXTRAINFO, 'bio', $xoopsUser->getVar('bio', 'E'));
    $pwd_text           = new XoopsFormPassword('', 'password', 10, 32);
    $pwd_text2          = new XoopsFormPassword('', 'vpass', 10, 32);
    $pwd_tray           = new XoopsFormElementTray(_US_PASSWORD . '<br>' . _US_TYPEPASSTWICE);
    $pwd_tray->addElement($pwd_text);
    $pwd_tray->addElement($pwd_text2);
    $mailok_radio  = new XoopsFormRadioYN(_US_MAILOK, 'user_mailok', $xoopsUser->getVar('user_mailok'));
    $uid_hidden    = new XoopsFormHidden('uid', $xoopsUser->getVar('uid'));
    $op_hidden     = new XoopsFormHidden('op', 'saveuser');
    $submit_button = new XoopsFormButton('', 'submit', _US_SAVECHANGES, 'submit');

    $form->addElement($timezone_select);
    $form->addElement($icq_text);
    $form->addElement($aim_text);
    $form->addElement($yim_text);
    $form->addElement($msnm_text);
    $form->addElement($location_text);
    $form->addElement($occupation_text);
    $form->addElement($interest_text);
    $form->addElement($sig_tray);
    $form->addElement($umode_select);
    $form->addElement($uorder_select);
    $form->addElement($notify_method_select);
    $form->addElement($notify_mode_select);
    $form->addElement($bio_tarea);
    $form->addElement($pwd_tray);
    //$form->addElement($cookie_radio);
    $form->addElement($mailok_radio);
    $form->addElement($uid_hidden);
    $form->addElement($op_hidden);
    //$form->addElement($token_hidden);
    $form->addElement($submit_button);
    if ($xoopsConfigUser['allow_chgmail'] == 1) {
        $form->setRequired($email_text);
    }
    $form->display();
    include $GLOBALS['xoops']->path('footer.php');
}

if ($op === 'avatarform') {
    include $GLOBALS['xoops']->path('header.php');
    echo '<a href="userinfo.php?uid=' . $xoopsUser->getVar('uid') . '">' . _US_PROFILE . '</a>&nbsp;<span class="bold">&raquo;</span>&nbsp;' . _US_UPLOADMYAVATAR . '<br><br>';
    $oldavatar = $xoopsUser->getVar('user_avatar');
    if (!empty($oldavatar) && $oldavatar !== 'blank.gif') {
        echo '<div class="pad10 txtcenter floatcenter0"><h4 class="red bold">' . _US_OLDDELETED . '</h4>';
        echo '<img src="' . XOOPS_UPLOAD_URL . '/' . $oldavatar . '" alt="" /></div>';
    }
    if ($xoopsConfigUser['avatar_allow_upload'] == 1 && $xoopsUser->getVar('posts') >= $xoopsConfigUser['avatar_minposts']) {
        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        $form = new XoopsThemeForm(_US_UPLOADMYAVATAR, 'uploadavatar', 'edituser.php', 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement(new XoopsFormLabel(_US_MAXPIXEL, $xoopsConfigUser['avatar_width'] . ' x ' . $xoopsConfigUser['avatar_height']));
        $form->addElement(new XoopsFormLabel(_US_MAXIMGSZ, $xoopsConfigUser['avatar_maxsize']));
        $form->addElement(new XoopsFormFile(_US_SELFILE, 'avatarfile', $xoopsConfigUser['avatar_maxsize']), true);
        $form->addElement(new XoopsFormHidden('op', 'avatarupload'));
        $form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $form->display();
    }
    $avatar_handler  = xoops_getHandler('avatar');
    $form2           = new XoopsThemeForm(_US_CHOOSEAVT, 'uploadavatar', 'edituser.php', 'post', true);
    $avatar_select   = new XoopsFormSelect('', 'user_avatar', $xoopsUser->getVar('user_avatar'));
    $avatar_list     = $avatar_handler->getList('S', true);
    $avatar_selected = $xoopsUser->getVar('user_avatar', 'E');
//    $avatar_selected = in_array($avatar_selected, array_keys($avatar_list)) ? $avatar_selected : "blank.gif";
    $avatar_selected = array_key_exists($avatar_selected, $avatar_list) ? $avatar_selected : 'blank.gif';
    $avatar_select->addOptionArray($avatar_list);
    $avatar_select->setExtra("onchange='showImgSelected(\"avatar\", \"user_avatar\", \"uploads\", \"\", \"" . XOOPS_URL . "\")'");
    $avatar_tray = new XoopsFormElementTray(_US_AVATAR, '&nbsp;');
    $avatar_tray->addElement($avatar_select);
    $avatar_tray->addElement(new XoopsFormLabel('', "<a href=\"javascript:openWithSelfMain('" . XOOPS_URL . "/misc.php?action=showpopups&amp;type=avatars','avatars',600,400);\">" . _LIST . '</a><br>'));
    $avatar_tray->addElement(new XoopsFormLabel('', "<br><img src='" . XOOPS_UPLOAD_URL . '/' . $avatar_selected . "' name='avatar' id='avatar' alt='' />"));
    $form2->addElement($avatar_tray);
    $form2->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form2->addElement(new XoopsFormHidden('op', 'avatarchoose'));
    $form2->addElement(new XoopsFormButton('', 'submit2', _SUBMIT, 'submit'));
    $form2->display();
    include $GLOBALS['xoops']->path('footer.php');
}

if ($op === 'avatarupload') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT . '<br>' . implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $xoops_upload_file = array();
    $uid               = 0;
    if (!empty($_POST['xoops_upload_file']) && is_array($_POST['xoops_upload_file'])) {
        $xoops_upload_file = $_POST['xoops_upload_file'];
    }
    if (!empty($_POST['uid'])) {
        $uid = (int)$_POST['uid'];
    }
    if (empty($uid) || $xoopsUser->getVar('uid') != $uid) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT);
    }
    if ($xoopsConfigUser['avatar_allow_upload'] == 1 && $xoopsUser->getVar('posts') >= $xoopsConfigUser['avatar_minposts']) {
        include_once $GLOBALS['xoops']->path('class/uploader.php');
        $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH . '/avatars', array(
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/x-png',
            'image/png'), $xoopsConfigUser['avatar_maxsize'], $xoopsConfigUser['avatar_width'], $xoopsConfigUser['avatar_height']);
        if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
            $uploader->setPrefix('cavt');
            if ($uploader->upload()) {

                /* @var $avt_handler XoopsAvatarHandler */
                $avt_handler = xoops_getHandler('avatar');
                $avatar      = $avt_handler->create();
                $avatar->setVar('avatar_file', 'avatars/' . $uploader->getSavedFileName());
                $avatar->setVar('avatar_name', $xoopsUser->getVar('uname'));
                $avatar->setVar('avatar_mimetype', $uploader->getMediaType());
                $avatar->setVar('avatar_display', 1);
                $avatar->setVar('avatar_type', 'C');
                if (!$avt_handler->insert($avatar)) {
                    @unlink($uploader->getSavedDestination());
                } else {
                    $oldavatar = $xoopsUser->getVar('user_avatar');
                    if (!empty($oldavatar) && false !== strpos(strtolower($oldavatar), 'cavt')) {
                        $avatars = $avt_handler->getObjects(new Criteria('avatar_file', $oldavatar));
                        if (!empty($avatars) && count($avatars) == 1 && is_object($avatars[0])) {
                            $avt_handler->delete($avatars[0]);
                            $oldavatar_path = realpath(XOOPS_UPLOAD_PATH . '/' . $oldavatar);
                            if (0 === strpos($oldavatar_path, XOOPS_UPLOAD_PATH) && is_file($oldavatar_path)) {
                                unlink($oldavatar_path);
                            }
                        }
                    }
                    $sql = sprintf('UPDATE %s SET user_avatar = %s WHERE uid = %u', $xoopsDB->prefix('users'), $xoopsDB->quoteString('avatars/' . $uploader->getSavedFileName()), $xoopsUser->getVar('uid'));
                    $xoopsDB->query($sql);
                    $avt_handler->addUser($avatar->getVar('avatar_id'), $xoopsUser->getVar('uid'));
                    redirect_header('userinfo.php?t=' . time() . '&amp;uid=' . $xoopsUser->getVar('uid'), 3, _US_PROFUPDATED);
                }
            }
        }
        redirect_header('edituser.php?op=avatarform', 3, $uploader->getErrors());
    }
}

if ($op === 'avatarchoose') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT . '<br>' . implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $uid = 0;
    if (!empty($_POST['uid'])) {
        $uid = (int)$_POST['uid'];
    }
    if (empty($uid) || $xoopsUser->getVar('uid') != $uid) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT);
    }
    $user_avatar = '';
    $avt_handler = xoops_getHandler('avatar');
    if (!empty($_POST['user_avatar'])) {
        $user_avatar     = $myts->addSlashes(trim($_POST['user_avatar']));
        $criteria_avatar = new CriteriaCompo(new Criteria('avatar_file', $user_avatar));
        $criteria_avatar->add(new Criteria('avatar_type', 'S'));
        $avatars = $avt_handler->getObjects($criteria_avatar);
        if (!is_array($avatars) || !count($avatars)) {
            $user_avatar = 'avatars/blank.gif';
        }
        unset($avatars, $criteria_avatar);
    }
    $user_avatarpath = realpath(XOOPS_UPLOAD_PATH . '/' . $user_avatar);
    if (0 === strpos($user_avatarpath, realpath(XOOPS_UPLOAD_PATH)) && is_file($user_avatarpath)) {
        $oldavatar = $xoopsUser->getVar('user_avatar');
        $xoopsUser->setVar('user_avatar', $user_avatar);
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        if (!$member_handler->insertUser($xoopsUser)) {
            include $GLOBALS['xoops']->path('header.php');
            echo $xoopsUser->getHtmlErrors();
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }
//        if ($oldavatar && preg_match("/^cavt/", strtolower(substr($oldavatar, 8)))) {
        if ($oldavatar && 0 === strpos(strtolower(substr($oldavatar, 8)), 'cavt')) {
            $avatars = $avt_handler->getObjects(new Criteria('avatar_file', $oldavatar));
            if (!empty($avatars) && count($avatars) == 1 && is_object($avatars[0])) {
                $avt_handler->delete($avatars[0]);
                $oldavatar_path = realpath(XOOPS_UPLOAD_PATH . '/' . $oldavatar);
                if (0 === strpos($oldavatar_path, realpath(XOOPS_UPLOAD_PATH)) && is_file($oldavatar_path)) {
                    unlink($oldavatar_path);
                }
            }
        }
        if ($user_avatar !== 'avatars/blank.gif') {
            $avatars = $avt_handler->getObjects(new Criteria('avatar_file', $user_avatar));
            if (is_object($avatars[0])) {
                $avt_handler->addUser($avatars[0]->getVar('avatar_id'), $xoopsUser->getVar('uid'));
            }
        }
    }
    redirect_header('userinfo.php?uid=' . $uid, 0, _US_PROFUPDATED);
}
