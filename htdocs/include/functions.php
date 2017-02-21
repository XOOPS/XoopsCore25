<?php
/**
 *  Xoops Functions
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
 * @since               2.0.0
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * xoops_getHandler()
 *
 * @param mixed $name
 * @param mixed $optional
 *
 * @return XoopsObjectHandler|false
 */
function xoops_getHandler($name, $optional = false)
{
    static $handlers;
    $name = strtolower(trim($name));
    if (!isset($handlers[$name])) {
        if (file_exists($hnd_file = XOOPS_ROOT_PATH . '/kernel/' . $name . '.php')) {
            require_once $hnd_file;
        }
        $class = 'Xoops' . ucfirst($name) . 'Handler';
        if (class_exists($class)) {
            $xoopsDB         = XoopsDatabaseFactory::getDatabaseConnection();
            $handlers[$name] = new $class($xoopsDB);
        }
    }
    if (!isset($handlers[$name])) {
        trigger_error('Class <strong>' . $class . '</strong> does not exist<br>Handler Name: ' . $name, $optional ? E_USER_WARNING : E_USER_ERROR);
    }
    if (isset($handlers[$name])) {
        return $handlers[$name];
    }
    $inst = false;

    return $inst;
}

/**
 * xoops_getModuleHandler()
 *
 * @param mixed $name
 * @param mixed $module_dir
 * @param mixed $optional
 * @return XoopsObjectHandler|false
 */
function xoops_getModuleHandler($name = null, $module_dir = null, $optional = false)
{
    static $handlers;
    // if $module_dir is not specified
    if (!isset($module_dir)) {
        // if a module is loaded
        if (isset($GLOBALS['xoopsModule']) && is_object($GLOBALS['xoopsModule'])) {
            $module_dir = $GLOBALS['xoopsModule']->getVar('dirname', 'n');
        } else {
            trigger_error('No Module is loaded', E_USER_ERROR);
        }
    } else {
        $module_dir = trim($module_dir);
    }
    $name = (!isset($name)) ? $module_dir : trim($name);
    if (!isset($handlers[$module_dir][$name])) {
        if (file_exists($hnd_file = XOOPS_ROOT_PATH . "/modules/{$module_dir}/class/{$name}.php")) {
            include_once $hnd_file;
        }
        $class = ucfirst(strtolower($module_dir)) . ucfirst($name) . 'Handler';
        if (class_exists($class)) {
            $xoopsDB                      = XoopsDatabaseFactory::getDatabaseConnection();
            $handlers[$module_dir][$name] = new $class($xoopsDB);
        }
    }
    if (!isset($handlers[$module_dir][$name])) {
        trigger_error('Handler does not exist<br>Module: ' . $module_dir . '<br>Name: ' . $name, $optional ? E_USER_WARNING : E_USER_ERROR);
    }
    if (isset($handlers[$module_dir][$name])) {
        return $handlers[$module_dir][$name];
    }
    $inst = false;

    return $inst;
}

/**
 * XOOPS class loader wrapper
 *
 * Temporay solution for XOOPS 2.3
 *
 * @param string $name                                          Name of class to be loaded
 * @param string $type                                          domain of the class, potential values:   core - located in /class/;
 *                                                              framework - located in /Frameworks/;
 *                                                              other - module class, located in /modules/[$type]/class/
 *
 * @return boolean
 */
function xoops_load($name, $type = 'core')
{
    if (!class_exists('XoopsLoad')) {
        require_once XOOPS_ROOT_PATH . '/class/xoopsload.php';
    }

    return XoopsLoad::load($name, $type);
}

/**
 * XOOPS language loader wrapper
 *
 * Temporay solution, not encouraged to use
 *
 * @param   string $name     Name of language file to be loaded, without extension
 * @param   string $domain   Module dirname; global language file will be loaded if $domain is set to 'global' or not specified
 * @param   string $language Language to be loaded, current language content will be loaded if not specified
 * @return  boolean
 * @todo    expand domain to multiple categories, e.g. module:system, framework:filter, etc.
 *
 */
function xoops_loadLanguage($name, $domain = '', $language = null)
{
    /**
     * Set pageType
     */
    if ($name === 'pagetype') {
        $name = xoops_getOption('pagetype');
    }
    /**
     * We must check later for an empty value. As xoops_getOption could be empty
     */
    if (empty($name)) {
        return false;
    }
    $language = empty($language) ? $GLOBALS['xoopsConfig']['language'] : $language;
    $path     = ((empty($domain) || 'global' === $domain) ? '' : "modules/{$domain}/") . 'language';
    if (!file_exists($fileinc = $GLOBALS['xoops']->path("{$path}/{$language}/{$name}.php"))) {
        if (!file_exists($fileinc = $GLOBALS['xoops']->path("{$path}/english/{$name}.php"))) {
            return false;
        }
    }
    $ret = include_once $fileinc;

    return $ret;
}

