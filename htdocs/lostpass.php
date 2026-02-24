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
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package   core
 * @since     2.0.0
 */

use Xmf\Request;

include __DIR__ . '/mainfile.php';

$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.lostpass.start');

xoops_loadLanguage('user');

require_once __DIR__ . '/class/XoopsTokenHandler.php';
require_once __DIR__ . '/class/LostPassSecurity.php';

/** @var XoopsMySQLDatabase $xoopsDB */
$tokenHandler = new XoopsTokenHandler($xoopsDB);
$rateLimiter  = new LostPassSecurity();
/** @var XoopsMemberHandler $member_handler */
$member_handler = xoops_getHandler('member');

/** @var XoopsConfigHandler $config_handler */
$config_handler = xoops_getHandler('config');
$xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);

// Use REMOTE_ADDR only — X-Forwarded-For is client-spoofable and would
// let attackers bypass rate limiting by rotating the header value.
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$minPw = max(8, (int)($xoopsConfigUser['minpass'] ?? 8));

// Generic message used on all exit paths to prevent enumeration
$msgGeneric = _US_PWDMAILED;
// Invalid-link message: use custom constant if defined, else fall back to generic
$msgInvalid = defined('_US_RESETLINKINVALID')
    ? constant('_US_RESETLINKINVALID')
    : $msgGeneric;

// Read uid/token: GET for link click, POST for form submission
$uid   = Request::getInt('uid', 0, 'GET');
$token = Request::getString('token', '', 'GET');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $uid   = Request::getInt('uid', $uid, 'POST');
    $token = Request::getString('token', $token, 'POST');
}

/* =========================================================
 * MODE A: Reset link clicked (uid + token present)
 * GET  → show "set new password" form
 * POST → validate & update password
 * ======================================================= */
if ($uid > 0 && $token !== '') {
    $user = $member_handler->getUser($uid);
    if (!is_object($user)) {
        redirect_header('user.php', 3, $msgInvalid, false);
        exit();
    }

    // --- POST: set new password ---
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        // Rate limit reset attempts
        if ($rateLimiter->isRateLimited($ip, 'uid:' . (string)$uid)) {
            redirect_header('user.php', 3, $msgInvalid, false);
            exit();
        }

        // CSRF check
        if (isset($GLOBALS['xoopsSecurity']) && is_object($GLOBALS['xoopsSecurity'])) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('user.php', 3, $msgInvalid, false);
                exit();
            }
        }

        $pass  = Request::getString('pass', '', 'POST');
        $vpass = Request::getString('vpass', '', 'POST');

        $errors = [];
        if ($pass === '' || $vpass === '') {
            $errors[] = _US_ENTERPWD;
        } elseif ($pass !== $vpass) {
            $errors[] = _US_PASSNOTSAME;
        } elseif (strlen($pass) < $minPw) {
            $errors[] = sprintf(_US_PWDTOOSHORT, (string)$minPw);
        } elseif (strlen($pass) > 4096) {
            $errors[] = defined('_US_PWDTOOLONG')
                ? constant('_US_PWDTOOLONG')
                : 'Password exceeds maximum length.';
        }

        if (!empty($errors)) {
            $GLOBALS['xoopsOption']['template_main'] = 'system_lostpass.tpl';
            include_once $GLOBALS['xoops']->path('header.php');
            lostpass_assign_form($GLOBALS['xoopsTpl'], $uid, $token, $minPw, $errors);
            include_once $GLOBALS['xoops']->path('footer.php');
            exit();
        }

        // Atomically verify and consume the token
        if (!$tokenHandler->verify($uid, 'lostpass', $token)) {
            redirect_header('user.php', 3, $msgInvalid, false);
            exit();
        }

        $user->setVar('pass', password_hash($pass, PASSWORD_DEFAULT));

        if (!$member_handler->insertUser($user, true)) {
            $GLOBALS['xoopsOption']['template_main'] = 'system_lostpass.tpl';
            include_once $GLOBALS['xoops']->path('header.php');
            lostpass_assign_form($GLOBALS['xoopsTpl'], $uid, $token, $minPw, [], _US_MAILPWDNG);
            include_once $GLOBALS['xoops']->path('footer.php');
            exit();
        }

        // User proved token possession — safe to show a clear success message
        $msgSuccess = defined('_US_PWDRESETDONE')
            ? constant('_US_PWDRESETDONE')
            : 'Your password has been changed successfully.';
        redirect_header('user.php', 3, $msgSuccess, false);
        exit();
    }

    // --- GET: show reset form ---
    $GLOBALS['xoopsOption']['template_main'] = 'system_lostpass.tpl';
    include_once $GLOBALS['xoops']->path('header.php');
    lostpass_assign_form($GLOBALS['xoopsTpl'], $uid, $token, $minPw);
    include_once $GLOBALS['xoops']->path('footer.php');
    exit();
}

