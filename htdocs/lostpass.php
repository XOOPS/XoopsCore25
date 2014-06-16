<?php
/**
 * XOOPS password recovery
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         core
 * @since           2.0.0
 * @version         $Id$
 */

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mainfile.php';

$xoopsPreload =& XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.lostpass.start');

xoops_loadLanguage('user');

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : $email;

if ($email == '') {
    redirect_header("user.php", 2, _US_SORRYNOTFOUND);
    exit();
}

$myts =& MyTextSanitizer::getInstance();
$member_handler =& xoops_gethandler('member');
$getuser =& $member_handler->getUsers(new Criteria('email', $myts->addSlashes($email)));

if (empty($getuser)) {
    $msg = _US_SORRYNOTFOUND;
    redirect_header("user.php", 2, $msg);
    exit();
} else {
    $code = isset($_GET['code']) ? trim($_GET['code']) : '';
    $areyou = substr($getuser[0]->getVar("pass"), 0, 5);
    if ($code != '' && $areyou == $code) {
        $newpass = xoops_makepass();
        $xoopsMailer =& xoops_getMailer();
        $xoopsMailer->useMail();
        $xoopsMailer->setTemplate("lostpass2.tpl");
        $xoopsMailer->assign("SITENAME", $xoopsConfig['sitename']);
        $xoopsMailer->assign("ADMINMAIL", $xoopsConfig['adminmail']);
        $xoopsMailer->assign("SITEURL", XOOPS_URL . "/");
        $xoopsMailer->assign("IP", $_SERVER['REMOTE_ADDR']);
        $xoopsMailer->assign("NEWPWD", $newpass);
        $xoopsMailer->setToUsers($getuser[0]);
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject(sprintf(_US_NEWPWDREQ, XOOPS_URL));
        if (! $xoopsMailer->send()) {
            echo $xoopsMailer->getErrors();
        }
        // Next step: add the new password to the database
        $sql = sprintf("UPDATE %s SET pass = '%s' WHERE uid = %u", $xoopsDB->prefix("users"), md5($newpass), $getuser[0]->getVar('uid'));
        if (!$xoopsDB->queryF($sql)) {
            include $GLOBALS['xoops']->path('header.php');
            echo _US_MAILPWDNG;
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }
        redirect_header("user.php", 3, sprintf(_US_PWDMAILED, $getuser[0]->getVar("uname")), false);
        exit();
        // If no Code, send it
    } else {
        $xoopsMailer =& xoops_getMailer();
        $xoopsMailer->useMail();
        $xoopsMailer->setTemplate("lostpass1.tpl");
        $xoopsMailer->assign("SITENAME", $xoopsConfig['sitename']);
        $xoopsMailer->assign("ADMINMAIL", $xoopsConfig['adminmail']);
        $xoopsMailer->assign("SITEURL", XOOPS_URL . "/");
        $xoopsMailer->assign("IP", $_SERVER['REMOTE_ADDR']);
        $xoopsMailer->assign("NEWPWD_LINK", XOOPS_URL . "/lostpass.php?email=" . $email . "&code=" . $areyou);
        $xoopsMailer->setToUsers($getuser[0]);
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject(sprintf(_US_NEWPWDREQ, $xoopsConfig['sitename']));
        include $GLOBALS['xoops']->path('header.php');
        if (! $xoopsMailer->send()) {
            echo $xoopsMailer->getErrors();
        }
        echo "<h4>";
        printf(_US_CONFMAIL, $getuser[0]->getVar("uname"));
        echo "</h4>";
        include $GLOBALS['xoops']->path('footer.php');
    }
}