/**
 * YOU SHOULD BE CAREFUL WITH USING THIS METHOD SINCE IT WILL BE DEPRECATED
 */
/**
 * xoops_getActiveModules()
 *
 * Get active modules from cache file
 *
 * @return array
 */
function xoops_getActiveModules()
{
    static $modules_active;
    if (is_array($modules_active)) {
        return $modules_active;
    }
    xoops_load('XoopsCache');
    if (!$modules_active = XoopsCache::read('system_modules_active')) {
        $modules_active = xoops_setActiveModules();
    }

    return $modules_active;
}

/**
 * YOU SHOULD BE CAREFUL WITH USING THIS METHOD SINCE IT WILL BE DEPRECATED
 */
/**
 * xoops_setActiveModules()
 *
 * Write active modules to cache file
 *
 * @return array
 */
function xoops_setActiveModules()
{
    xoops_load('XoopsCache');
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $modules_obj    = $module_handler->getObjects(new Criteria('isactive', 1));
    $modules_active = array();
    foreach (array_keys($modules_obj) as $key) {
        $modules_active[] = $modules_obj[$key]->getVar('dirname');
    }
    unset($modules_obj);
    XoopsCache::write('system_modules_active', $modules_active);

    return $modules_active;
}

/**
 * YOU SHOULD BE CAREFUL WITH USING THIS METHOD SINCE IT WILL BE DEPRECATED
 */
/**
 * xoops_isActiveModule()
 *
 * Checks is module is installed and active
 *
 * @param $dirname
 * @return bool
 */
function xoops_isActiveModule($dirname)
{
    return isset($dirname) && in_array($dirname, xoops_getActiveModules());
}

/**
 * xoops_header()
 *
 * @param mixed $closehead
 * @return void
 */
