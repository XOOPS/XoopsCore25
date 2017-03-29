<?php
/**
 * XOOPS common initialization file
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
 * @package             kernel
 */
defined('XOOPS_MAINFILE_INCLUDED') || die('Restricted access');

global $xoops, $xoopsPreload, $xoopsLogger, $xoopsErrorHandler, $xoopsSecurity, $sess_handler;

/**
 * YOU SHOULD NEVER USE THE FOLLOWING TO CONSTANTS, THEY WILL BE REMOVED
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('NWLINE') or define('NWLINE', "\n");

/**
 * Include files with definitions
 */
include_once XOOPS_ROOT_PATH . '/include/defines.php';
include_once XOOPS_ROOT_PATH . '/include/version.php';
include_once XOOPS_ROOT_PATH . '/include/license.php';

/**
 * Include XoopsLoad
 */
require_once XOOPS_ROOT_PATH . '/class/xoopsload.php';

/**
 * YOU SHOULD BE CAREFUL WITH THE PRELOAD METHODS IN 2.4*, THEY WILL BE DEPRECATED AND IMPLEMENTED IN A DIFFERENT WAY
 */
/**
 *  Create Instance of Preload Object
 */
XoopsLoad::load('preload');
$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.include.common.start');

/**
 * YOU SHOULD BE CAREFUL WITH THE {@xos_kernel_Xoops2}, MOST METHODS WILL BE DEPRECATED
 */
/**
 * Create Instance of xos_kernel_Xoops2 Object
 * Atention, not all methods can be used at this point
 */
XoopsLoad::load('xoopskernel');
$xoops = new xos_kernel_Xoops2();
$xoops->pathTranslation();
$xoopsRequestUri =& $_SERVER['REQUEST_URI'];// Deprecated (use the corrected $_SERVER variable now)

/**
 * Create Instance of xoopsSecurity Object and check Supergolbals
 */
XoopsLoad::load('xoopssecurity');
$xoopsSecurity = new XoopsSecurity();
$xoopsSecurity->checkSuperglobals();

/**
 * Create Instantance XoopsLogger Object
 */
XoopsLoad::load('xoopslogger');
$xoopsLogger       = XoopsLogger::getInstance();
$xoopsErrorHandler = XoopsLogger::getInstance();
$xoopsLogger->startTime();
$xoopsLogger->startTime('XOOPS Boot');

/**
 * Include Required Files
 */
include_once $xoops->path('kernel/object.php');
include_once $xoops->path('class/criteria.php');
include_once $xoops->path('class/module.textsanitizer.php');
include_once $xoops->path('include/functions.php');

/* new installs should create this in mainfile */
if (!defined('XOOPS_COOKIE_DOMAIN')) {
    define('XOOPS_COOKIE_DOMAIN', xoops_getBaseDomain(XOOPS_URL));
}

/**
 * Check Proxy;
 * Requires functions
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$xoopsSecurity->checkReferer(XOOPS_DB_CHKREF)) {
    define('XOOPS_DB_PROXY', 1);
}

/**
 * Get database for making it global
 * Requires XoopsLogger, XOOPS_DB_PROXY;
 */
include_once $xoops->path('class/database/databasefactory.php');
/* @var $xoopsDB XoopsMySQLDatabase */
$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();

/**
 * Get xoops configs
 * Requires functions and database loaded
 */
/* @var $config_handler XoopsConfigHandler  */
$config_handler = xoops_getHandler('config');
$xoopsConfig    = $config_handler->getConfigsByCat(XOOPS_CONF);

/**
 * Merge file and db configs.
 */
if (file_exists($file = $GLOBALS['xoops']->path('var/configs/xoopsconfig.php'))) {
    $fileConfigs = include $file;
    $xoopsConfig = array_merge($xoopsConfig, (array)$fileConfigs);
    unset($fileConfigs, $file);
} else {
    trigger_error('File Path Error: ' . 'var/configs/xoopsconfig.php' . ' does not exist.');
}

/**
 * clickjack protection - Add option to HTTP header restricting using site in an iframe
 */
$xFrameOptions = isset($xoopsConfig['xFrameOptions']) ? $xoopsConfig['xFrameOptions'] : 'sameorigin';
if (!headers_sent() && !empty($xFrameOptions)) {
    header('X-Frame-Options: ' . $xFrameOptions);
}

