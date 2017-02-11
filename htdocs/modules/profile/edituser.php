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

$xoopsOption['pagetype'] = 'user';
include __DIR__ . '/header.php';
include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

// If not a user, redirect
if (!is_object($GLOBALS['xoopsUser'])) {
    redirect_header(XOOPS_URL, 3, _US_NOEDITRIGHT);
}

$myts                       = MyTextSanitizer::getInstance();
$op                         = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'editprofile';
/* @var $config_handler XoopsConfigHandler  */
$config_handler             = xoops_getHandler('config');
$GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);

if ($op === 'save') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/', 3, _US_NOEDITRIGHT . '<br>' . implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit();
    }
    $uid      = $GLOBALS['xoopsUser']->getVar('uid');
    $errors   = array();
    $edituser =& $GLOBALS['xoopsUser'];
    if ($GLOBALS['xoopsUser']->isAdmin()) {
        $edituser->setVar('uname', trim($_POST['uname']));
        $edituser->setVar('email', trim($_POST['email']));
    }
    xoops_load('XoopsUserUtility');
    $stop = XoopsUserUtility::validate($edituser);

    if (!empty($stop)) {
        $op = 'editprofile';
    } else {

        // Dynamic fields
        $profile_handler = xoops_getModuleHandler('profile');
        // Get fields
        $fields = $profile_handler->loadFields();
        // Get ids of fields that can be edited
        /* @var  $gperm_handler XoopsGroupPermHandler */
        $gperm_handler   = xoops_getHandler('groupperm');
        $editable_fields = $gperm_handler->getItemIds('profile_edit', $GLOBALS['xoopsUser']->getGroups(), $GLOBALS['xoopsModule']->getVar('mid'));

        if (!$profile = $profile_handler->get($edituser->getVar('uid'))) {
            $profile = $profile_handler->create();
            $profile->setVar('profile_id', $edituser->getVar('uid'));
        }

        foreach (array_keys($fields) as $i) {
            $fieldname = $fields[$i]->getVar('field_name');
            if (in_array($fields[$i]->getVar('field_id'), $editable_fields) && isset($_REQUEST[$fieldname])) {
                $value = $fields[$i]->getValueForSave($_REQUEST[$fieldname]);
                if (in_array($fieldname, $profile_handler->getUserVars())) {
                    $edituser->setVar($fieldname, $value);
                } else {
                    $profile->setVar($fieldname, $value);
                }
            }
        }
        if (!$member_handler->insertUser($edituser)) {
            $stop = $edituser->getHtmlErrors();
            $op   = 'editprofile';
        } else {
            $profile->setVar('profile_id', $edituser->getVar('uid'));
            $profile_handler->insert($profile);
            unset($_SESSION['xoopsUserTheme']);
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/userinfo.php?uid=' . $edituser->getVar('uid'), 2, _US_PROFUPDATED);
        }
    }
}

if ($op === 'editprofile') {
    $GLOBALS['xoopsOption']['template_main'] = 'profile_editprofile.tpl';
    include_once $GLOBALS['xoops']->path('header.php');
    include_once __DIR__ . '/include/forms.php';
    $form = profile_getUserForm($GLOBALS['xoopsUser']);
    $form->assign($GLOBALS['xoopsTpl']);
    if (!empty($stop)) {
        $GLOBALS['xoopsTpl']->assign('stop', $stop);
    }

    $xoBreadcrumbs[] = array('title' => _US_EDITPROFILE);
}