function xoops_header($closehead = true)
{
    global $xoopsConfig;

    $themeSet = $xoopsConfig['theme_set'];
    $themePath = XOOPS_THEME_PATH . '/' . $themeSet . '/';
    $themeUrl = XOOPS_THEME_URL . '/' . $themeSet . '/';
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    $headTpl = new \XoopsTpl();
    $headTpl->assign(array(
        'closeHead'      => (bool) $closehead,
        'themeUrl'       => $themeUrl,
        'xoops_langcode' => _LANGCODE,
        'xoops_charset'  => _CHARSET,
        'xoops_sitename' => $xoopsConfig['sitename'],
        'xoops_url'      => XOOPS_URL,
    ));

    if (file_exists($themePath . 'theme_autorun.php')) {
        include_once($themePath . 'theme_autorun.php');
    }

    $headItems = array();
    $headItems[] = '<script type="text/javascript" src="' . XOOPS_URL . '/include/xoops.js"></script>';
    $headItems[] = '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/xoops.css">';
    $headItems[] = '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/media/font-awesome/css/font-awesome.min.css">';
    $languageFile = 'language/' . $GLOBALS['xoopsConfig']['language'] . '/style.css';
    if (file_exists($GLOBALS['xoops']->path($languageFile))) {
        $headItems[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $GLOBALS['xoops']->url($languageFile) . '">';
    }
    $themecss = xoops_getcss($xoopsConfig['theme_set']);
    if ($themecss!=='') {
        $headItems[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $themecss . '">';
    }
    $headTpl->assign('headItems', $headItems);

    if (!headers_sent()) {
        header('Content-Type:text/html; charset=' . _CHARSET);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, max-age=1, s-maxage=1, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
    }

    $output = $headTpl->fetch('db:system_popup_header.tpl');
    echo $output;
}

/**
 * xoops_footer
 *
 * @return void
 */
function xoops_footer()
{
    global $xoopsConfig;

    $themeSet = $xoopsConfig['theme_set'];
    $themePath = XOOPS_THEME_URL . '/' . $themeSet . '/';
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    $footTpl = new \XoopsTpl();
    $footTpl->assign(array(
        'themePath'      => $themePath,
        'xoops_langcode' => _LANGCODE,
        'xoops_charset'  => _CHARSET,
        'xoops_sitename' => $xoopsConfig['sitename'],
        'xoops_url'      => XOOPS_URL,
    ));
    $output = $footTpl->fetch('db:system_popup_footer.tpl');
    echo $output;
    ob_end_flush();
}

/**
 * xoops_error
 *
 * @param mixed  $msg
 * @param string $title
 * @return void
 */
function xoops_error($msg, $title = '')
{
    echo '<div class="errorMsg">';
    if ($title != '') {
        echo '<strong>' . $title . '</strong><br><br>';
    }
    if (is_object($msg)) {
        $msg = (array)$msg;
    }
    if (is_array($msg)) {
        foreach ($msg as $key => $value) {
            if (is_numeric($key)) {
                $key = '';
            }
            xoops_error($value, $key);
        }
    } else {
        echo "<div>{$msg}</div>";
    }
    echo '</div>';
}

/**
 * xoops_result
 *
 * @param mixed  $msg
 * @param string $title
 * @return void
 */
function xoops_result($msg, $title = '')
{
    echo '<div class="resultMsg">';
    if ($title != '') {
        echo '<strong>' . $title . '</strong><br><br>';
    }
    if (is_object($msg)) {
        $msg = (array)$msg;
    }
    if (is_array($msg)) {
        foreach ($msg as $key => $value) {
            if (is_numeric($key)) {
                $key = '';
            }
            xoops_result($value, $key);
        }
    } else {
        echo "<div>{$msg}</div>";
    }
    echo '</div>';
}

/**
 * xoops_confirm()
 *
 * @param mixed  $hiddens
 * @param mixed  $action
 * @param mixed  $msg
 * @param string $submit
 * @param mixed  $addtoken
 * @return void
 */
function xoops_confirm($hiddens, $action, $msg, $submit = '', $addtoken = true)
{
    $submit = ($submit != '') ? trim($submit) : _SUBMIT;
    echo '<div class="confirmMsg">' . $msg . '<br>
          <form method="post" action="' . $action . '">';
    foreach ($hiddens as $name => $value) {
        if (is_array($value)) {
            foreach ($value as $caption => $newvalue) {
                echo '<input type="radio" name="' . $name . '" value="' . htmlspecialchars($newvalue) . '" /> ' . $caption;
            }
            echo '<br>';
        } else {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
        }
    }
    if ($addtoken != false) {
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
    }
    echo '<input type="submit" name="confirm_submit" value="' . $submit . '" title="' . $submit . '"/>
          <input type="button" name="confirm_back" value="' . _CANCEL . '" onclick="history.go(-1);" title="' . _CANCEL . '" />
          </form>
          </div>';
}

/**
 * xoops_getUserTimestamp()
 *
 * @param mixed  $time
 * @param string $timeoffset
 * @return int
 */
function xoops_getUserTimestamp($time, $timeoffset = '')
{
    global $xoopsConfig, $xoopsUser;
    if ($timeoffset == '') {
        if ($xoopsUser) {
            $timeoffset = $xoopsUser->getVar('timezone_offset');
        } else {
            $timeoffset = $xoopsConfig['default_TZ'];
        }
    }
    $usertimestamp = (int)$time + ((float)$timeoffset - $xoopsConfig['server_TZ']) * 3600;

    return $usertimestamp;
}

/**
 * Function to display formatted times in user timezone
 * @param        $time
 * @param string $format
 * @param string $timeoffset
 * @return string
 */
function formatTimestamp($time, $format = 'l', $timeoffset = '')
{
    xoops_load('XoopsLocal');

    return XoopsLocal::formatTimestamp($time, $format, $timeoffset);
}

/**
 * Function to calculate server timestamp from user entered time (timestamp)
 * @param      $timestamp
 * @param null $userTZ
 * @return
 */
function userTimeToServerTime($timestamp, $userTZ = null)
{
    global $xoopsConfig;
    if (!isset($userTZ)) {
        $userTZ = $xoopsConfig['default_TZ'];
    }
    $timestamp -= (($userTZ - $xoopsConfig['server_TZ']) * 3600);

    return $timestamp;
}

/**
 * xoops_makepass()
 *
 * @return string
 */
function xoops_makepass()
{
    $makepass  = '';
    $syllables = array(
        'er',
        'in',
        'tia',
        'wol',
        'fe',
        'pre',
        'vet',
        'jo',
        'nes',
        'al',
        'len',
        'son',
        'cha',
        'ir',
        'ler',
        'bo',
        'ok',
        'tio',
        'nar',
        'sim',
        'ple',
        'bla',
        'ten',
        'toe',
        'cho',
        'co',
        'lat',
        'spe',
        'ak',
        'er',
        'po',
        'co',
        'lor',
        'pen',
        'cil',
        'li',
        'ght',
        'wh',
        'at',
        'the',
        'he',
        'ck',
        'is',
        'mam',
        'bo',
        'no',
        'fi',
        've',
        'any',
        'way',
        'pol',
        'iti',
        'cs',
        'ra',
        'dio',
        'sou',
        'rce',
        'sea',
        'rch',
        'pa',
        'per',
        'com',
        'bo',
        'sp',
        'eak',
        'st',
        'fi',
        'rst',
        'gr',
        'oup',
        'boy',
        'ea',
        'gle',
        'tr',
        'ail',
        'bi',
        'ble',
        'brb',
        'pri',
        'dee',
        'kay',
        'en',
        'be',
        'se');
    mt_srand((double)microtime() * 1000000);
    for ($count = 1; $count <= 4; ++$count) {
        if (mt_rand() % 10 == 1) {
            $makepass .= sprintf('%0.0f', (mt_rand() % 50) + 1);
        } else {
            $makepass .= sprintf('%s', $syllables[mt_rand() % 62]);
        }
    }

    return $makepass;
}

/**
 * checkEmail()
 *
 * @param mixed $email
 * @param mixed $antispam
 * @return bool|mixed
 */
function checkEmail($email, $antispam = false)
{
    if (!$email || !preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
        return false;
    }
    $email_array      = explode('@', $email);
    $local_array      = explode('.', $email_array[0]);
    $local_arrayCount = count($local_array);
    for ($i = 0; $i < $local_arrayCount; ++$i) {
        if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/\=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/\=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
            return false;
        }
    }
    if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
        $domain_array = explode('.', $email_array[1]);
        if (count($domain_array) < 2) {
            return false; // Not enough parts to domain
        }
        for ($i = 0; $i < count($domain_array); ++$i) {
            if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                return false;
            }
        }
    }
    if ($antispam) {
        $email = str_replace('@', ' at ', $email);
        $email = str_replace('.', ' dot ', $email);
    }

    return $email;
}

