<?php
/**
 * xoImgUrl Smarty compiler plug-in
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright   The XOOPS project http://www.xoops.org/
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @package		xos_opal
 * @subpackage	xos_opal_Smarty
 * @since       2.0.14
 * @version		$Id$
 */

/**
 * Inserts the URL of a file resource customizable by themes
 *
 * This plug-in works like the {@link smarty_compiler_xoAppUrl() xoAppUrl} plug-in,
 * except that it is intended to generate the URL of resource files customizable by
 * themes.
 *
 * Here the current theme is asked to check if a custom version of the requested file exists, and
 * if one is found its URL is returned. Otherwise, the request will be passed to the
 * theme parents one by one. Ultimately, if no custom version has been found, the resource
 * default URL location will be returned.
 *
 * <b>Note:</b> the themes inheritance system can generate many filesystem accesses depending
 * on your themes configuration. Because of this, the use of the dynamic syntax with this plug-in
 * is not possible right now.
 */

function smarty_compiler_xoImgUrl($argStr, &$smarty)
{
    global $xoops, $xoTheme;
    $path = (isset($xoTheme) && is_object($xoTheme)) ? $xoTheme->resourcePath($argStr) : $argStr;
    return "\necho '" . addslashes($xoops->url($path)) . "';";

}