//check if user set a local timezone (from XavierS)
// $xoops_server_timezone="Etc/GMT";
// if ($xoopsConfig["server_TZ"]>0) {
// $xoops_server_timezone .="+".$xoopsConfig["server_TZ"]; } else{
// $xoops_server_timezone .=$xoopsConfig["server_TZ"]; } date_default_timezone_set($xoops_server_timezone);

//check if 'date.timezone' is set in php.ini
if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

/**
 * Enable Gzip compression, r
 * Requires configs loaded and should go before any output
 */
$xoops->gzipCompression();

/**
 * Start of Error Reportings.
 */
if ($xoopsConfig['debug_mode'] == 1 || $xoopsConfig['debug_mode'] == 2) {
    xoops_loadLanguage('logger');
    error_reporting(E_ALL);
    $xoopsLogger->enableRendering();
    $xoopsLogger->usePopup = ($xoopsConfig['debug_mode'] == 2);
} else {
    error_reporting(0);
    $xoopsLogger->activated = false;
}

/**
 * Check Bad Ip Addressed against database and block bad ones, requires configs loaded
 */
$xoopsSecurity->checkBadips();

/**
 * Load Language settings and defines
 */
$xoopsPreload->triggerEvent('core.include.common.language');
xoops_loadLanguage('global');
xoops_loadLanguage('errors');
xoops_loadLanguage('pagetype');

/**
 * User Sessions
 */
$xoopsUser        = '';
$xoopsUserIsAdmin = false;
/* @var $member_handler XoopsMemberHandler */
$member_handler   = xoops_getHandler('member');
$sess_handler     = xoops_getHandler('session');
if ($xoopsConfig['use_ssl'] && isset($_POST[$xoopsConfig['sslpost_name']]) && $_POST[$xoopsConfig['sslpost_name']] != '') {
    session_id($_POST[$xoopsConfig['sslpost_name']]);
} elseif ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '' && $xoopsConfig['session_expire'] > 0) {
    session_name($xoopsConfig['session_name']);
    session_cache_expire($xoopsConfig['session_expire']);
    @ini_set('session.gc_maxlifetime', $xoopsConfig['session_expire'] * 60);
}
session_set_save_handler(
    array($sess_handler, 'open'),
    array($sess_handler, 'close'),
    array($sess_handler, 'read'),
    array($sess_handler, 'write'),
    array($sess_handler, 'destroy'),
    array($sess_handler, 'gc')
);

if (function_exists('session_status')) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
} else {
    // this should silently fail if session has already started (for PHP 5.3)
    @session_start();
}
$xoopsPreload->triggerEvent('core.behavior.session.start');
/**
 * Remove expired session for xoopsUserId
 */
if ($xoopsConfig['use_mysession']
    && $xoopsConfig['session_name'] != ''
    && !isset($_COOKIE[$xoopsConfig['session_name']])
    && !empty($_SESSION['xoopsUserId'])
) {
    unset($_SESSION['xoopsUserId']);
}

/**
 * Load xoopsUserId from cookie if "Remember me" is enabled.
 */
$rememberClaims = false;
if (empty($_SESSION['xoopsUserId'])
    && !empty($GLOBALS['xoopsConfig']['usercookie'])
) {
    $rememberClaims = \Xmf\Jwt\TokenReader::fromCookie('rememberme', $GLOBALS['xoopsConfig']['usercookie']);
    if (false !== $rememberClaims && !empty($rememberClaims->uid)) {
        $_SESSION['xoopsUserId'] = $rememberClaims->uid;
    } else {
        setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600, '/', XOOPS_COOKIE_DOMAIN, 0, true);
        setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600);
    }
}

/**
 * Log user in and deal with Sessions and Cookies
 */