/**
 * formatURL()
 *
 * @param mixed $url
 * @return mixed|string
 */
function formatURL($url)
{
    $url = trim($url);
    if ($url != '') {
        if ((!preg_match('/^http[s]*:\/\//i', $url)) && (!preg_match('/^ftp*:\/\//i', $url)) && (!preg_match('/^ed2k*:\/\//i', $url))) {
            $url = 'http://' . $url;
        }
    }

    return $url;
}

/**
 * Function to get banner html tags for use in templates
 */
function xoops_getbanner()
{
    global $xoopsConfig;

    $db      = XoopsDatabaseFactory::getDatabaseConnection();
    $bresult = $db->query('SELECT COUNT(*) FROM ' . $db->prefix('banner'));
    list($numrows) = $db->fetchRow($bresult);
    if ($numrows > 1) {
        --$numrows;
        mt_srand((double)microtime() * 1000000);
        $bannum = mt_rand(0, $numrows);
    } else {
        $bannum = 0;
    }
    if ($numrows > 0) {
        $bresult = $db->query('SELECT * FROM ' . $db->prefix('banner'), 1, $bannum);
        list($bid, $cid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date, $htmlbanner, $htmlcode) = $db->fetchRow($bresult);
        if ($xoopsConfig['my_ip'] == xoops_getenv('REMOTE_ADDR')) {
            // EMPTY
        } else {
            ++$impmade;
            $db->queryF(sprintf('UPDATE %s SET impmade = %u WHERE bid = %u', $db->prefix('banner'), $impmade, $bid));
            /**
             * Check if this impression is the last one
             */
            if ($imptotal > 0 && $impmade >= $imptotal) {
                $newid = $db->genId($db->prefix('bannerfinish') . '_bid_seq');
                $sql   = sprintf('INSERT INTO %s (bid, cid, impressions, clicks, datestart, dateend) VALUES (%u, %u, %u, %u, %u, %u)', $db->prefix('bannerfinish'), $newid, $cid, $impmade, $clicks, $date, time());
                $db->queryF($sql);
                $db->queryF(sprintf('DELETE FROM %s WHERE bid = %u', $db->prefix('banner'), $bid));
            }
        }
        /**
         * Print the banner
         */
        $bannerobject = '';
        if ($htmlbanner) {
            if ($htmlcode) {
                $bannerobject = $htmlcode;
            } else {
                $bannerobject = $bannerobject . '<div id="xo-bannerfix">';
                // $bannerobject = $bannerobject . '<div id="xo-fixbanner">';
                $bannerobject = $bannerobject . ' <iframe src=' . $imageurl . ' border="0" scrolling="no" allowtransparency="true" width="480px" height="60px" style="border:0" alt="' . $clickurl . ';"> </iframe>';
                $bannerobject .= '</div>';
                // $bannerobject .= '</div>';
            }
        } else {
            $bannerobject = '<div id="xo-bannerfix">';
            if (false !== stripos($imageurl, '.swf')) {
                $bannerobject = $bannerobject . '<div id ="xo-fixbanner">' . '<a href="' . XOOPS_URL . '/banners.php?op=click&amp;bid=' . $bid . '" rel="external" title="' . $clickurl . '"></a></div>' . '<object type="application/x-shockwave-flash" width="468" height="60" data="' . $imageurl . '" style="z-index:100;">' . '<param name="movie" value="' . $imageurl . '" />' . '<param name="wmode" value="opaque" />' . '</object>';
            } else {
                $bannerobject = $bannerobject . '<a href="' . XOOPS_URL . '/banners.php?op=click&amp;bid=' . $bid . '" rel="external" title="' . $clickurl . '"><img src="' . $imageurl . '" alt="' . $clickurl . '" /></a>';
            }

            $bannerobject .= '</div>';
        }

        return $bannerobject;
    }
    return null;
}

