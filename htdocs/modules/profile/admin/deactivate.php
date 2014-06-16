<?php
include_once dirname(__FILE__) . '/admin_header.php';
xoops_cp_header();

if ( !isset($_REQUEST['uid'])  ) {
    redirect_header("index.php", 2, _PROFILE_AM_NOSELECTION);
}
$member_handler = xoops_gethandler('member');
$user = $member_handler->getUser($_REQUEST['uid']);
if ( !$user || $user->isNew()  ) {
    redirect_header("index.php", 2, _PROFILE_AM_USERDONEXIT);
}

if ( in_array(XOOPS_GROUP_ADMIN, $user->getGroups() ) ) {
    redirect_header("index.php", 2, _PROFILE_AM_CANNOTDEACTIVATEWEBMASTERS);
}
$user->setVar('level', $_REQUEST['level']);
if ( $member_handler->insertUser($user)  ) {
    if ( $_REQUEST['level'] == 1 ) {
        $message = _PROFILE_AM_USER_ACTIVATED;
    } else {
        $message = _PROFILE_AM_USER_DEACTIVATED;
    }
} else {
    if ( $_REQUEST['level'] == 1 ) {
        $message = _PROFILE_AM_USER_NOT_ACTIVATED;
    } else {
        $message = _PROFILE_AM_USER_NOT_DEACTIVATED;
    }
}
redirect_header("../userinfo.php?uid=" . $user->getVar('uid'), 3, $message);
