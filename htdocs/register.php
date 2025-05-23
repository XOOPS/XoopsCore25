<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * XOOPS Register
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             core
 * @since               2.0.0
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 */

use Xmf\Request;

include __DIR__ . '/mainfile.php';
$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.register.start');

xoops_loadLanguage('user');
xoops_load('XoopsUserUtility');

$myts = \MyTextSanitizer::getInstance();
/** @var XoopsConfigHandler $config_handler */
$config_handler  = xoops_getHandler('config');
$xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);

if (empty($xoopsConfigUser['allow_register'])) {
    redirect_header('index.php', 6, _US_NOREGISTER);
}

require_once $GLOBALS['xoops']->path('include/notification_constants.php');

/**
 * @param $uname
 * @param $email
 * @param $pass
 * @param $vpass
 *
 * @return bool|string
 */
function userCheck($uname, $email, $pass, $vpass)
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . ' is deprecated, use XoopsUserUtility::validate() instead');

    return XoopsUserUtility::validate($uname, $email, $pass, $vpass);
}

// from $_POST we use keys: op, uname, email, url, pass, vpass, timezone_offset,
//                          user_viewemail, user_mailok, agree_disc
    $op = Request::getCmd('op', 'register', 'POST');
    $uname = Request::getString('uname', '', 'POST');
    $email = Request::getEmail('email', '', 'POST');
    $url = Request::getUrl('url', '', 'POST');
    $pass = Request::getString('pass', '', 'POST');
    $vpass = Request::getString('vpass', '', 'POST');
    $timezone_offset = Request::getFloat('cid', $xoopsConfig['default_TZ'], 'POST');
    $user_viewemail = Request::getBool('user_viewemail', false, 'POST');
    $user_mailok = Request::getBool('user_mailok', false, 'POST');
    $agree_disc = Request::getBool('agree_disc', false, 'POST');

// from $_GET we may use keys: op, id, actkey
$clean_id     = '';
$clean_actkey = '';
if (!isset($_POST['op']) && isset($_GET['op'])) {
    $op = Request::getCmd('op', 'register', 'GET');
    if (isset($_GET['id'])) {
        $clean_id =  Request::getInt('id', '', 'GET');
    }
    if (isset($_GET['actkey'])) {
        $clean_actkey =  Request::getCmd('actkey', '', 'GET');
    }
    $op = in_array($op, [
        'actv',
        'activate',
    ], true) ? $op : 'register';
}