/**
 * Function to redirect a user to certain pages
 * @param        $url
 * @param int    $time
 * @param string $message
 * @param bool   $addredirect
 * @param bool   $allowExternalLink
 */
function redirect_header($url, $time = 3, $message = '', $addredirect = true, $allowExternalLink = false)
{
    global $xoopsConfig, $xoopsLogger, $xoopsUserIsAdmin;

    $xoopsPreload = XoopsPreload::getInstance();
    $xoopsPreload->triggerEvent('core.include.functions.redirectheader.start', array($url, $time, $message, $addredirect, $allowExternalLink));
    // under normal circumstance this event will exit, so listen for the .start above
    $xoopsPreload->triggerEvent('core.include.functions.redirectheader', array($url, $time, $message, $addredirect, $allowExternalLink));

    if (preg_match("/[\\0-\\31]|about:|script:/i", $url)) {
        if (!preg_match('/^\b(java)?script:([\s]*)history\.go\(-\d*\)([\s]*[;]*[\s]*)$/si', $url)) {
            $url = XOOPS_URL;
        }
    }
    if (!$allowExternalLink && $pos = strpos($url, '://')) {
        $xoopsLocation = substr(XOOPS_URL, strpos(XOOPS_URL, '://') + 3);
        if (strcasecmp(substr($url, $pos + 3, strlen($xoopsLocation)), $xoopsLocation)) {
            $url = XOOPS_URL;
        }
    }
    if (defined('XOOPS_CPFUNC_LOADED')) {
        $theme = 'default';
    } else {
        $theme = $xoopsConfig['theme_set'];
    }

    require_once XOOPS_ROOT_PATH . '/class/template.php';
    require_once XOOPS_ROOT_PATH . '/class/theme.php';
    $xoopsThemeFactory                = null;
    $xoopsThemeFactory                = new xos_opal_ThemeFactory();
    $xoopsThemeFactory->allowedThemes = $xoopsConfig['theme_set_allowed'];
    $xoopsThemeFactory->defaultTheme  = $theme;
    $xoTheme                          = $xoopsThemeFactory->createInstance(array(
                                                                                'plugins'      => array(),
                                                                                'renderBanner' => false));
    $xoopsTpl                         = $xoTheme->template;
    $xoopsTpl->assign(array(
                          'xoops_theme'      => $theme,
                          'xoops_imageurl'   => XOOPS_THEME_URL . '/' . $theme . '/',
                          'xoops_themecss'   => xoops_getcss($theme),
                          'xoops_requesturi' => htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES),
                          'xoops_sitename'   => htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
                          'xoops_slogan'     => htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES),
                          'xoops_dirname'    => isset($xoopsModule) && is_object($xoopsModule) ? $xoopsModule->getVar('dirname') : 'system',
                          'xoops_pagetitle'  => isset($xoopsModule) && is_object($xoopsModule) ? $xoopsModule->getVar('name') : htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES)));
    if ($xoopsConfig['debug_mode'] == 2 && $xoopsUserIsAdmin) {
        $xoopsTpl->assign('time', 300);
        $xoopsTpl->assign('xoops_logdump', $xoopsLogger->dump());
    } else {
        $xoopsTpl->assign('time', (int)$time);
    }
    if (!empty($_SERVER['REQUEST_URI']) && $addredirect && false !== strpos($url, 'user.php')) {
        if (false === strpos($url, '?')) {
            $url .= '?xoops_redirect=' . urlencode($_SERVER['REQUEST_URI']);
        } else {
            $url .= '&amp;xoops_redirect=' . urlencode($_SERVER['REQUEST_URI']);
        }
    }
    if (defined('SID') && SID && (!isset($_COOKIE[session_name()]) || ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '' && !isset($_COOKIE[$xoopsConfig['session_name']])))) {
        if (false === strpos($url, '?')) {
            $url .= '?' . SID;
        } else {
            $url .= '&amp;' . SID;
        }
    }
    $url = preg_replace('/&amp;/i', '&', htmlspecialchars($url, ENT_QUOTES));
    $xoopsTpl->assign('url', $url);
    $message = trim($message) != '' ? $message : _TAKINGBACK;
    $xoopsTpl->assign('message', $message);
    $xoopsTpl->assign('lang_ifnotreload', sprintf(_IFNOTRELOAD, $url));

    $xoopsTpl->display('db:system_redirect.tpl');
    exit();
}

