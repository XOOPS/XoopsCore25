<?php
/**
 * Extension to mimetype lookup table
 *
 * This file is provided as an helper for objects who need to perform filename to mimetype translations.
 * Common types have been provided, but feel free to add your own one if you need it.
 * <br><br>
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @since               2.0.9.3
 * 
 * @deprecated
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
$GLOBALS['xoopsLogger']->addDeprecated("'/class/mimetypes.inc.php' is deprecated, use '/include/mimetypes.inc.php' directly, called from {$trace[0]['file']} line {$trace[0]['line']}");

return include XOOPS_ROOT_PATH . '/include/mimetypes.inc.php';
