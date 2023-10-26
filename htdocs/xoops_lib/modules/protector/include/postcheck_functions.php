<?php

use Xmf\Request;

/**
 * @return bool
 */
function protector_postcommon()
{
    global $xoopsUser, $xoopsModule;

    $uname = Request::getString('uname', '', 'POST');
    $pass = Request::getString('pass', '', 'POST');
    $autologin_uname = Request::getString('autologin_uname', '', 'COOKIE');
    $autologin_pass = Request::getString('autologin_pass', '', 'COOKIE');

    // patch for 2.2.x from xoops.org (I know this is not so beautiful...)
    if (defined('XOOPS_VERSION') && substr(XOOPS_VERSION, 6, 3) > 2.0) {
        $requestUri = Request::getString('REQUEST_URI', '', 'SERVER');  // Fetch the REQUEST_URI from the server superglobal
        if (false !== stripos($requestUri, 'modules/system/admin.php?fct=preferences')) {
            /** @var XoopsModuleHandler $module_handler */
            $module_handler = xoops_getHandler('module');

            // Fetch the 'mod' parameter from the GET request and cast it to an integer
            $mod = Request::getInt('mod', 0, 'GET');

            /** @var XoopsModule $module */
            $module = 0 !== $mod ? $module_handler->get($mod) : null;

            if (is_object($module)) {
                $module->getInfo();
            }
        }
    }

    // configs writable check
    $requestUriForWritableCheck = Request::getString('REQUEST_URI', '', 'SERVER');
    if ($requestUriForWritableCheck === '/admin.php' && !is_writable(dirname(__DIR__) . '/configs')) {
        trigger_error('You should turn the directory ' . dirname(__DIR__) . '/configs writable', E_USER_WARNING);
    }

    // Protector object
    require_once dirname(__DIR__) . '/class/protector.php';
    $db        = XoopsDatabaseFactory::getDatabaseConnection();
    $protector = Protector::getInstance();
    $protector->setConn($db->conn);
    $protector->updateConfFromDb();
    $conf = $protector->getConf();
    if (empty($conf)) {
        return true;
    } // not installed yet

    // phpmailer vulnerability
    // http://larholm.com/2007/06/11/phpmailer-0day-remote-execution/
    if (in_array(substr(XOOPS_VERSION, 0, 12), array('XOOPS 2.0.16', 'XOOPS 2.0.13', 'XOOPS 2.2.4'))) {
        /** @var XoopsConfigHandler $config_handler */
        $config_handler    = xoops_getHandler('config');
        $xoopsMailerConfig = $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);
        if ($xoopsMailerConfig['mailmethod'] === 'sendmail' && md5_file(XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.phpmailer.php') === 'ee1c09a8e579631f0511972f929fe36a') {
            echo '<strong>phpmailer security hole! Change the preferences of mail from "sendmail" to another, or upgrade the core right now! (message by protector)</strong>';
        }
    }

    // global enabled or disabled
    if (!empty($conf['global_disabled'])) {
        return true;
    }

    // group1_ips (groupid=1)
    if (is_object($xoopsUser) && in_array(1, $xoopsUser->getGroups())) {
        $group1_ips = $protector->get_group1_ips(true);
        if (implode('', array_keys($group1_ips))) {
            $group1_allow = $protector->ip_match($group1_ips);
            if (empty($group1_allow)) {
                die('This account is disabled for your IP by Protector.<br>Clear cookie if you want to access this site as a guest.');
            }
        }
    }

    // reliable ips
    $remoteAddr = Request::getString('REMOTE_ADDR', '', 'SERVER');
    $reliable_ips = isset($conf['reliable_ips']) ? unserialize($conf['reliable_ips'], array('allowed_classes' => false)) : null;

    if (is_array($reliable_ips)) {
        foreach ($reliable_ips as $reliable_ip) {
            if (!empty($reliable_ip) && preg_match('/' . $reliable_ip . '/', $remoteAddr)) {
                return true;
            }
        }
    }

    // user information (uid and can be banned)
    $can_ban = true;
    $uid = 0;

    if (isset($xoopsUser) && is_object($xoopsUser)) {
        $uid     = $xoopsUser->getVar('uid');
        $userGroups = $xoopsUser->getGroups();
        $bip_except = isset($conf['bip_except']) ? unserialize($conf['bip_except'], array('allowed_classes' => false)) : [];

        $can_ban = (!empty($userGroups) && !empty($bip_except)) ? (count(array_intersect($userGroups, $bip_except)) ? false : true) : true;
    } else {
        // login failed check
        if ((!empty($uname) && !empty($pass)) || (!empty($autologin_uname) && !empty($autologin_pass))) {
            $protector->check_brute_force();
        }
    }

    // CHECK for spammers IPS/EMAILS during POST Actions
    if (isset($conf['stopforumspam_action']) && $conf['stopforumspam_action'] !== 'none') {
        $protector->stopforumspam($uid);
    }

    // If precheck has already judged that they should be banned
    if ($can_ban && $protector->_should_be_banned) {
        $protector->register_bad_ips();
    } elseif ($can_ban && $protector->_should_be_banned_time0) {
        $protector->register_bad_ips(time() + $protector->_conf['banip_time0']);
    }

    // DOS/CRAWLER skipping based on 'dirname' or getcwd()
    $dos_skipping  = false;
    $skip_dirnames = isset($conf['dos_skipmodules']) ? explode('|', $conf['dos_skipmodules']) : array();
    if (!is_array($skip_dirnames)) {
        $skip_dirnames = array();
    }
    if (isset($xoopsModule) && is_object($xoopsModule)) {
        if (in_array($xoopsModule->getVar('dirname'), $skip_dirnames)) {
            $dos_skipping = true;
        }
    } else {
        foreach ($skip_dirnames as $skip_dirname) {
            if ($skip_dirname && false !== strpos(getcwd(), $skip_dirname)) {
                $dos_skipping = true;
                break;
            }
        }
    }

    // module can control DoS skipping
    if (defined('PROTECTOR_SKIP_DOS_CHECK')) {
        $dos_skipping = true;
    }

    // DoS Attack
    if (empty($dos_skipping) && !$protector->check_dos_attack($uid, $can_ban)) {
        $protector->output_log($protector->last_error_type, $uid, true, 16);
    }

    // check session hi-jacking
    $masks = isset($conf['session_fixed_topbit']) ? $conf['session_fixed_topbit'] : null;
    if (is_string($masks)) {
        $maskArray = explode('/', $masks);
    } else {
        $maskArray = array(); // Or some default value that makes sense for your application
    }
    $ipv4Mask = empty($maskArray[0]) ? 24 : $maskArray[0];
    $ipv6Mask = (!isset($maskArray[1])) ? 56 : $maskArray[1];
    $ip = \Xmf\IPAddress::fromRequest();
    $maskCheck = true;
    if (isset($_SESSION['protector_last_ip'])) {
        $maskCheck = $ip->sameSubnet($_SESSION['protector_last_ip'], $ipv4Mask, $ipv6Mask);
    }
    if (!$maskCheck) {
        if (is_object($xoopsUser) && count(array_intersect($xoopsUser->getGroups(), unserialize($conf['groups_denyipmove'], array('allowed_classes' => false))))) {
            $protector->purge(true);
        }
    }
    $_SESSION['protector_last_ip'] = $ip->asReadable();

    // SQL Injection "Isolated /*"
    if (!$protector->check_sql_isolatedcommentin((bool)(isset($conf['isocom_action']) ? $conf['isocom_action'] & 1 : 0))) {
        if (($conf['isocom_action'] & 8) && $can_ban) {
            $protector->register_bad_ips();
        } elseif (($conf['isocom_action'] & 4) && $can_ban) {
            $protector->register_bad_ips(time() + $protector->_conf['banip_time0']);
        }
        $protector->output_log('ISOCOM', $uid, true, 32);
        if ($conf['isocom_action'] & 2) {
            $protector->purge();
        }
    }

    // SQL Injection "UNION"
    if (!$protector->check_sql_union((bool)(isset($conf['union_action']) ? $conf['union_action'] & 1 : 0))) {
        if (($conf['union_action'] & 8) && $can_ban) {
            $protector->register_bad_ips();
        } elseif (($conf['union_action'] & 4) && $can_ban) {
            $protector->register_bad_ips(time() + $protector->_conf['banip_time0']);
        }
        $protector->output_log('UNION', $uid, true, 32);
        if ($conf['union_action'] & 2) {
            $protector->purge();
        }
    }

    if (!empty($_POST)) {
        // SPAM Check
        if (is_object($xoopsUser)) {
            if (!$xoopsUser->isAdmin() && $conf['spamcount_uri4user']) {
                $protector->spam_check((int)$conf['spamcount_uri4user'], $xoopsUser->getVar('uid'));
            }
        } elseif ($conf['spamcount_uri4guest']) {
            $protector->spam_check((int)$conf['spamcount_uri4guest'], 0);
        }

        // filter plugins for POST on postcommon stage
        $protector->call_filter('postcommon_post');
    }

    // register.php Protection - both core and profile module have a register.php
    // There should be an event to trigger this check instead of filename sniffing.
    $scriptFilename = Request::getString('SCRIPT_FILENAME', '', 'SERVER');
    if (basename($scriptFilename) == 'register.php') {
        $protector->call_filter('postcommon_register');
    }

    return null;
}