/**
 * xoops_getenv()
 *
 * @param mixed $key
 * @return string
 */
function xoops_getenv($key)
{
    $ret = '';
    if (array_key_exists($key, $_SERVER) && isset($_SERVER[$key])) {
        $ret = $_SERVER[$key];

        return $ret;
    }
    if (array_key_exists($key, $_ENV) && isset($_ENV[$key])) {
        $ret = $_ENV[$key];

        return $ret;
    }

    return $ret;
}

/**
 * Function to get css file for a certain themeset
 * @param string $theme
 * @return string
 */
function xoops_getcss($theme = '')
{
    if ($theme == '') {
        $theme = $GLOBALS['xoopsConfig']['theme_set'];
    }
    $uagent  = xoops_getenv('HTTP_USER_AGENT');
    $str_css = 'styleNN.css';
    if (false !== stripos($uagent, 'mac')) {
        $str_css = 'styleMAC.css';
    } elseif (preg_match("/MSIE (\d\.\d{1,2})/i", $uagent)) {
        $str_css = 'style.css';
    }
    if (is_dir(XOOPS_THEME_PATH . '/' . $theme)) {
        if (file_exists(XOOPS_THEME_PATH . '/' . $theme . '/' . $str_css)) {
            return XOOPS_THEME_URL . '/' . $theme . '/' . $str_css;
        } elseif (file_exists(XOOPS_THEME_PATH . '/' . $theme . '/style.css')) {
            return XOOPS_THEME_URL . '/' . $theme . '/style.css';
        }
    }
    if (is_dir(XOOPS_THEME_PATH . '/' . $theme . '/css')) {
        if (file_exists(XOOPS_THEME_PATH . '/' . $theme . '/css/' . $str_css)) {
            return XOOPS_THEME_URL . '/' . $theme . '/css/' . $str_css;
        } elseif (file_exists(XOOPS_THEME_PATH . '/' . $theme . '/css/style.css')) {
            return XOOPS_THEME_URL . '/' . $theme . '/css/style.css';
        }
    }

    return '';
}

/**
 * xoops_getMailer()
 *
 * @return \XoopsMailer|\XoopsMailerLocal
 */
