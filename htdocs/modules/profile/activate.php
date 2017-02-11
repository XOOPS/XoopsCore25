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

include $GLOBALS['xoops']->path('header.php');
if (!empty($_GET['id']) && !empty($_GET['actkey'])) {
    $id     = (int)$_GET['id'];
    $actkey = trim($_GET['actkey']);
    if (empty($id)) {
        redirect_header(XOOPS_URL, 1, '');
    }
    /* @var $member_handler XoopsMemberHandler */
    $member_handler = xoops_getHandler('member');
    $thisuser       = $member_handler->getUser($id);
    if (!is_object($thisuser)) {
        redirect_header(XOOPS_URL, 1, '');
    }
    if ($thisuser->getVar('actkey') != $actkey) {
        redirect_header(XOOPS_URL . '/', 5, _US_ACTKEYNOT);
    } else {
        if ($thisuser->getVar('level') > 0) {
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/index.php', 5, _US_ACONTACT, false);
        } else {
            if (false !== $member_handler->activateUser($thisuser)) {
                $xoopsPreload = XoopsPreload::getInstance();
                $xoopsPreload->triggerEvent('core.behavior.user.activate', $thisuser);
                /* @var $config_handler XoopsConfigHandler  */
                $config_handler             = xoops_getHandler('config');
                $GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
                if ($GLOBALS['xoopsConfigUser']['activation_type'] == 2) {
                    $myts        = MyTextSanitizer::getInstance();
                    $xoopsMailer =& xoops_getMailer();
                    $xoopsMailer->useMail();
                    $xoopsMailer->setTemplate('activated.tpl');
                    $xoopsMailer->assign('SITENAME', $GLOBALS['xoopsConfig']['sitename']);
                    $xoopsMailer->assign('ADMINMAIL', $GLOBALS['xoopsConfig']['adminmail']);
                    $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                    $xoopsMailer->setToUsers($thisuser);
                    $xoopsMailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
                    $xoopsMailer->setFromName($GLOBALS['xoopsConfig']['sitename']);
                    $xoopsMailer->setSubject(sprintf(_US_YOURACCOUNT, $GLOBALS['xoopsConfig']['sitename']));
                    include $GLOBALS['xoops']->path('header.php');
                    if (!$xoopsMailer->send()) {
                        printf(_US_ACTVMAILNG, $thisuser->getVar('uname'));
                    } else {
                        printf(_US_ACTVMAILOK, $thisuser->getVar('uname'));
                    }
                    include __DIR__ . '/footer.php';
                } else {
                    redirect_header(XOOPS_URL . '/user.php', 5, _US_ACTLOGIN, false);
                }
            } else {
                redirect_header(XOOPS_URL . '/index.php', 5, 'Activation failed!');
            }
        }
    }
    // Not implemented yet: re-send activiation code
} elseif (!empty($_REQUEST['email']) && $xoopsConfigUser['activation_type'] != 0) {
    $myts           = MyTextSanitizer::getInstance();
    /* @var $member_handler XoopsMemberHandler */
    $member_handler = xoops_getHandler('member');
    $getuser        = $member_handler->getUsers(new Criteria('email', $myts->addSlashes(trim($_REQUEST['email']))));
    if (count($getuser) == 0) {
        redirect_header(XOOPS_URL, 2, _US_SORRYNOTFOUND);
    }
    if ($getuser[0]->isActive()) {
        redirect_header(XOOPS_URL, 2, sprintf(_US_USERALREADYACTIVE, $getuser[0]->getVar('email')));
    }
    $xoopsMailer =& xoops_getMailer();
    $xoopsMailer->useMail();
    $xoopsMailer->setTemplate('register.tpl');
    $xoopsMailer->assign('SITENAME', $GLOBALS['xoopsConfig']['sitename']);
    $xoopsMailer->assign('ADMINMAIL', $GLOBALS['xoopsConfig']['adminmail']);
    $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
    $xoopsMailer->setToUsers($getuser[0]);
    $xoopsMailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
    $xoopsMailer->setFromName($GLOBALS['xoopsConfig']['sitename']);
    $xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $getuser[0]->getVar('uname')));
    if (!$xoopsMailer->send()) {
        echo _US_YOURREGMAILNG;
    } else {
        echo _US_YOURREGISTERED;
    }
} else {
    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
    $form = new XoopsThemeForm('', 'form', 'activate.php');
    $form->addElement(new XoopsFormText(_US_EMAIL, 'email', 25, 255));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->display();
}

$xoBreadcrumbs[] = array('title' => _PROFILE_MA_REGISTER);
include __DIR__ . '/footer.php';
