<?php
// This script displays a login screen in a popupbox when SSL is enabled in the preferences. You should use this script only when your server supports SSL. Place this file under your SSL directory

// path to your xoops main directory
$path = '/path/to/xoops/directory';

include $path.'/mainfile.php';
if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/user.php';
$op = (isset($_POST['op']) && $_POST['op'] == 'dologin') ? 'dologin' : 'login';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['userpass']) ? trim($_POST['userpass']) : '';
if ($username == '' || $password == '') {
    $op ='login';
}

echo '
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" />
    <meta http-equiv="content-language" content="'._LANGCODE.'" />
    <title>'.$xoopsConfig['sitename'].'</title>
    <link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/xoops.css" />
';
$style = xoops_getcss($xoopsConfig['theme_set']);
if ($style == '') {
	$style = xoops_getcss($xoopsConfig['theme_set']);
}
if ($style != '') {
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.$style.'" />';
}
echo '
  </head>
  <body>
';

if ($op == 'dologin') {
    $member_handler =& xoops_gethandler('member');
    $myts =& MyTextsanitizer::getInstance();
	$user =& $member_handler->loginUser(addslashes($myts->stripSlashesGPC($username)), addslashes($myts->stripSlashesGPC($password)));
    if (is_object($user)) {
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
		$_SESSION = array();
		$_SESSION['xoopsUserId'] = $user->getVar('uid');
		$_SESSION['xoopsUserGroups'] = $user->getGroups();
		if (!empty($xoopsConfig['use_ssl'])) {
			xoops_confirm(array($xoopsConfig['sslpost_name'] => session_id()), XOOPS_URL.'/misc.php?action=showpopups&amp;type=ssllogin', _US_PRESSLOGIN, _LOGIN);
		} else {
			echo sprintf(_US_LOGGINGU, $user->getVar('uname'));
			echo '<div style="text-align:center;"><input value="'._CLOSE.'" type="button" onclick="document.window.opener.location.reload();document.window.close();" /></div>';
		}
    } else {
        xoops_error(_US_INCORRECTLOGIN.'<br /><a href="login.php">'._BACK.'</a>');
    }
}

if ($op == 'login') {
	echo '
    <div style="text-align: center; padding: 5; margin: 0">
    <form action="login.php" method="post">
      <table class="outer" width="95%">
        <tr>
          <td class="head">'._USERNAME.'</td>
          <td class="even"><input type="text" name="username" value="" /></td>
        </tr>
        <tr>
          <td class="head">'._PASSWORD.'</td>
          <td class="even"><input type="password" name="userpass" value="" /></td>
        </tr>
        <tr>
          <td class="head">&nbsp;</td>
          <td class="even"><input type="hidden" name="op" value="dologin" /><input type="submit" name="submit" value="'._LOGIN.'" /></td>
        </tr>
      </table>
    </form>
    </div>
	';
}

echo '
  </body>
</html>
';
?>