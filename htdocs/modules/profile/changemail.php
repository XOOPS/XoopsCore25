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
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

$xoopsOption['pagetype'] = 'user';
include __DIR__ . '/header.php';
/* @var $config_handler XoopsConfigHandler  */
$config_handler             = xoops_getHandler('config');
$GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);

if (!$GLOBALS['xoopsUser'] || $GLOBALS['xoopsConfigUser']['allow_chgmail'] != 1) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/', 2, _NOPERM);
}

$GLOBALS['xoopsOption']['template_main'] = 'profile_email.tpl';
include $GLOBALS['xoops']->path('header.php');

if (!isset($_POST['submit']) || !isset($_POST['passwd'])) {
    //show change password form
    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
    $form = new XoopsThemeForm(_PROFILE_MA_CHANGEMAIL, 'emailform', $_SERVER['REQUEST_URI'], 'post', true);
    $form->addElement(new XoopsFormPassword(_US_PASSWORD, 'passwd', 15, 50), true);
    $form->addElement(new XoopsFormText(_PROFILE_MA_NEWMAIL, 'newmail', 15, 50), true);
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->assign($GLOBALS['xoopsTpl']);
} else {
    $myts   = MyTextSanitizer::getInstance();
    $pass   = @$myts->stripSlashesGPC(trim($_POST['passwd']));
    $email  = @$myts->stripSlashesGPC(trim($_POST['newmail']));
    $errors = array();
    if (!password_verify($oldpass, $GLOBALS['xoopsUser']->getVar('pass', 'n'))) {
        $errors[] = _PROFILE_MA_WRONGPASSWORD;
    }
    if (!checkEmail($email)) {
        $errors[] = _US_INVALIDMAIL;
    }

    if ($errors) {
        $msg = implode('<br>', $errros);
    } else {
        //update password
        $GLOBALS['xoopsUser']->setVar('email', trim($_POST['newmail']));
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        if ($member_handler->insertUser($GLOBALS['xoopsUser'])) {
            $msg = _PROFILE_MA_EMAILCHANGED;

            //send email to new email address
            $xoopsMailer =& xoops_getMailer();
            $xoopsMailer->useMail();
            $xoopsMailer->setTemplateDir($GLOBALS['xoopsModule']->getVar('dirname', 'n'));
            $xoopsMailer->setTemplate('emailchanged.tpl');
            $xoopsMailer->assign('SITENAME', $GLOBALS['xoopsConfig']['sitename']);
            $xoopsMailer->assign('ADMINMAIL', $GLOBALS['xoopsConfig']['adminmail']);
            $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
            $xoopsMailer->assign('NEWEMAIL', $email);
            $xoopsMailer->setToEmails($email);
            $xoopsMailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
            $xoopsMailer->setFromName($GLOBALS['xoopsConfig']['sitename']);
            $xoopsMailer->setSubject(sprintf(_PROFILE_MA_NEWEMAIL, $GLOBALS['xoopsConfig']['sitename']));
            $xoopsMailer->send();
        } else {
            $msg = implode('<br>', $GLOBALS['xoopsUser']->getErrors());
        }
    }
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/userinfo.php?uid=' . $GLOBALS['xoopsUser']->getVar('uid'), 2, $msg);
}

$xoBreadcrumbs[] = array('title' => _PROFILE_MA_CHANGEMAIL);

include __DIR__ . '/footer.php';
