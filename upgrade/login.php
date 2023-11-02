<?php
defined('XOOPS_ROOT_PATH') or exit();

if (empty($_POST['uname']) || empty($_POST['pass'])) {
    ?>
    <h2><?php echo _USER_LOGIN; ?></h2>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="uname"><?php echo _USERNAME; ?></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input class="form-control" type="text" name="uname" id="uname" value="" placeholder="<?php echo _USERNAME_PLACEHOLDER; ?>">
        </div>

        <label for="pass"><?php echo _PASSWORD; ?></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input class="form-control" type="password" name="pass" id="pass" placeholder="<?php echo _PASSWORD_PLACEHOLDER; ?>">
        </div>
        <div class="input-group">
            <br>
            <button type="submit" class="btn btn-default"><?php echo _LOGIN; ?></button>
        </div>
    </form>
    <?php
} else {
    $myts  = \MyTextSanitizer::getInstance();
    $uname = !isset($_POST['uname']) ? '' : $myts->addSlashes(trim($_POST['uname']));
    $pass  = !isset($_POST['pass']) ? '' : $myts->addSlashes(trim($_POST['pass']));

    $member_handler = xoops_getHandler('member');

    include_once XOOPS_ROOT_PATH . '/class/auth/authfactory.php';
    if (!@include_once XOOPS_ROOT_PATH . '/language/' . $upgrade_language . '/auth.php') {
        include_once XOOPS_ROOT_PATH . '/language/english/auth.php';
    }
    $xoopsAuth = XoopsAuthFactory::getAuthConnection($uname);
    $user      = $xoopsAuth->authenticate($uname, $pass);

    // For XOOPS 2.2*
    if (!is_object($user)) {
        try {
            $criteria = new CriteriaCompo(new Criteria('loginname', $uname));
            $criteria->add(new Criteria('pass', md5($pass)));
            list($user) = $member_handler->getUsers($criteria);
        } catch (\RuntimeException $e) {
            $user = false;
        }
    }

    $isAllowed = false;
    if (is_object($user) && $user->getVar('level') > 0) {
        $isAllowed = true;
        if ($xoopsConfig['closesite'] == 1) {
            $groups = $user->getGroups();
            if (in_array(XOOPS_GROUP_ADMIN, $groups) || array_intersect($groups, $xoopsConfig['closesite_okgrp'])) {
                $isAllowed = true;
            } else {
                $isAllowed = false;
            }
        }
    }
    if ($isAllowed) {
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
    }

    header('location: ' . XOOPS_URL . '/upgrade/index.php');
    exit();
}
