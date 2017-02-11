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
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              Jan Pedersen
 * @author              trabis <lusopoemas@gmail.com>
 */

include __DIR__ . '/header.php';

if ($GLOBALS['xoopsUser']) {
    header('location: userinfo.php?uid= ' . $GLOBALS['xoopsUser']->getVar('uid'));
    exit();
}

if (!empty($_GET['op']) && in_array($_GET['op'], array('actv', 'activate'))) {
    header('location: ./activate.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
    exit();
}

xoops_load('XoopsUserUtility');
$myts = MyTextSanitizer::getInstance();

/* @var $config_handler XoopsConfigHandler  */
$config_handler             = xoops_getHandler('config');
$GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
if (empty($GLOBALS['xoopsConfigUser']['allow_register'])) {
    redirect_header('index.php', 6, _US_NOREGISTER);
}

// get the key we need to access our 'op' in $_POST
// if this key is not set, empty $_POST since this is a new registration and
// no legitimate data would be there.
$opkey = 'profile_opname';
if (isset($_SESSION[$opkey])) {
    $current_opname = $_SESSION[$opkey];
    unset($_SESSION[$opkey]);
    if (!isset($_POST[$current_opname])) {
        $_POST = array();
    }
} else {
    $_POST          = array();
    $current_opname = 'op'; // does not matter, it isn't there
}

$op           = !isset($_POST[$current_opname]) ? 'register' : $_POST[$current_opname];
$current_step = isset($_POST['step']) ? (int)$_POST['step'] : 0;

// The newly introduced variable $_SESSION['profile_post'] is contaminated by $_POST, thus we use an old vaiable to hold uid parameter
$uid = !empty($_SESSION['profile_register_uid']) ? (int)$_SESSION['profile_register_uid'] : 0;

// First step is already secured by with the captcha Token so lets check the others
if ($current_step > 0 && !$GLOBALS['xoopsSecurity']->check()) {
    redirect_header('user.php', 5, _PROFILE_MA_EXPIRED);
}

$criteria = new CriteriaCompo();
$criteria->setSort('step_order');
$regstep_handler = xoops_getModuleHandler('regstep');

if (!$steps = $regstep_handler->getAll($criteria, null, false, false)) {
    redirect_header(XOOPS_URL . '/', 6, _PROFILE_MA_NOSTEPSAVAILABLE);
}

foreach (array_keys($steps) as $key) {
    $steps[$key]['step_no'] = $key + 1;
}

$GLOBALS['xoopsOption']['template_main'] = 'profile_register.tpl';
include $GLOBALS['xoops']->path('header.php');

$GLOBALS['xoopsTpl']->assign('steps', $steps);
$GLOBALS['xoopsTpl']->assign('lang_register_steps', _PROFILE_MA_REGISTER_STEPS);

$xoBreadcrumbs[] = array(
    'link'  => XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/register.php',
    'title' => _PROFILE_MA_REGISTER);
if (isset($steps[$current_step])) {
    $xoBreadcrumbs[] = array('title' => $steps[$current_step]['step_name']);
}

/* @var $member_handler XoopsMemberHandler */
$member_handler  = xoops_getHandler('member');
$profile_handler = xoops_getModuleHandler('profile');

$fields     = $profile_handler->loadFields();
$userfields = $profile_handler->getUserVars();

if ($uid == 0) {
    // No user yet? Create one and set default values.
    $newuser = $member_handler->createUser();
    $profile = $profile_handler->create();
    if (count($fields) > 0) {
        foreach (array_keys($fields) as $i) {
            $fieldname = $fields[$i]->getVar('field_name');
            if (in_array($fieldname, $userfields)) {
                $default = $fields[$i]->getVar('field_default');
                if ($default === '' || $default === null) {
                    continue;
                }
                $newuser->setVar($fieldname, $default);
            }
        }
    }
} else {
    // We already have a user? Just load it! Security is handled by token so there is no fake uid here.
    $newuser = $member_handler->getUser($uid);
    $profile = $profile_handler->get($uid);
}

// Lets merge current $_POST  with $_SESSION['profile_post'] so we can have access to info submited in previous steps
// Get all fields that we can expect from a $_POST inlcuding our private '_message_'
$fieldnames = array();
foreach (array_keys($fields) as $i) {
    $fieldnames[] = $fields[$i]->getVar('field_name');
}
$fieldnames   = array_merge($fieldnames, $userfields);
$fieldnames[] = '_message_';

// Get $_POST that matches above criteria, we do not need to store step, tokens, etc
$postfields = array();
foreach ($fieldnames as $fieldname) {
    if (isset($_POST[$fieldname])) {
        $postfields[$fieldname] = $_POST[$fieldname];
    }
}

if ($current_step == 0) {
    // Reset any previous session for first step
    $_SESSION['profile_post']         = array();
    $_SESSION['profile_register_uid'] = null;
} else {
    // Merge current $_POST  with $_SESSION['profile_post']
    $_SESSION['profile_post'] = array_merge($_SESSION['profile_post'], $postfields);
    $_POST                    = array_merge($_SESSION['profile_post'], $_POST);
}

// Set vars from $_POST/$_SESSION['profile_post']
foreach (array_keys($fields) as $field) {
    if (!isset($_POST[$field])) {
        continue;
    }

    $value = $fields[$field]->getValueForSave($_POST[$field]);
    if (in_array($field, $userfields)) {
        $newuser->setVar($field, $value);
    } else {
        $profile->setVar($field, $value);
    }
}

$stop = '';

//Client side validation
if (isset($_POST['step']) && isset($_SESSION['profile_required'])) {
    foreach ($_SESSION['profile_required'] as $name => $title) {
        if (!isset($_POST[$name]) || empty($_POST[$name])) {
            $stop .= sprintf(_FORM_ENTER, $title) . '<br>';
        }
    }
}

// Check user data at first step
if ($current_step == 1) {
    $uname      = isset($_POST['uname']) ? $myts->stripSlashesGPC(trim($_POST['uname'])) : '';
    $email      = isset($_POST['email']) ? $myts->stripSlashesGPC(trim($_POST['email'])) : '';
    $url        = isset($_POST['url']) ? $myts->stripSlashesGPC(trim($_POST['url'])) : '';
    $pass       = isset($_POST['pass']) ? $myts->stripSlashesGPC(trim($_POST['pass'])) : '';
    $vpass      = isset($_POST['vpass']) ? $myts->stripSlashesGPC(trim($_POST['vpass'])) : '';
    $agree_disc = (isset($_POST['agree_disc']) && (int)$_POST['agree_disc']) ? 1 : 0;

    if ($GLOBALS['xoopsConfigUser']['reg_dispdsclmr'] != 0 && $GLOBALS['xoopsConfigUser']['reg_disclaimer'] !== '') {
        if (empty($agree_disc)) {
            $stop .= _US_UNEEDAGREE . '<br>';
        }
    }

    $newuser->setVar('uname', $uname);
    $newuser->setVar('email', $email);
    $newuser->setVar('pass', $pass ? password_hash($pass, PASSWORD_DEFAULT) : '');
    $stop .= XoopsUserUtility::validate($newuser, $pass, $vpass);

    xoops_load('XoopsCaptcha');
    $xoopsCaptcha = XoopsCaptcha::getInstance();
    if (!$xoopsCaptcha->verify()) {
        $stop .= $xoopsCaptcha->getMessage();
    }
}

// If the last step required SAVE or if we're on the last step then we will insert/update user on database
if ($current_step > 0 && empty($stop) && (!empty($steps[$current_step - 1]['step_save']) || !isset($steps[$current_step]))) {
    if ($GLOBALS['xoopsModuleConfig']['profileCaptchaAfterStep1'] == 1 && $current_step > 1) {
        xoops_load('XoopsCaptcha');
        $xoopsCaptcha2 = XoopsCaptcha::getInstance();
        if (!$xoopsCaptcha2->verify()) {
            $stop .= $xoopsCaptcha2->getMessage();
        }
    }

    if (empty($stop)) {
        $isNew = $newuser->isNew();

        //Did created an user already? If not then let us set some extra info
        if ($isNew) {
            $uname = isset($_POST['uname']) ? $myts->stripSlashesGPC(trim($_POST['uname'])) : '';
            $email = isset($_POST['email']) ? $myts->stripSlashesGPC(trim($_POST['email'])) : '';
            $url   = isset($_POST['url']) ? $myts->stripSlashesGPC(trim($_POST['url'])) : '';
            $pass  = isset($_POST['pass']) ? $myts->stripSlashesGPC(trim($_POST['pass'])) : '';
            $newuser->setVar('uname', $uname);
            $newuser->setVar('email', $email);
            $newuser->setVar('pass', $pass ? password_hash($pass, PASSWORD_DEFAULT) : '');
            $actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
            $newuser->setVar('actkey', $actkey, true);
            $newuser->setVar('user_regdate', time(), true);
            $newuser->setVar('uorder', $GLOBALS['xoopsConfig']['com_order'], true);
            $newuser->setVar('umode', $GLOBALS['xoopsConfig']['com_mode'], true);
            $newuser->setVar('theme', $GLOBALS['xoopsConfig']['theme_set'], true);
            $newuser->setVar('user_avatar', 'avatars/blank.gif', true);
            if ($GLOBALS['xoopsConfigUser']['activation_type'] == 1) {
                $newuser->setVar('level', 1, true);
            } else {
                $newuser->setVar('level', 0, true);
            }
        }

        // Insert/update user and check if we have succeded
        if (!$member_handler->insertUser($newuser)) {
            $stop .= _US_REGISTERNG . '<br>';
            $stop .= implode('<br>', $newuser->getErrors());
        } else {
            // User inserted! Now insert custom profile fields
            $profile->setVar('profile_id', $newuser->getVar('uid'));
            $profile_handler->insert($profile);

            // We are good! If this is 'was' a new user then we handle notification
            if ($isNew) {
                if ($GLOBALS['xoopsConfigUser']['new_user_notify'] == 1 && !empty($GLOBALS['xoopsConfigUser']['new_user_notify_group'])) {
                    $xoopsMailer =& xoops_getMailer();
                    $xoopsMailer->reset();
                    $xoopsMailer->useMail();
                    $xoopsMailer->setToGroups($member_handler->getGroup($GLOBALS['xoopsConfigUser']['new_user_notify_group']));
                    $xoopsMailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
                    $xoopsMailer->setFromName($GLOBALS['xoopsConfig']['sitename']);
                    $xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT, $GLOBALS['xoopsConfig']['sitename']));
                    $xoopsMailer->setBody(sprintf(_US_HASJUSTREG, $newuser->getVar('uname')));
                    $xoopsMailer->send(true);
                }

                $message = '';
                if (!$member_handler->addUserToGroup(XOOPS_GROUP_USERS, $newuser->getVar('uid'))) {
                    $message = _PROFILE_MA_REGISTER_NOTGROUP . '<br>';
                } else {
                    if ($GLOBALS['xoopsConfigUser']['activation_type'] == 1) {
                        XoopsUserUtility::sendWelcome($newuser);
                    } else {
                        if ($GLOBALS['xoopsConfigUser']['activation_type'] == 0) {
                            $xoopsMailer =& xoops_getMailer();
                            $xoopsMailer->reset();
                            $xoopsMailer->useMail();
                            $xoopsMailer->setTemplate('register.tpl');
                            $xoopsMailer->assign('SITENAME', $GLOBALS['xoopsConfig']['sitename']);
                            $xoopsMailer->assign('ADMINMAIL', $GLOBALS['xoopsConfig']['adminmail']);
                            $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                            $xoopsMailer->assign('X_UPASS', $_POST['vpass']);
                            $xoopsMailer->setToUsers($newuser);
                            $xoopsMailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
                            $xoopsMailer->setFromName($GLOBALS['xoopsConfig']['sitename']);
                            $xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $newuser->getVar('uname')));
                            if (!$xoopsMailer->send(true)) {
                                $_SESSION['profile_post']['_message_'] = 0;
                            } else {
                                $_SESSION['profile_post']['_message_'] = 1;
                            }
                        } else {
                            if ($GLOBALS['xoopsConfigUser']['activation_type'] == 2) {
                                $xoopsMailer =& xoops_getMailer();
                                $xoopsMailer->reset();
                                $xoopsMailer->useMail();
                                $xoopsMailer->setTemplate('adminactivate.tpl');
                                $xoopsMailer->assign('USERNAME', $newuser->getVar('uname'));
                                $xoopsMailer->assign('USEREMAIL', $newuser->getVar('email'));
                                $xoopsMailer->assign('USERACTLINK', XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/activate.php?id=' . $newuser->getVar('uid') . '&actkey=' . $newuser->getVar('actkey', 'n'));
                                $xoopsMailer->assign('SITENAME', $GLOBALS['xoopsConfig']['sitename']);
                                $xoopsMailer->assign('ADMINMAIL', $GLOBALS['xoopsConfig']['adminmail']);
                                $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                                $xoopsMailer->setToGroups($member_handler->getGroup($GLOBALS['xoopsConfigUser']['activation_group']));
                                $xoopsMailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
                                $xoopsMailer->setFromName($GLOBALS['xoopsConfig']['sitename']);
                                $xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $newuser->getVar('uname')));
                                if (!$xoopsMailer->send()) {
                                    $_SESSION['profile_post']['_message_'] = 2;
                                } else {
                                    $_SESSION['profile_post']['_message_'] = 3;
                                }
                            }
                        }
                    }
                }
                if ($message) {
                    $GLOBALS['xoopsTpl']->append('confirm', $message);
                }
                $_SESSION['profile_register_uid'] = $newuser->getVar('uid');
            }
        }
    }
}

