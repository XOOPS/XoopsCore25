<?php
/**
 * xoAppUrl Smarty compiler plug-in
 *
 * See the enclosed file LICENSE for licensing information. If you did not
 * receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright   (c) 2000-2022 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @package     xos_opal
 * @subpackage  xos_opal_Smarty
 * @since       2.0.14
 */

/**
 * Build application relative URL
 *
 * This plug-in allows you to generate a module location URL. It uses any URL rewriting
 * mechanism and rules you'll have configured for the system.
 *
 * // Generate a URL using a physical path
 * <{xoAppUrl 'modules/something/yourpage.php'}>
 *
 * The path should be in a form understood by Xoops::url()
 *
 * @param string[] $params
 * @param Smarty   $smarty
 * @return string
 */
function smarty_compiler_xoAppUrl($params, Smarty $smarty)
{
    global $xoops;
    $arg = reset($params);
    $url = trim($arg, " '\"\t\n\r\0\x0B");

    if (strpos($url, '/') === 0) {
        $url = 'www' . $url;
    }
    return "<?php echo '" . addslashes(htmlspecialchars($xoops->url($url), ENT_QUOTES)) . "'; ?>";
}
