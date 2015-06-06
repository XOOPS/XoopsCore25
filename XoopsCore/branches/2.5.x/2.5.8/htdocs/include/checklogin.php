<?php
/**
 * XOOPS authentication/authorization
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         core
 * @since           2.0.0
 * @version         $Id$
 * @todo            Will be refactored
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

xoops_loadLanguage('user');

// from $_POST we use keys: uname, pass, rememberme, xoops_redirect
XoopsLoad::load('XoopsFilterInput');
$clean_uname = '';
if (isset($_POST['uname'])) {
    $clean_uname = trim(XoopsFilterInput::clean($_POST['uname'], 'STRING'));
}
$clean_pass = '';
if (isset($_POST['pass'])) {
    $clean_pass = trim(XoopsFilterInput::clean($_POST['pass'], 'STRING'));
}
$clean_rememberme = '';
if (isset($_POST['rememberme'])) {
    $clean_rememberme = trim(XoopsFilterInput::clean($_POST['rememberme'], 'STRING'));
}
$clean_redirect = '';
if (isset($_POST['xoops_redirect'])) {
    $clean_redirect = trim(XoopsFilterInput::clean($_POST['xoops_redirect'], 'WEBURL'));
}

$uname = $clean_uname;
$pass = $clean_pass;
if ($uname == '' || $pass == '') {
    redirect_header(XOOPS_URL.'/user.php', 1, _US_INCORRECTLOGIN);
    exit();
}

$member_handler =& xoops_gethandler('member');
$myts =& MyTextsanitizer::getInstance();

include_once $GLOBALS['xoops']->path('class/auth/authfactory.php');

xoops_loadLanguage('auth');

$xoopsAuth =& XoopsAuthFactory::getAuthConnection($myts->addSlashes($uname));
$user = $xoopsAuth->authenticate($myts->addSlashes($uname), $myts->addSlashes($pass));

if (false != $user) {
    if (0 == $user->getVar('level')) {
        redirect_header(XOOPS_URL.'/index.php', 5, _US_NOACTTPADM);
        exit();
    }
    if ($xoopsConfig['closesite'] == 1) {
        $allowed = false;
        foreach ($user->getGroups() as $group) {
            if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            redirect_header(XOOPS_URL.'/index.php', 1, _NOPERM);
            exit();
        }
    }
    $user->setVar('last_login', time());
    if (!$member_handler->insertUser($user)) {
    }
    // Regenrate a new session id and destroy old session
    $GLOBALS["sess_handler"]->regenerate_id(true);
    $_SESSION = array();
    $_SESSION['xoopsUserId'] = $user->getVar('uid');
    $_SESSION['xoopsUserGroups'] = $user->getGroups();
    $user_theme = $user->getVar('theme');
    if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
        $_SESSION['xoopsUserTheme'] = $user_theme;
    }

    // Set cookie for rememberme
    if (!empty($xoopsConfig['usercookie'])) {
        if (!empty($clean_rememberme)) {
            setcookie($xoopsConfig['usercookie'], $_SESSION['xoopsUserId'] . '{-}' . md5($user->getVar('pass') . XOOPS_DB_NAME . XOOPS_DB_PASS . XOOPS_DB_PREFIX), time() + 31536000, '/', XOOPS_COOKIE_DOMAIN, 0);
        } else {
            setcookie($xoopsConfig['usercookie'], 0, -1, '/', XOOPS_COOKIE_DOMAIN, 0);
        }
    }

    if (!empty($clean_redirect) && !strpos($clean_redirect, 'register')) {
        $xoops_redirect = rawurldecode($clean_redirect);
        $parsed = parse_url(XOOPS_URL);
        $url = isset($parsed['scheme']) ? $parsed['scheme'].'://' : 'http://';
        if (isset( $parsed['host'])) {
            $url .= $parsed['host'];
            if (isset( $parsed['port'])) {
                $url .= ':' . $parsed['port'];
            }
        } else {
            $url .= $_SERVER['HTTP_HOST'];
        }
        if (@$parsed['path']) {
            if (strncmp($parsed['path'], $xoops_redirect, strlen($parsed['path']))) {
                $url .= $parsed['path'];
            }
        }
        $url .= $xoops_redirect;
    } else {
        $url = XOOPS_URL . '/index.php';
    }

    // RMV-NOTIFY
    // Perform some maintenance of notification records
    $notification_handler =& xoops_gethandler('notification');
    $notification_handler->doLoginMaintenance($user->getVar('uid'));

    redirect_header($url, 1, sprintf(_US_LOGGINGU, $user->getVar('uname')), false);
} else if (empty($clean_redirect)) {
    redirect_header(XOOPS_URL . '/user.php', 5, $xoopsAuth->getHtmlErrors());
} else {
    redirect_header(XOOPS_URL . '/user.php?xoops_redirect=' . urlencode($clean_redirect), 5, $xoopsAuth->getHtmlErrors(), false);
}
exit();