if (!empty($_SESSION['xoopsUserId'])) {
    $xoopsUser = $member_handler->getUser($_SESSION['xoopsUserId']);
    if (!is_object($xoopsUser)) {
        $xoopsUser = '';
        $_SESSION  = array();
        session_destroy();
        setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600, '/', XOOPS_COOKIE_DOMAIN, 0, true);
        setcookie($GLOBALS['xoopsConfig']['usercookie'], null, time() - 3600);
    } else {
        if (((int)$xoopsUser->getVar('last_login') + 60 * 5) < time()) {
            $sql = 'UPDATE ' . $xoopsDB->prefix('users') . " SET last_login = '" . time()
                   . "' WHERE uid = " . $_SESSION['xoopsUserId'];
            @$xoopsDB->queryF($sql);
        }
        $sess_handler->update_cookie();
        if (isset($_SESSION['xoopsUserGroups'])) {
            $xoopsUser->setGroups($_SESSION['xoopsUserGroups']);
        } else {
            $_SESSION['xoopsUserGroups'] = $xoopsUser->getGroups();
        }
        if (is_object($rememberClaims)) {   // only do during a 'remember me' login
            $user_theme = $xoopsUser->getVar('theme');
            if ($user_theme != $xoopsConfig['theme_set'] && in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
                $_SESSION['xoopsUserTheme'] = $user_theme;
            }
            // update our remember me cookie
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
                XOOPS_COOKIE_DOMAIN,
                (XOOPS_PROT === 'https://'),
                true
            );
        }
        $xoopsUserIsAdmin = $xoopsUser->isAdmin();
    }
}

/**
 * *#@+
 * Debug level for XOOPS
 * Check /xoops_data/configs/xoopsconfig.php for details
 *
 * Note: temporary solution only. Will be re-designed in XOOPS 3.0
 */
if ($xoopsLogger->activated) {
    $level = isset($xoopsConfig['debugLevel']) ? (int)$xoopsConfig['debugLevel'] : 0;
    if (($level == 2 && empty($xoopsUserIsAdmin)) || ($level == 1 && !$xoopsUser)) {
        error_reporting(0);
        $xoopsLogger->activated = false;
    }
    unset($level);
}

/**
 * YOU SHOULD NEVER USE THE FOLLOWING METHOD, IT WILL BE REMOVED
 */
/**
 * Theme Selection
 */
$xoops->themeSelect();
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRenderer');

/**
 * Closed Site
 */
if ($xoopsConfig['closesite'] == 1) {
    include_once $xoops->path('include/site-closed.php');
}

/**
 * Load Xoops Module
 */
if (file_exists('./xoops_version.php')) {
    $url_arr        = explode('/', strstr($_SERVER['PHP_SELF'], '/modules/'));
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $xoopsModule    = $module_handler->getByDirname($url_arr[2]);
    unset($url_arr);

    if (!$xoopsModule || !$xoopsModule->getVar('isactive')) {
        include_once $xoops->path('header.php');
        echo '<h4>' . _MODULENOEXIST . '</h4>';
        include_once $xoops->path('footer.php');
        exit();
    }
    /* @var $moduleperm_handler XoopsGroupPermHandler  */
    $moduleperm_handler = xoops_getHandler('groupperm');
    if ($xoopsUser) {
        if (!$moduleperm_handler->checkRight('module_read', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
            redirect_header(XOOPS_URL, 1, _NOPERM, false);
        }
        $xoopsUserIsAdmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
    } else {
        if (!$moduleperm_handler->checkRight('module_read', $xoopsModule->getVar('mid'), XOOPS_GROUP_ANONYMOUS)) {
            redirect_header(XOOPS_URL . '/user.php?from=' . $xoopsModule->getVar('dirname', 'n'), 1, _NOPERM);
        }
    }

    if ($xoopsModule->getVar('dirname', 'n') !== 'system') {
        if (file_exists($file = $xoops->path('modules/' . $xoopsModule->getVar('dirname', 'n') . '/language/' . $xoopsConfig['language'] . '/main.php'))) {
            include_once $file;
        } elseif (file_exists($file = $xoops->path('modules/' . $xoopsModule->getVar('dirname', 'n') . '/language/english/main.php'))) {
            include_once $file;
        }
        unset($file);
    }

    if ($xoopsModule->getVar('hasconfig') == 1 || $xoopsModule->getVar('hascomments') == 1 || $xoopsModule->getVar('hasnotification') == 1) {
        $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
    }
} elseif ($xoopsUser) {
    $xoopsUserIsAdmin = $xoopsUser->isAdmin(1);
}

/**
 * YOU SHOULD AVOID USING THE FOLLOWING FUNCTION, IT WILL BE REMOVED
 */
//Creates 'system_modules_active' cache file if it has been deleted.
xoops_getActiveModules();

$xoopsLogger->stopTime('XOOPS Boot');
$xoopsLogger->startTime('Module init');

$xoopsPreload->triggerEvent('core.include.common.end');
