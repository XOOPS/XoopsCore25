<?php
/**
 * XOOPS admin file
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2021 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 */
$xoopsOption['pagetype'] = 'admin';

include __DIR__ . '/mainfile.php';
include_once $GLOBALS['xoops']->path('include/cp_functions.php');
/**
 * Admin Authentication
 */
if ($xoopsUser) {
    if (!$xoopsUser->isAdmin(-1)) {
        redirect_header('index.php', 2, _AD_NORIGHT);
    }
} else {
    redirect_header('index.php', 2, _AD_NORIGHT);
}

xoops_cp_header();
// ###### Output warn messages for security ######
/**
 * Error warning messages
 */
 // Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
if (!isset($xoopsConfig['admin_warnings_enable']) || $xoopsConfig['admin_warnings_enable']) {
    // recommend the lowest security supported version at time of XOOPS release
    // see: https://php.net/supported-versions.php
    $minRecommendedPHP = '7.3.0';
    if (version_compare(PHP_VERSION, $minRecommendedPHP) < 0) {
        xoops_error(sprintf(_AD_WARNING_OLD_PHP, $minRecommendedPHP));
        echo '<br>';
    }

	$installDirs = glob(XOOPS_ROOT_PATH . '/install*', GLOB_ONLYDIR);
    if (!empty($installDirs)) {
        foreach ($installDirs as $installDir) {
            xoops_error(sprintf(_AD_WARNINGINSTALL, $installDir));
        echo '<br>';
    }
    }

    if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
        xoops_error(sprintf(_AD_WARNINGWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php'));
        echo '<br>';
    }
    // ###### Output warn messages for correct functionality  ######
    if (!is_writable(XOOPS_CACHE_PATH)) {
        xoops_error(sprintf(_AD_WARNINGNOTWRITEABLE, XOOPS_CACHE_PATH));
        echo '<br>';
    }
    if (!is_writable(XOOPS_UPLOAD_PATH)) {
        xoops_error(sprintf(_AD_WARNINGNOTWRITEABLE, XOOPS_UPLOAD_PATH));
        echo '<br>';
    }
    if (!is_writable(XOOPS_COMPILE_PATH)) {
        xoops_error(sprintf(_AD_WARNINGNOTWRITEABLE, XOOPS_COMPILE_PATH));
        echo '<br>';
    }

    //www fits inside www_private, lets add a trailing slash to make sure it doesn't
    if (strpos(XOOPS_PATH . '/', XOOPS_ROOT_PATH . '/') !== false || strpos(XOOPS_PATH . '/', $_SERVER['DOCUMENT_ROOT'] . '/') !== false) {
        xoops_error(sprintf(_AD_WARNINGXOOPSLIBINSIDE, XOOPS_PATH));
        echo '<br>';
    }

    if (strpos(XOOPS_VAR_PATH . '/', XOOPS_ROOT_PATH . '/') !== false || strpos(XOOPS_VAR_PATH . '/', $_SERVER['DOCUMENT_ROOT'] . '/') !== false) {
        xoops_error(sprintf(_AD_WARNINGXOOPSLIBINSIDE, XOOPS_VAR_PATH));
        echo '<br>';
    }
}

if (!empty($_GET['xoopsorgnews']) && !function_exists('xml_parser_create')) {
    xoops_warning(_AD_WARNING_NO_XML);
    echo '<br>';
    unset($_GET['xoopsorgnews']);
}

if (!empty($_GET['xoopsorgnews'])) {
    // Multiple feeds
    $myts     = \MyTextSanitizer::getInstance();
    $rssurl   = array();
    $rssurl[] = 'https://xoops.org/modules/publisher/backend.php';
    if ($URLs = include $GLOBALS['xoops']->path('language/' . xoops_getConfigOption('language') . '/backend.php')) {
        $rssurl = array_unique(array_merge($URLs, $rssurl));
    }
    $rssfile = 'adminnews-' . xoops_getConfigOption('language');
    xoops_load('XoopsCache');
    $items = array();
    if (!$items = XoopsCache::read($rssfile)) {
        XoopsLoad::load('xoopshttpget');
        require_once $GLOBALS['xoops']->path('class/xml/rss/xmlrss2parser.php');

        xoops_load('XoopsLocal');
        $cnt    = 0;
        foreach ($rssurl as $url) {
            try {
                $httpGet = new XoopsHttpGet($url);
            } catch (\RuntimeException $e) {
                echo $e->getMessage() . '<br>';
                break;
            }
            $rssdata    = $httpGet->fetch();
            if (false === $rssdata) {
                echo $httpGet->getError() . '<br>';
            } else {
                $rss2parser = new XoopsXmlRss2Parser($rssdata);
                if (false !== $rss2parser->parse()) {
                    $_items =& $rss2parser->getItems();
                    $count = count($_items);
                    for ($i = 0; $i < $count; ++$i) {
                        $_items[$i]['title'] = XoopsLocal::convert_encoding($_items[$i]['title'], _CHARSET, 'UTF-8');
                        $_items[$i]['description'] = XoopsLocal::convert_encoding($_items[$i]['description'], _CHARSET, 'UTF-8');
                        $items[(string)strtotime($_items[$i]['pubdate']) . '-' . (string)($cnt++)] = $_items[$i];
                    }
                } else {
                    echo $rss2parser->getErrors();
                }
            }
        }
        //krsort($items);
        XoopsCache::write($rssfile, $items, 86400);
    }
    if ($items != '') {
        $ret = '<table id="xoopsorgnews" class="outer width100">';
        foreach (array_keys($items) as $i) {
            $ret .= '<tr class="head"><td><a href="' . htmlspecialchars(trim($items[$i]['link']), ENT_QUOTES) . '" rel="external">';
            $ret .= htmlspecialchars($items[$i]['title'], ENT_QUOTES) . '</a> (' . htmlspecialchars($items[$i]['pubdate'], ENT_QUOTES) . ')</td></tr>';
            if ($items[$i]['description'] != '') {
                $ret .= '<tr><td class="odd">' . $items[$i]['description'];
                if (!empty($items[$i]['guid'])) {
                    $ret .= '&nbsp;&nbsp;<a href="' . htmlspecialchars($items[$i]['guid'], ENT_QUOTES) . '" rel="external" title="">' . _MORE . '</a>';
                }
                $ret .= '</td></tr>';
            } elseif ($items[$i]['guid'] != '') {
                $ret .= '<tr><td class="even aligntop"></td><td colspan="2" class="odd"><a href="' . htmlspecialchars($items[$i]['guid'], ENT_QUOTES) . '" rel="external">' . _MORE . '</a></td></tr>';
            }
        }
        $ret .= '</table>';
        echo $ret;
    }
}
xoops_cp_footer();
