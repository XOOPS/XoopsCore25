<?php
/**
 *  Xoops footer
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

$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.footer.start');

if (!defined('XOOPS_FOOTER_INCLUDED')) {
    define('XOOPS_FOOTER_INCLUDED', 1);

    $xoopsLogger = XoopsLogger::getInstance();
    $xoopsLogger->stopTime('Module display');

    if ($xoopsOption['theme_use_smarty'] == 0) {
        // the old way
        $footer = htmlspecialchars($xoopsConfigMetaFooter['footer']) . '<br><div class="txtcenter small">Powered by XOOPS &copy; <a href="http://xoops.org" rel="external" title="The XOOPS Project">The XOOPS Project</a></div>';
        if (isset($GLOBALS['xoopsOption']['template_main'])) {
            $xoopsTpl->caching = 0;
            $xoopsTpl->display('db:' . $GLOBALS['xoopsOption']['template_main']);
        }
        if (!isset($xoopsOption['show_rblock'])) {
            $xoopsOption['show_rblock'] = 0;
        }
        themefooter($xoopsOption['show_rblock'], $footer);
        xoops_footer();
    } else {
        // RMV-NOTIFY
        include_once $GLOBALS['xoops']->path('include/notification_select.php');
        if (!headers_sent()) {
            header('Content-Type:text/html; charset=' . _CHARSET);
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            //header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Cache-Control: private, no-cache');
            header('Pragma: no-cache');
        }

        //@internal: using global $xoTheme dereferences the variable in old versions, this does not
        if (!isset($xoTheme)) {
            $xoTheme = $GLOBALS['xoTheme'];
        }

        if (isset($GLOBALS['xoopsOption']['template_main']) && $GLOBALS['xoopsOption']['template_main'] != $xoTheme->contentTemplate) {
            trigger_error("xoopsOption['template_main'] should be defined before including header.php", E_USER_WARNING);
            if (false === strpos($GLOBALS['xoopsOption']['template_main'], ':')) {
                $xoTheme->contentTemplate = 'db:' . $GLOBALS['xoopsOption']['template_main'];
            } else {
                $xoTheme->contentTemplate = $GLOBALS['xoopsOption']['template_main'];
            }
        }

        $xoTheme->render();
    }
    $xoopsLogger->stopTime();
}

$xoopsPreload->triggerEvent('core.footer.end');
