<?php
/**
 * XOOPS User
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2021 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             core
 * @since               2.0.0
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 */

use Xmf\Request;

include __DIR__ . '/mainfile.php';
$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.user.start');

xoops_loadLanguage('user');

$op = Request::getString('op', 'main');

if ($op === 'main') {
    if (!is_object($GLOBALS['xoopsUser'])) {
        $GLOBALS['xoopsOption']['template_main'] = 'system_userform.tpl';
        include $GLOBALS['xoops']->path('header.php');
        $GLOBALS['xoopsTpl']->assign('lang_login', _LOGIN);
        $GLOBALS['xoopsTpl']->assign('lang_username', _USERNAME);
        if (Request::hasVar('xoops_redirect', 'GET')) {
            $GLOBALS['xoopsTpl']->assign('redirect_page', htmlspecialchars(Request::getUrl('xoops_redirect', 'GET'), ENT_QUOTES));
        }
        if ($GLOBALS['xoopsConfig']['usercookie']) {
            $GLOBALS['xoopsTpl']->assign('lang_rememberme', _US_REMEMBERME);
        }
        $GLOBALS['xoopsTpl']->assign('lang_password', _PASSWORD);
        $GLOBALS['xoopsTpl']->assign('lang_notregister', _US_NOTREGISTERED);
        $GLOBALS['xoopsTpl']->assign('lang_lostpassword', _US_LOSTPASSWORD);
        $GLOBALS['xoopsTpl']->assign('lang_noproblem', _US_NOPROBLEM);
        $GLOBALS['xoopsTpl']->assign('lang_youremail', _US_YOUREMAIL);
        $GLOBALS['xoopsTpl']->assign('lang_sendpassword', _US_SENDPASSWORD);
        $GLOBALS['xoopsTpl']->assign('mailpasswd_token', $GLOBALS['xoopsSecurity']->createToken());
        include __DIR__ . '/footer.php';
        exit();
    }

    $redirect = Request::getUrl('xoops_redirect', '', 'GET');
    if (!empty($redirect)) {
        $urlParts = parse_url($redirect);
        $xoopsUrlParts = parse_url(XOOPS_URL);
        if (false !== $urlParts) {
            // make sure $redirect is somewhere inside XOOPS_URL
            // catch https:example.com (no //)
            $badScheme = (isset($urlParts['path']) && !isset($urlParts['host']) && isset($urlParts['scheme']));
            // no host or matching host
            $hostMatch = (!isset($urlParts['host'])) || (0 === strcasecmp($urlParts['host'], $xoopsUrlParts['host']));
            // path only, or path matches
            $pathMatch = (isset($urlParts['path']) && !isset($urlParts['host']) && !isset($urlParts['scheme']))
                || ($hostMatch && isset($urlParts['path']) && isset($xoopsUrlParts['path'])
                    && 0 === strncmp($urlParts['path'], $xoopsUrlParts['path'], strlen($xoopsUrlParts['path'])));
            if ($badScheme || !($hostMatch && $pathMatch)) {
                $redirect = XOOPS_URL;
            }
            header('Location: ' . $redirect);
            exit();
        }
    }

    header('Location: ./userinfo.php?uid=' . $GLOBALS['xoopsUser']->getVar('uid'));
    exit();
}

if ($op === 'login') {
    include_once $GLOBALS['xoops']->path('include/checklogin.php');
    exit();
}

if ($op === 'logout') {
    $message = '';
    // Regenerate a new session id and destroy old session
    $GLOBALS['sess_handler']->regenerate_id(true);
    $_SESSION = array();
    xoops_setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600, '/', XOOPS_COOKIE_DOMAIN, 0);
    xoops_setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600, '/');
    // clear entry from online users table
    if (is_object($GLOBALS['xoopsUser'])) {
        /** @var XoopsOnlineHandler $online_handler */
        $online_handler = xoops_getHandler('online');
        $online_handler->destroy($GLOBALS['xoopsUser']->getVar('uid'));
    }
    $xoopsPreload->triggerEvent('core.behavior.user.logout');
    $message = _US_LOGGEDOUT . '<br>' . _US_THANKYOUFORVISIT;
    redirect_header(XOOPS_URL . '/', 1, $message);
}

if ($op === 'actv') {
    $GLOBALS['xoopsLogger']->addDeprecated('Deprecated code. The activation is now handled by register.php');
    $id     = Request::getInt('id', 0, 'GET');
    $actkey = Request::getString('actkey', '', 'GET');
    redirect_header("register.php?id={$id}&amp;actkey={$actkey}", 1, '');
}

if ($op === 'delete') {
    /** @var XoopsConfigHandler $config_handler */
    $config_handler             = xoops_getHandler('config');
    $GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
    if (!$GLOBALS['xoopsUser'] || $GLOBALS['xoopsConfigUser']['self_delete'] != 1) {
        redirect_header(XOOPS_URL . '/', 5, _US_NOPERMISS);
    } else {
        $groups = $GLOBALS['xoopsUser']->getGroups();
        if (in_array(XOOPS_GROUP_ADMIN, $groups)) {
            // users in the webmasters group may not be deleted
            redirect_header('user.php', 5, _US_ADMINNO);
        }
        $ok = Request::getInt('ok', 0, 'POST');
        if ($ok != 1) {
            include $GLOBALS['xoops']->path('header.php');
            xoops_confirm(array('op' => 'delete', 'ok' => 1), 'user.php', _US_SURETODEL . '<br>' . _US_REMOVEINFO);
            include $GLOBALS['xoops']->path('footer.php');
        } else {
            $del_uid        = $GLOBALS['xoopsUser']->getVar('uid');
            /** @var XoopsMemberHandler $member_handler */
            $member_handler = xoops_getHandler('member');
            if (false !== $member_handler->deleteUser($GLOBALS['xoopsUser'])) {
                /** @var XoopsOnlineHandler $online_handler */
                $online_handler = xoops_getHandler('online');
                $online_handler->destroy($del_uid);
                xoops_notification_deletebyuser($del_uid);
                redirect_header(XOOPS_URL . '/', 5, _US_BEENDELED);
            }
            redirect_header(XOOPS_URL . '/', 5, _US_NOPERMISS);
        }
        exit();
    }
}
