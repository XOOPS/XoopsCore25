<?php
/**
 * XOOPS password recovery (hardened, PHP 8.2+)
 *
 * Security improvements over original:
 * - Strong random tokens replace md5-truncated codes
 * - User chooses own password (no plaintext password in email)
 * - Rate limiting (IP + identifier) via XoopsCache
 * - No account enumeration (unified generic responses)
 * - CSRF protection on password change form
 * - Token TTL + single-use invalidation
 * - Hash-DoS prevention (password length cap)
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

require_once __DIR__ . '/class/LostpassSecurity.php';

/** @var XoopsDatabase $xoopsDB */
$security = new LostpassSecurity($xoopsDB);
/** @var XoopsMemberHandler $member_handler */
$member_handler = xoops_getHandler('member');

$ip    = $_SERVER['REMOTE_ADDR'] ?? '';
$minPw = 12;

// Generic message used on all exit paths to prevent enumeration
$msgGeneric = _US_PWDMAILED;
// Invalid-link message: use custom constant if defined, else fall back to generic
$msgInvalid = defined('_US_RESETLINKINVALID')
    ? _US_RESETLINKINVALID
    : $msgGeneric;

// Read uid/token: GET for link click, POST for form submission
$uid   = Request::getInt('uid', 0, 'GET');
$token = Request::getString('token', '', 'GET');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    $payload = $security->readPayload($user);
    if ($payload === null) {
        redirect_header('user.php', 3, $msgInvalid, false);
        exit();
    }

    $issuedAt   = (int)$payload['issuedAt'];
    $storedHash = (string)$payload['hash'];
    $source     = (string)$payload['source'];

    // Check token expiry
    if ($security->isExpired($issuedAt)) {
        // Clear expired token (safe: only clears our lostpass tokens)
        $security->clearPayloadInMemory($user, $source);
        if ($source === 'actkey') {
            $member_handler->insertUser($user, true);
        }
        redirect_header('user.php', 3, $msgInvalid, false);
        exit();
    }

    // Verify token hash (timing-safe)
    if (!hash_equals($storedHash, $security->hashToken($token))) {
        redirect_header('user.php', 3, $msgInvalid, false);
        exit();
    }

    // --- POST: set new password ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Rate limit reset attempts
        if ($security->isAbusing($ip, 'uid:' . (string)$uid)) {
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
            $errors[] = 'Password is too long.';
        }

        if (!empty($errors)) {
            include $GLOBALS['xoops']->path('header.php');
            echo '<div class="errorMsg"><ul>';
            foreach ($errors as $err) {
                echo '<li>' . htmlspecialchars($err, ENT_QUOTES) . '</li>';
            }
            echo '</ul></div><br>';
            lostpass_render_form($uid, $token, $minPw);
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }

        // For actkey source: clear in memory (deferred), set password, single DB write.
        // For cache source: set password first, save to DB, then delete cache.
        // This ensures the token is never consumed if the DB write fails.
        if ($source === 'actkey') {
            $security->clearPayloadInMemory($user, $source);
        }
        $user->setVar('pass', password_hash($pass, PASSWORD_DEFAULT));

        if (!$member_handler->insertUser($user, true)) {
            include $GLOBALS['xoops']->path('header.php');
            echo _US_MAILPWDNG;
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }

        // Cache source: delete token only after successful password save
        if ($source === 'cache') {
            $security->clearPayloadInMemory($user, $source);
        }

        redirect_header('user.php', 3, $msgGeneric, false);
        exit();
    }

    // --- GET: show reset form ---
    include $GLOBALS['xoops']->path('header.php');
    lostpass_render_form($uid, $token, $minPw);
    include $GLOBALS['xoops']->path('footer.php');
    exit();
}

/* =========================================================
 * MODE B: Request password reset (email submitted)
 * Always responds with the same generic message.
 * ======================================================= */
$email = Request::getEmail('email', '', 'POST');
if ($email === '') {
    $email = Request::getEmail('email', '', 'GET');
}

if ($email === '') {
    redirect_header('user.php', 3, $msgGeneric, false);
    exit();
}

// Rate limit before any DB lookup
if ($security->isAbusing($ip, $email)) {
    redirect_header('user.php', 3, $msgGeneric, false);
    exit();
}

$criteria = new Criteria('email', $email);
$users    = $member_handler->getUsers($criteria);

if (!empty($users) && is_object($users[0])) {
    $user = $users[0];

    // Only active accounts (level > 0); inactive/banned get no email but same response
    if ((int)$user->getVar('level') > 0) {
        $rawToken = $security->generateToken();
        $hash     = $security->hashToken($rawToken);
        $issuedAt = time();

        // Store payload (actkey if safe + fits, else cache)
        if ($security->storePayload($user, $member_handler, $issuedAt, $hash)) {
            $resetLink = XOOPS_URL . '/lostpass.php?uid=' . (int)$user->getVar('uid')
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

// Always the same response regardless of outcome
redirect_header('user.php', 3, $msgGeneric, false);
exit();

/* =========================================================
 * Form renderer (inline HTML, matches register.php pattern)
 * ======================================================= */

/**
 * Render the password reset form.
 */
function lostpass_render_form(int $uid, string $token, int $minPw): void
{
    echo '<fieldset class="pad10">';
    echo '<legend class="bold">' . _US_LOSTPASSWORD . '</legend>';
    echo '<form method="post" action="' . XOOPS_URL . '/lostpass.php">';
    echo '<input type="hidden" name="uid" value="' . (int)$uid . '">';
    echo '<input type="hidden" name="token" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';

    if (isset($GLOBALS['xoopsSecurity']) && is_object($GLOBALS['xoopsSecurity'])) {
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
    }

    echo '<div>' . _US_PASSWORD . '<br>';
    echo '<input type="password" name="pass" size="21" autocomplete="new-password" required>';
    echo '</div><br>';

    echo '<div>' . _US_VERIFYPASS . '<br>';
    echo '<input type="password" name="vpass" size="21" autocomplete="new-password" required>';
    echo '</div><br>';

    echo '<div class="xoops-form-element-caption-required">';
    echo sprintf(_US_PWDTOOSHORT, (string)$minPw);
    echo '</div><br>';

    echo '<div><input type="submit" value="' . htmlspecialchars(_US_SUBMIT, ENT_QUOTES) . '"></div>';
    echo '</form>';
    echo '</fieldset>';
}