switch ($op) {
    case 'newuser':
        $xoopsOption['xoops_pagetitle'] = _US_USERREG;
        include $GLOBALS['xoops']->path('header.php');
        $stop = '';
        if (!$GLOBALS['xoopsSecurity']->check()) {
            $stop .= implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()) . '<br>';
        }
        if (0 != $xoopsConfigUser['reg_dispdsclmr'] && '' != $xoopsConfigUser['reg_disclaimer']) {
            if (empty($agree_disc)) {
                $stop .= _US_UNEEDAGREE . '<br>';
            }
        }
        $stop .= XoopsUserUtility::validate($uname, $email, $pass, $vpass);
        if (empty($stop)) {
            echo _US_USERNAME . ': ' . $myts->htmlSpecialChars($uname) . '<br>';
            echo _US_EMAIL . ': ' . $myts->htmlSpecialChars($email) . '<br>';
            if ('' != $url) {
                $url = formatURL($url);
                echo _US_WEBSITE . ': ' . $myts->htmlSpecialChars($url) . '<br>';
            }
            $f_timezone = ($timezone_offset < 0) ? 'GMT ' . $timezone_offset : 'GMT +' . $timezone_offset;
            echo _US_TIMEZONE . ": $f_timezone<br>";
            echo "<form action='register.php' method='post'>";
            xoops_load('XoopsFormCaptcha');
            $cpatcha = new XoopsFormCaptcha();
            echo '<br>' . $cpatcha->getCaption() . ': ' . $cpatcha->render();
            echo "<input type='hidden' name='uname' value='" . $myts->htmlSpecialChars($uname) . "' />
                  <input type='hidden' name='email' value='" . $myts->htmlSpecialChars($email) . "' />
                  <input type='hidden' name='user_viewemail' value='" . $user_viewemail . "' />
                  <input type='hidden' name='timezone_offset' value='" . (float)$timezone_offset . "' />
                  <input type='hidden' name='url' value='" . $myts->htmlSpecialChars($url) . "' />
                  <input type='hidden' name='pass' value='" . $myts->htmlSpecialChars($pass) . "' />
                  <input type='hidden' name='vpass' value='" . $myts->htmlSpecialChars($vpass) . "' />
                  <input type='hidden' name='user_mailok' value='" . $user_mailok . "' />
                  <br><br><input type='hidden' name='op' value='finish' />" . $GLOBALS['xoopsSecurity']->getTokenHTML() . "<input type='submit' value='" . _US_FINISH . "' /></form>";
        } else {
            echo "<span class='red'>$stop</span>";
            include $GLOBALS['xoops']->path('include/registerform.php');
            $reg_form->display();
        }
        include $GLOBALS['xoops']->path('footer.php');
        break;

    case 'finish':
        include $GLOBALS['xoops']->path('header.php');
        $stop = XoopsUserUtility::validate($uname, $email, $pass, $vpass);
        if (!$GLOBALS['xoopsSecurity']->check()) {
            $stop .= implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()) . '<br>';
        }
        xoops_load('XoopsCaptcha');
        $xoopsCaptcha = XoopsCaptcha::getInstance();
        if (!$xoopsCaptcha->verify()) {
            $stop .= $xoopsCaptcha->getMessage() . '<br>';
        }
        if (empty($stop)) {
            /** @var XoopsMemberHandler $member_handler */
            $member_handler = xoops_getHandler('member');
            /** @var XoopsUser $newuser */
            $newuser        = $member_handler->createUser();
            $newuser->setVar('user_viewemail', $user_viewemail, true);
            $newuser->setVar('uname', $uname, true);
            $newuser->setVar('email', $email, true);
            if ('' != $url) {
                $newuser->setVar('url', formatURL($url), true);
            }
            $newuser->setVar('user_avatar', 'avatars/blank.gif', true);
            $actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
            $newuser->setVar('actkey', $actkey, true);
            $newuser->setVar('pass', password_hash($pass, PASSWORD_DEFAULT), true);
            $newuser->setVar('timezone_offset', $timezone_offset, true);
            $newuser->setVar('user_regdate', time(), true);
            $newuser->setVar('uorder', $GLOBALS['xoopsConfig']['com_order'], true);
            $newuser->setVar('umode', $GLOBALS['xoopsConfig']['com_mode'], true);
            $newuser->setVar('theme', $GLOBALS['xoopsConfig']['theme_set'], true);
            $newuser->setVar('user_mailok', $user_mailok, true);
            $newuser->setVar('notify_method', ($xoopsConfigUser['default_notification'] ?? XOOPS_NOTIFICATION_METHOD_PM));
            if (1 == $xoopsConfigUser['activation_type']) {
                $newuser->setVar('level', 1, true);
            } else {
                $newuser->setVar('level', 0, true);
            }
            if (!$member_handler->insertUser($newuser)) {
                echo _US_REGISTERNG;
                include $GLOBALS['xoops']->path('footer.php');
                exit();
            }
            $newid = $newuser->getVar('uid');
            if (!$member_handler->addUserToGroup(XOOPS_GROUP_USERS, $newid)) {
                echo _US_REGISTERNG;
                include $GLOBALS['xoops']->path('footer.php');
                exit();
            }
            if (1 == $xoopsConfigUser['activation_type']) {
                XoopsUserUtility::sendWelcome($newuser);
                redirect_header('index.php', 4, _US_ACTLOGIN);
            }
            // Sending notification email to user for self activation
            if (0 == $xoopsConfigUser['activation_type']) {
                $xoopsMailer = xoops_getMailer();
                $xoopsMailer->useMail();
                $xoopsMailer->setTemplate('register.tpl');
                $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
                $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
                $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                $xoopsMailer->setToUsers(new XoopsUser($newid));
                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                $xoopsMailer->setFromName($xoopsConfig['sitename']);
                $xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $uname));
                if (!$xoopsMailer->send()) {
                    echo _US_YOURREGMAILNG;
                } else {
                    echo _US_YOURREGISTERED;
                }
                // Sending notification email to administrator for activation
            } elseif (2 == $xoopsConfigUser['activation_type']) {
                $xoopsMailer = xoops_getMailer();
                $xoopsMailer->useMail();
                $xoopsMailer->setTemplate('adminactivate.tpl');
                $xoopsMailer->assign('USERNAME', $uname);
                $xoopsMailer->assign('USEREMAIL', $email);
                $xoopsMailer->assign('USERACTLINK', XOOPS_URL . '/register.php?op=actv&id=' . $newid . '&actkey=' . $actkey);
                $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
                $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
                $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                /** @var XoopsMemberHandler $member_handler */
                $member_handler = xoops_getHandler('member');
                $xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['activation_group']));
                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                $xoopsMailer->setFromName($xoopsConfig['sitename']);
                $xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $uname));
                if (!$xoopsMailer->send()) {
                    echo _US_YOURREGMAILNG;
                } else {
                    echo _US_YOURREGISTERED2;
                }
            }
            if (1 == $xoopsConfigUser['new_user_notify'] && !empty($xoopsConfigUser['new_user_notify_group'])) {
                $xoopsMailer = xoops_getMailer();
                $xoopsMailer->reset();
                $xoopsMailer->useMail();
                /** @var XoopsMemberHandler $member_handler */
                $member_handler = xoops_getHandler('member');
                $xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                $xoopsMailer->setFromName($xoopsConfig['sitename']);
                $xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT, $xoopsConfig['sitename']));
                $xoopsMailer->setBody(sprintf(_US_HASJUSTREG, $uname));
                $xoopsMailer->send();
            }
        } else {
            echo "<span class='red bold'>{$stop}</span>";
            include $GLOBALS['xoops']->path('include/registerform.php');
            $reg_form->display();
        }
        include $GLOBALS['xoops']->path('footer.php');
        break;

    case 'actv':
    case 'activate':
        $id     = $clean_id;
        $actkey = $clean_actkey;
        if (empty($id)) {
            redirect_header('index.php', 1, '');
        }
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        /** @var XoopsUser $thisuser */
        $thisuser       = $member_handler->getUser($id);
        if (!is_object($thisuser)) {
            exit();
        }
        if ($thisuser->getVar('actkey') != $actkey) {
            redirect_header('index.php', 5, _US_ACTKEYNOT);
        } else {
            if ($thisuser->getVar('level') > 0) {
                redirect_header('user.php', 5, _US_ACONTACT, false);
            } else {
                if (false !== $member_handler->activateUser($thisuser)) {
                    $xoopsPreload->triggerEvent('core.behavior.user.activate', $thisuser);
                    $config_handler  = xoops_getHandler('config');
                    $xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
                    if (2 == $xoopsConfigUser['activation_type']) {
                        $myts        = \MyTextSanitizer::getInstance();
                        $xoopsMailer = xoops_getMailer();
                        $xoopsMailer->useMail();
                        $xoopsMailer->setTemplate('activated.tpl');
                        $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
                        $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
                        $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                        $xoopsMailer->setToUsers($thisuser);
                        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                        $xoopsMailer->setFromName($xoopsConfig['sitename']);
                        $xoopsMailer->setSubject(sprintf(_US_YOURACCOUNT, $xoopsConfig['sitename']));
                        include $GLOBALS['xoops']->path('header.php');
                        if (!$xoopsMailer->send()) {
                            printf(_US_ACTVMAILNG, $thisuser->getVar('uname'));
                        } else {
                            printf(_US_ACTVMAILOK, $thisuser->getVar('uname'));
                        }
                        include $GLOBALS['xoops']->path('footer.php');
                    } else {
                        redirect_header('user.php', 5, _US_ACTLOGIN, false);
                    }
                } else {
                    redirect_header('index.php', 5, _US_ACTFAILD);
                }
            }
        }
        break;

    case 'register':
    default:
        $xoopsOption['xoops_pagetitle'] = _US_USERREG;
        include $GLOBALS['xoops']->path('header.php');
        $xoTheme->addMeta('meta', 'keywords', _US_USERREG . ', ' . _US_NICKNAME); // FIXME!
        $xoTheme->addMeta('meta', 'description', strip_tags($xoopsConfigUser['reg_disclaimer']));
        include $GLOBALS['xoops']->path('include/registerform.php');
        $reg_form->display();
        include $GLOBALS['xoops']->path('footer.php');
        break;
}