if ($op === 'avatarform') {
    $GLOBALS['xoopsOption']['template_main'] = 'profile_avatar.tpl';
    include $GLOBALS['xoops']->path('header.php');
    $xoBreadcrumbs[] = array('title' => _US_MYAVATAR);

    $oldavatar = $GLOBALS['xoopsUser']->getVar('user_avatar');
    if (!empty($oldavatar) && $oldavatar !== 'blank.gif') {
        $GLOBALS['xoopsTpl']->assign('old_avatar', XOOPS_UPLOAD_URL . '/' . $oldavatar);
    }
    if ($GLOBALS['xoopsConfigUser']['avatar_allow_upload'] == 1 && $GLOBALS['xoopsUser']->getVar('posts') >= $GLOBALS['xoopsConfigUser']['avatar_minposts']) {
        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        $form = new XoopsThemeForm(_US_UPLOADMYAVATAR, 'uploadavatar', XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/edituser.php', 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement(new XoopsFormLabel(_US_MAXPIXEL, $GLOBALS['xoopsConfigUser']['avatar_width'] . ' x ' . $GLOBALS['xoopsConfigUser']['avatar_height']));
        $form->addElement(new XoopsFormLabel(_US_MAXIMGSZ, $GLOBALS['xoopsConfigUser']['avatar_maxsize']));
        $form->addElement(new XoopsFormFile(_US_SELFILE, 'avatarfile', $GLOBALS['xoopsConfigUser']['avatar_maxsize']), true);
        $form->addElement(new XoopsFormHidden('op', 'avatarupload'));
        $form->addElement(new XoopsFormHidden('uid', $GLOBALS['xoopsUser']->getVar('uid')));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $form->assign($GLOBALS['xoopsTpl']);
    }
    $avatar_handler  = xoops_getHandler('avatar');
    $form2           = new XoopsThemeForm(_US_CHOOSEAVT, 'chooseavatar', XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/edituser.php', 'post', true);
    $avatar_select   = new XoopsFormSelect('', 'user_avatar', $GLOBALS['xoopsUser']->getVar('user_avatar'));
    $avatar_list     = $avatar_handler->getList('S', true);
    $avatar_selected = $GLOBALS['xoopsUser']->getVar('user_avatar', 'E');
    //    $avatar_selected = in_array($avatar_selected, array_keys($avatar_list)) ? $avatar_selected : "blank.gif";
    $avatar_selected = array_key_exists($avatar_selected, $avatar_list) ? $avatar_selected : 'blank.gif';
    $avatar_select->addOptionArray($avatar_list);
    $avatar_select->setExtra("onchange='showImgSelected(\"avatar\", \"user_avatar\", \"uploads\", \"\", \"" . XOOPS_URL . "\")'");
    $avatar_tray = new XoopsFormElementTray(_US_AVATAR, '&nbsp;');
    $avatar_tray->addElement($avatar_select);
    $avatar_tray->addElement(new XoopsFormLabel('', "<a href=\"javascript:openWithSelfMain('" . XOOPS_URL . "/misc.php?action=showpopups&amp;type=avatars','avatars',600,400);\">" . _LIST . '</a><br>'));
    $avatar_tray->addElement(new XoopsFormLabel('', "<br><img src='" . XOOPS_UPLOAD_URL . '/' . $avatar_selected . "' name='avatar' id='avatar' alt='' />"));
    $form2->addElement($avatar_tray);
    $form2->addElement(new XoopsFormHidden('uid', $GLOBALS['xoopsUser']->getVar('uid')));
    $form2->addElement(new XoopsFormHidden('op', 'avatarchoose'));
    $form2->addElement(new XoopsFormButton('', 'submit2', _SUBMIT, 'submit'));
    $form2->assign($GLOBALS['xoopsTpl']);
}

if ($op === 'avatarupload') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT . '<br>' . implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }
    $xoops_upload_file = array();
    $uid               = 0;
    if (!empty($_POST['xoops_upload_file']) && is_array($_POST['xoops_upload_file'])) {
        $xoops_upload_file = $_POST['xoops_upload_file'];
    }
    if (!empty($_POST['uid'])) {
        $uid = (int)$_POST['uid'];
    }
    if (empty($uid) || $GLOBALS['xoopsUser']->getVar('uid') != $uid) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT);
    }
    if ($GLOBALS['xoopsConfigUser']['avatar_allow_upload'] == 1 && $GLOBALS['xoopsUser']->getVar('posts') >= $GLOBALS['xoopsConfigUser']['avatar_minposts']) {
        include_once $GLOBALS['xoops']->path('class/uploader.php');
        $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH . '/avatars', array(
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/x-png',
            'image/png'), $GLOBALS['xoopsConfigUser']['avatar_maxsize'], $GLOBALS['xoopsConfigUser']['avatar_width'], $GLOBALS['xoopsConfigUser']['avatar_height']);
        if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
            $uploader->setPrefix('cavt');
            if ($uploader->upload()) {
                /* @var $avt_handler XoopsAvatarHandler */
                $avt_handler = xoops_getHandler('avatar');
                $avatar      = $avt_handler->create();
                $avatar->setVar('avatar_file', 'avatars/' . $uploader->getSavedFileName());
                $avatar->setVar('avatar_name', $GLOBALS['xoopsUser']->getVar('uname'));
                $avatar->setVar('avatar_mimetype', $uploader->getMediaType());
                $avatar->setVar('avatar_display', 1);
                $avatar->setVar('avatar_type', 'C');
                if (!$avt_handler->insert($avatar)) {
                    @unlink($uploader->getSavedDestination());
                } else {
                    $oldavatar = $GLOBALS['xoopsUser']->getVar('user_avatar');
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
                    $sql = sprintf('UPDATE %s SET user_avatar = %s WHERE uid = %u', $GLOBALS['xoopsDB']->prefix('users'), $GLOBALS['xoopsDB']->quoteString('avatars/' . $uploader->getSavedFileName()), $GLOBALS['xoopsUser']->getVar('uid'));
                    $GLOBALS['xoopsDB']->query($sql);
                    $avt_handler->addUser($avatar->getVar('avatar_id'), $GLOBALS['xoopsUser']->getVar('uid'));
                    redirect_header('userinfo.php?t=' . time() . '&amp;uid=' . $GLOBALS['xoopsUser']->getVar('uid'), 3, _US_PROFUPDATED);
                }
            }
        }
        redirect_header('edituser.php?op=avatarform', 3, $uploader->getErrors());
    }
}

if ($op === 'avatarchoose') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('index.php', 3, _US_NOEDITRIGHT . '<br>' . implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }
    $uid = 0;
    if (!empty($_POST['uid'])) {
        $uid = (int)$_POST['uid'];
    }
    if (empty($uid) || $GLOBALS['xoopsUser']->getVar('uid') != $uid) {
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
        $oldavatar = $GLOBALS['xoopsUser']->getVar('user_avatar');
        $GLOBALS['xoopsUser']->setVar('user_avatar', $user_avatar);
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        if (!$member_handler->insertUser($GLOBALS['xoopsUser'])) {
            include $GLOBALS['xoops']->path('header.php');
            echo $GLOBALS['xoopsUser']->getHtmlErrors();
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
                $avt_handler->addUser($avatars[0]->getVar('avatar_id'), $GLOBALS['xoopsUser']->getVar('uid'));
            }
        }
    }
    redirect_header('userinfo.php?uid=' . $uid, 0, _US_PROFUPDATED);
}
include __DIR__ . '/footer.php';
