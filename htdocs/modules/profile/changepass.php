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
if (!$GLOBALS['xoopsUser']) {
    redirect_header(XOOPS_URL, 2, _NOPERM);
}
$GLOBALS['xoopsOption']['template_main'] = 'profile_changepass.tpl';
include $GLOBALS['xoops']->path('header.php');

if (!isset($_POST['submit'])) {
    //show change password form
    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
    $form = new XoopsThemeForm(_PROFILE_MA_CHANGEPASSWORD, 'form', $_SERVER['REQUEST_URI'], 'post', true);
    $form->addElement(new XoopsFormPassword(_PROFILE_MA_OLDPASSWORD, 'oldpass', 15, 50), true);
    $form->addElement(new XoopsFormPassword(_PROFILE_MA_NEWPASSWORD, 'newpass', 15, 50), true);
    $form->addElement(new XoopsFormPassword(_US_VERIFYPASS, 'vpass', 15, 50), true);
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->assign($GLOBALS['xoopsTpl']);

    $xoBreadcrumbs[] = array('title' => _PROFILE_MA_CHANGEPASSWORD);
} else {
    /* @var $config_handler XoopsConfigHandler  */
    $config_handler             = xoops_getHandler('config');
    $GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
    $myts                       = MyTextSanitizer::getInstance();
    $oldpass                    = @$myts->stripSlashesGPC(trim($_POST['oldpass']));
    $password                   = @$myts->stripSlashesGPC(trim($_POST['newpass']));
    $vpass                      = @$myts->stripSlashesGPC(trim($_POST['vpass']));
    $errors                     = array();
    if (!password_verify($oldpass, $GLOBALS['xoopsUser']->getVar('pass', 'n'))) {
        $errors[] = _PROFILE_MA_WRONGPASSWORD;
    }
    if (strlen($password) < $GLOBALS['xoopsConfigUser']['minpass']) {
        $errors[] = sprintf(_US_PWDTOOSHORT, $GLOBALS['xoopsConfigUser']['minpass']);
    }
    if ($password != $vpass) {
        $errors[] = _US_PASSNOTSAME;
    }

    if ($errors) {
        $msg = implode('<br>', $errors);
    } else {
        //update password
        $GLOBALS['xoopsUser']->setVar('pass', password_hash($password, PASSWORD_DEFAULT));
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $msg = _PROFILE_MA_ERRORDURINGSAVE;
        if ($member_handler->insertUser($GLOBALS['xoopsUser'])) {
            $msg = _PROFILE_MA_PASSWORDCHANGED;
        }
    }
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/userinfo.php?uid=' . $GLOBALS['xoopsUser']->getVar('uid'), 2, $msg);
}

include __DIR__ . '/footer.php';