/* =========================================================
 * MODE B: Request password reset (email submitted, POST only)
 * Always responds with the same generic message.
 * ======================================================= */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect_header('user.php', 3, $msgGeneric, false);
    exit();
}

// CSRF check on email submission — form posts token as hidden field "t"
$csrfToken = Request::getString('t', '', 'POST');
if (isset($GLOBALS['xoopsSecurity']) && is_object($GLOBALS['xoopsSecurity'])) {
    if (!$GLOBALS['xoopsSecurity']->check(true, $csrfToken)) {
        redirect_header('user.php', 3, $msgGeneric, false);
        exit();
    }
}

$email = Request::getEmail('email', '', 'POST');

if ($email === '') {
    redirect_header('user.php', 3, $msgGeneric, false);
    exit();
}

// Rate limit before any DB lookup
if ($rateLimiter->isRateLimited($ip, $email)) {
    redirect_header('user.php', 3, $msgGeneric, false);
    exit();
}

$criteria = new Criteria('email', $email);
$users    = $member_handler->getUsers($criteria);

if (!empty($users) && is_object($users[0])) {
    $user = $users[0];

    // Only active accounts (level > 0); inactive/banned get no email but same response
    if ((int)$user->getVar('level') > 0) {
        $userUid = (int)$user->getVar('uid');

        // Cooldown: skip if a token was already issued in the last 15 minutes
        if ($tokenHandler->countRecent($userUid, 'lostpass', 900) === 0) {
            $rawToken = $tokenHandler->create($userUid, 'lostpass', 3600);

            if ($rawToken === false) {
                trigger_error(
                    basename(__FILE__) . ': token creation failed for uid ' . $userUid,
                    E_USER_WARNING
                );
            } else {
                $resetLink = XOOPS_URL . '/lostpass.php?uid=' . $userUid
                           . '&token=' . urlencode($rawToken);

                $xoopsMailer = xoops_getMailer();
                $xoopsMailer->useMail();
                $xoopsMailer->setTemplate('lostpass1.tpl');
                $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
                $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
                $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                $xoopsMailer->assign('IP', $ip);
                $xoopsMailer->assign('NEWPWD_LINK', $resetLink);
                $xoopsMailer->setToUsers($user);
                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                $xoopsMailer->setFromName($xoopsConfig['sitename']);
                $xoopsMailer->setSubject(sprintf(_US_NEWPWDREQ, $xoopsConfig['sitename']));
                $xoopsMailer->send();
            }
        }
    }
}

// Always the same response regardless of outcome
redirect_header('user.php', 3, $msgGeneric, false);
exit();

/* =========================================================
 * Template variable assignment helper
 * ======================================================= */

/**
 * Assign template variables for the password reset form.
 *
 * @param \XoopsTpl $tpl     Smarty template instance
 * @param int       $uid     User ID
 * @param string    $token   Reset token
 * @param int       $minPw   Minimum password length
 * @param array     $errors  Validation error messages
 * @param string    $message General message (e.g. save failure)
 *
 * @return void
 * @throws \SmartyException If template variable assignment fails
 */
function lostpass_assign_form(\XoopsTpl $tpl, int $uid, string $token, int $minPw, array $errors = [], string $message = ''): void
{
    $tokenHtml = '';
    if (isset($GLOBALS['xoopsSecurity']) && is_object($GLOBALS['xoopsSecurity'])) {
        $tokenHtml = $GLOBALS['xoopsSecurity']->getTokenHTML();
    }

    $tpl->assign('lp_heading', _US_LOSTPASSWORD);
    $tpl->assign('lp_action', XOOPS_URL . '/lostpass.php');
    $tpl->assign('lp_uid', $uid);
    $tpl->assign('lp_token', $token);
    $tpl->assign('lp_token_html', $tokenHtml);
    $tpl->assign('lp_lang_password', _US_PASSWORD);
    $tpl->assign('lp_lang_verifypass', _US_VERIFYPASS);
    $tpl->assign('lp_lang_submit', _US_SUBMIT);
    $tpl->assign('lp_min_pw_note', sprintf(_US_PWDTOOSHORT, (string)$minPw));
    $tpl->assign('lp_errors', $errors);
    $tpl->assign('lp_message', $message);
    $tpl->assign('lp_show_form', $message === '');
}
