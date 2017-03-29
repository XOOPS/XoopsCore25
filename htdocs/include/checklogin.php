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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             core
 * @since               2.0.0
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_loadLanguage('user');

// from $_POST we use keys: uname, pass, rememberme, xoops_redirect
XoopsLoad::load('XoopsRequest');
$uname = XoopsRequest::getString('uname', '', 'POST');
$pass = XoopsRequest::getString('pass', '', 'POST');
$rememberme = XoopsRequest::getString('rememberme', '', 'POST');
$redirect = XoopsRequest::getUrl('xoops_redirect', '', 'POST');

if ($uname == '' || $pass == '') {
    redirect_header(XOOPS_URL . '/user.php', 1, _US_INCORRECTLOGIN);
}

/* @var $member_handler XoopsMemberHandler */
$member_handler = xoops_getHandler('member');
$myts           = MyTextSanitizer::getInstance();

include_once $GLOBALS['xoops']->path('class/auth/authfactory.php');

xoops_loadLanguage('auth');

$xoopsAuth = XoopsAuthFactory::getAuthConnection($myts->addSlashes($uname));
$user      = $xoopsAuth->authenticate($uname, $pass);

if (false !== $user) {
    if (0 == $user->getVar('level')) {
        redirect_header(XOOPS_URL . '/index.php', 5, _US_NOACTTPADM);
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
            redirect_header(XOOPS_URL . '/index.php', 1, _NOPERM);
        }
    }
    $user->setVar('last_login', time());
    if (!$member_handler->insertUser($user)) {
    }
    // Regenrate a new session id and destroy old session
    $GLOBALS['sess_handler']->regenerate_id(true);
    $_SESSION                    = array();
    $_SESSION['xoopsUserId']     = $user->getVar('uid');
    $_SESSION['xoopsUserGroups'] = $user->getGroups();
    $user_theme                  = $user->getVar('theme');
    if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
        $_SESSION['xoopsUserTheme'] = $user_theme;
    }
    $xoopsPreload = XoopsPreload::getInstance();
    $xoopsPreload->triggerEvent('core.behavior.user.login', $user);
    // Set cookie for rememberme
    if (!empty($GLOBALS['xoopsConfig']['usercookie'])) {
        if (!empty($rememberme)) {
            $claims = array(
                'uid' => $_SESSION['xoopsUserId'],
            );
            $rememberTime = 60*60*24*30;
            $token = \Xmf\Jwt\TokenFactory::build('rememberme', $claims, $rememberTime);
            setcookie(
                $GLOBALS['xoopsConfig']['usercookie'],
                $token,
                time() + $rememberTime,
                '/',
                XOOPS_COOKIE_DOMAIN, XOOPS_PROT === 'https://',
                true
            );
        } else {
            setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600, '/', XOOPS_COOKIE_DOMAIN, 0, true);
            setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600);
        }
    }

    if (!empty($redirect) && !strpos($redirect, 'register')) {
        $xoops_redirect = rawurldecode($redirect);
        $parsed         = parse_url(XOOPS_URL);
        $url            = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : 'http://';
        if (isset($parsed['host'])) {
            $url .= $parsed['host'];
            if (isset($parsed['port'])) {
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
    $notification_handler = xoops_getHandler('notification');
    $notification_handler->doLoginMaintenance($user->getVar('uid'));

    redirect_header($url, 1, sprintf(_US_LOGGINGU, $user->getVar('uname')), false);
} elseif (empty($redirect)) {
    redirect_header(XOOPS_URL . '/user.php', 5, $xoopsAuth->getHtmlErrors());
} else {
    redirect_header(XOOPS_URL . '/user.php?xoops_redirect=' . urlencode($redirect), 5, $xoopsAuth->getHtmlErrors(), false);
}
exit();
