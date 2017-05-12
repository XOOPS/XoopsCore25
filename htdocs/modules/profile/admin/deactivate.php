<?php
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

if (!$GLOBALS['xoopsSecurity']->check()) {
    redirect_header('index.php', 3, _NOPERM);
}

$uid = Xmf\Request::getInt('uid', 0);
if ($uid === 0) {
    redirect_header('index.php', 2, _PROFILE_AM_NOSELECTION);
}
/* @var $member_handler XoopsMemberHandler */
$member_handler = xoops_getHandler('member');
$user           = $member_handler->getUser($uid);
if (!$user || $user->isNew()) {
    redirect_header('index.php', 2, _PROFILE_AM_USERDONEXIT);
}

if (in_array(XOOPS_GROUP_ADMIN, $user->getGroups())) {
    redirect_header('index.php', 2, _PROFILE_AM_CANNOTDEACTIVATEWEBMASTERS);
}
$level = Xmf\Request::getInt('level', 0);
if ($level===0) {
    $user->setVar('level', 0);
    // reset the activation key so it cannot be reused
    // this now gets done at activation, but we do it here also to fix accounts created before the change.
    $actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
    $user->setVar('actkey', $actkey);
    $result = $member_handler->insertUser($user);
} else {
    $result = $member_handler->activateUser($user);
}
if ($result) {
    if ($level !== 0) {
        $message = _PROFILE_AM_USER_ACTIVATED;
    } else {
        $message = _PROFILE_AM_USER_DEACTIVATED;
    }
} else {
    if ($level !== 0) {
        $message = _PROFILE_AM_USER_NOT_ACTIVATED;
    } else {
        $message = _PROFILE_AM_USER_NOT_DEACTIVATED;
    }
}
redirect_header('../userinfo.php?uid=' . $user->getVar('uid'), 3, $message);