if (!empty($stop) || isset($steps[$current_step])) {
    include_once __DIR__ . '/include/forms.php';
    $current_step = empty($stop) ? $current_step : $current_step - 1;
    $reg_form     = profile_getRegisterForm($newuser, $profile, $steps[$current_step]);
    $reg_form->assign($GLOBALS['xoopsTpl']);
    $GLOBALS['xoopsTpl']->assign('current_step', $current_step);
    $GLOBALS['xoopsTpl']->assign('stop', $stop);
} else {
    // No errors and no more steps, finish
    $GLOBALS['xoopsTpl']->assign('finish', _PROFILE_MA_REGISTER_FINISH);
    $GLOBALS['xoopsTpl']->assign('current_step', -1);
    if ($GLOBALS['xoopsConfigUser']['activation_type'] == 1 && !empty($_SESSION['profile_post']['pass'])) {
        $GLOBALS['xoopsTpl']->assign('finish_login', _PROFILE_MA_FINISH_LOGIN);
        $GLOBALS['xoopsTpl']->assign('finish_uname', $newuser->getVar('uname'));
        $GLOBALS['xoopsTpl']->assign('finish_pass', htmlspecialchars($_SESSION['profile_post']['pass']));
    }
    if (isset($_SESSION['profile_post']['_message_'])) {
        //todo, if user is activated by admin, then we should inform it along with error messages.  _US_YOURREGMAILNG is not enough
        $messages = array(_US_YOURREGMAILNG, _US_YOURREGISTERED, _US_YOURREGMAILNG, _US_YOURREGISTERED2);
        $GLOBALS['xoopsTpl']->assign('finish_message', $messages[$_SESSION['profile_post']['_message_']]);
    }
    $_SESSION['profile_post'] = null;
}

include __DIR__ . '/footer.php';