function xoops_getMailer()
{
    static $mailer;
    global $xoopsConfig;
    if (is_object($mailer)) {
        return $mailer;
    }
    include_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';
    if (file_exists($file = XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/xoopsmailerlocal.php')) {
        include_once $file;
    } elseif (file_exists($file = XOOPS_ROOT_PATH . '/language/english/xoopsmailerlocal.php')) {
        include_once $file;
    }
    unset($mailer);
    if (class_exists('XoopsMailerLocal')) {
        $mailer = new XoopsMailerLocal();
    } else {
        $mailer = new XoopsMailer();
    }

    return $mailer;
}

/**
 * xoops_getrank()
 *
 * @param integer $rank_id
 * @param mixed   $posts
 * @return
 */
function xoops_getrank($rank_id = 0, $posts = 0)
{
    $db      = XoopsDatabaseFactory::getDatabaseConnection();
    $myts    = MyTextSanitizer::getInstance();
    $rank_id = (int)$rank_id;
    $posts   = (int)$posts;
    if ($rank_id != 0) {
        $sql = 'SELECT rank_title AS title, rank_image AS image FROM ' . $db->prefix('ranks') . ' WHERE rank_id = ' . $rank_id;
    } else {
        $sql = 'SELECT rank_title AS title, rank_image AS image FROM ' . $db->prefix('ranks') . ' WHERE rank_min <= ' . $posts . ' AND rank_max >= ' . $posts . ' AND rank_special = 0';
    }
    $rank          = $db->fetchArray($db->query($sql));
    $rank['title'] = $myts->htmlspecialchars($rank['title']);
    $rank['id']    = $rank_id;

    return $rank;
}

/**
 * Returns the portion of string specified by the start and length parameters. If $trimmarker is supplied, it is appended to the return string. This function works fine with multi-byte characters if mb_* functions exist on the server.
 *
 * @param string $str
 * @param int    $start
 * @param int    $length
 * @param string $trimmarker
 * @return string
 */
function xoops_substr($str, $start, $length, $trimmarker = '...')
{
    xoops_load('XoopsLocal');

    return XoopsLocal::substr($str, $start, $length, $trimmarker);
}

// RMV-NOTIFY
// ################ Notification Helper Functions ##################
// We want to be able to delete by module, by user, or by item.
// How do we specify this??
/**
 * @param $module_id
 *
 * @return mixed
 */
function xoops_notification_deletebymodule($module_id)
{
    $notification_handler = xoops_getHandler('notification');

    return $notification_handler->unsubscribeByModule($module_id);
}

/**
 * xoops_notification_deletebyuser()
 *
 * @param mixed $user_id
 * @return
 */
function xoops_notification_deletebyuser($user_id)
{
    $notification_handler = xoops_getHandler('notification');

    return $notification_handler->unsubscribeByUser($user_id);
}

/**
 * xoops_notification_deletebyitem()
 *
 * @param mixed $module_id
 * @param mixed $category
 * @param mixed $item_id
 * @return
 */
function xoops_notification_deletebyitem($module_id, $category, $item_id)
{
    $notification_handler = xoops_getHandler('notification');

    return $notification_handler->unsubscribeByItem($module_id, $category, $item_id);
}

/**
 * xoops_comment_count()
 *
 * @param mixed $module_id
 * @param mixed $item_id
 * @return
 */
function xoops_comment_count($module_id, $item_id = null)
{
    $comment_handler = xoops_getHandler('comment');
    $criteria        = new CriteriaCompo(new Criteria('com_modid', (int)$module_id));
    if (isset($item_id)) {
        $criteria->add(new Criteria('com_itemid', (int)$item_id));
    }

    return $comment_handler->getCount($criteria);
}

/**
 * xoops_comment_delete()
 *
 * @param mixed $module_id
 * @param mixed $item_id
 * @return bool
 */
function xoops_comment_delete($module_id, $item_id)
{
    if ((int)$module_id > 0 && (int)$item_id > 0) {
        $comment_handler = xoops_getHandler('comment');
        $comments        = $comment_handler->getByItemId($module_id, $item_id);
        if (is_array($comments)) {
            $count       = count($comments);
            $deleted_num = array();
            for ($i = 0; $i < $count; ++$i) {
                if (false !== $comment_handler->delete($comments[$i])) {
                    // store poster ID and deleted post number into array for later use
                    $poster_id = $comments[$i]->getVar('com_uid');
                    if ($poster_id != 0) {
                        $deleted_num[$poster_id] = !isset($deleted_num[$poster_id]) ? 1 : ($deleted_num[$poster_id] + 1);
                    }
                }
            }
            /* @var $member_handler XoopsMemberHandler */
            $member_handler = xoops_getHandler('member');
            foreach ($deleted_num as $user_id => $post_num) {
                // update user posts
                $com_poster = $member_handler->getUser($user_id);
                if (is_object($com_poster)) {
                    $member_handler->updateUserByField($com_poster, 'posts', $com_poster->getVar('posts') - $post_num);
                }
            }

            return true;
        }
    }

    return false;
}

/**
 * xoops_groupperm_deletebymoditem()
 *
 * Group Permission Helper Functions
 *
 * @param mixed $module_id
 * @param mixed $perm_name
 * @param mixed $item_id
 * @return bool
 */
function xoops_groupperm_deletebymoditem($module_id, $perm_name, $item_id = null)
{
    // do not allow system permissions to be deleted
    if ((int)$module_id <= 1) {
        return false;
    }
    /* @var  $gperm_handler XoopsGroupPermHandler */
    $gperm_handler = xoops_getHandler('groupperm');

    return $gperm_handler->deleteByModule($module_id, $perm_name, $item_id);
}

/**
 * xoops_utf8_encode()
 *
 * @param mixed $text
 * @return string
 */
function xoops_utf8_encode(&$text)
{
    xoops_load('XoopsLocal');

    return XoopsLocal::utf8_encode($text);
}

/**
 * xoops_convert_encoding()
 *
 * @param mixed $text
 * @return string
 */
function xoops_convert_encoding(&$text)
{
    return xoops_utf8_encode($text);
}

/**
 * xoops_trim()
 *
 * @param mixed $text
 * @return string
 */
function xoops_trim($text)
{
    xoops_load('XoopsLocal');

    return XoopsLocal::trim($text);
}

/**
 * YOU SHOULD NOT USE THIS METHOD, IT WILL BE REMOVED
 */
/**
 * xoops_getOption()
 *
 * @param mixed $option
 * @internal param string $type
 * @deprecated
 * @return string
 */
function xoops_getOption($option)
{
    $ret = '';
    if (isset($GLOBALS['xoopsOption'][$option])) {
        $ret = $GLOBALS['xoopsOption'][$option];
    }

    return $ret;
}

/**
 * YOU SHOULD NOT USE THIS METHOD, IT WILL BE REMOVED
 */
/**
 * xoops_getConfigOption()
 *
 * @param mixed  $option
 * @param array|string $type
 * @internal param string $dirname
 * @deprecated
 * @return bool
 */
function xoops_getConfigOption($option, $type = 'XOOPS_CONF')
{
    static $coreOptions = array();

    if (is_array($coreOptions) && array_key_exists($option, $coreOptions)) {
        return $coreOptions[$option];
    }
    $ret            = false;
    /* @var $config_handler XoopsConfigHandler  */
    $config_handler = xoops_getHandler('config');
    $configs        = $config_handler->getConfigsByCat(is_array($type) ? $type : constant($type));
    if ($configs) {
        if (isset($configs[$option])) {
            $ret = $configs[$option];
        }
    }
    $coreOptions[$option] = $ret;

    return $ret;
}

/**
 * YOU SHOULD NOT USE THIS METHOD, IT WILL BE REMOVED
 */
/**
 * xoops_setConfigOption()
 *
 * @param mixed $option
 * @param null  $new
 * @return void
@deprecated
 */
function xoops_setConfigOption($option, $new = null)
{
    if (isset($GLOBALS['xoopsConfig'][$option]) && null !== $new) {
        $GLOBALS['xoopsConfig'][$option] = $new;
    }
}

/**
 * YOU SHOULD NOT USE THIS METHOD, IT WILL BE REMOVED
 */
/**
 * xoops_getModuleOption
 *
 * Method for module developers getting a module config item. This could be from any module requested.
 *
 * @param mixed  $option
 * @param string $dirname
 * @return bool
@deprecated
 */
function xoops_getModuleOption($option, $dirname = '')
{
    static $modOptions = array();
    if (is_array($modOptions) && isset($modOptions[$dirname][$option])) {
        return $modOptions[$dirname][$option];
    }

    $ret            = false;
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $module         = $module_handler->getByDirname($dirname);
    /* @var $config_handler XoopsConfigHandler  */
    $config_handler = xoops_getHandler('config');
    if (is_object($module)) {
        $moduleConfig = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        if (isset($moduleConfig[$option])) {
            $ret = $moduleConfig[$option];
        }
    }
    $modOptions[$dirname][$option] = $ret;

    return $ret;
}

/**
 * Determine the base domain name for a URL. The primary use for this is to set the domain
 * used for cookies to represent any subdomains.
 *
 * The registrable domain is determined using the public suffix list. If the domain is not
 * registrable, an empty string is returned. This empty string can be used in setcookie()
 * as the domain, which restricts cookie to just the current host.
 *
 * @param string $url URL or hostname to process
 *
 * @return string the registrable domain or an empty string
 */
function xoops_getBaseDomain($url)
{
    $parts = parse_url($url);
    $host = '';
    if (!empty($parts['host'])) {
        $host = $parts['host'];
        if (strtolower($host) === 'localhost') {
            return 'localhost';
        }
        // bail if this is an IPv4 address (IPv6 will fail later)
        if (false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return '';
        }
        $regdom = new \Geekwright\RegDom\RegisteredDomain();
        $host = $regdom->getRegisteredDomain($host);
    }
    return (null === $host) ? '' : $host;
}

/**
 * YOU SHOULD NOT USE THIS METHOD, IT WILL BE REMOVED
 */
/**
 * Function to get the domain from a URL.
 *
 * @param string $url the URL to be stripped.
 * @return string
 * @deprecated
 */
function xoops_getUrlDomain($url)
{
    $domain = '';
    $_URL   = parse_url($url);

    if (!empty($_URL) || !empty($_URL['host'])) {
        $domain = $_URL['host'];
    }

    return $domain;
}

include_once __DIR__ . '/functions.encoding.php';
include_once __DIR__ . '/functions.legacy.php';
