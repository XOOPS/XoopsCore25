<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_link
 * Version:  1.0
 * Author:     Skalpa Keo <skalpa@xoops.org>
 * Purpose:  format URL for linking to specific Xoops page
 * Input:    module = module to link to (optional, default to current module)
 *           page   = page to link to (optional, default to current page)
 *           params = query string parameters (optional, default to empty)
 *                  ex: urlparm1=,urlparm2,urlparm3=val3, etc.....
 *                      urlparm3 value will be set to val3
 *                      urlparm2 value will keep current one (no = sign)
 *                      urlparm1 value will be set to empty ( = sign, but nothing after)
 *
 *          I.e: The template called by 'index.php?cid=5' calls this function with
 *              {xoops_link page="viewcat.php" urlvars="cid,orderby=titleA"}>
 *              Then the generated URL will be:
 *              XOOPS_URL/modules/MODULENAME/viewcat.php?cid=5&orderby=titleA
 * -------------------------------------------------------------
 */

/**
 * @param $params
 * @param $smarty
 */
function smarty_function_xoops_link($params, &$smarty)
{
    $urlstr = '';
    if (isset($params['urlvars'])) {
        $szvars = explode('&', $params['urlvars']);
        $vars   = array();
        // Split the string making an array from the ('name','value') pairs
        foreach ($szvars as $szvar) {
            $pos = strpos($szvar, '=');
            if ($pos != false) {             // If a value is specified, use it
                $vars[] = array('name' => substr($szvar, 0, $pos), 'value' => substr($szvar, $pos + 1));
            } else {                         // Otherwise use current one (if any)
                if (isset($_POST[$szvar])) {
                    $vars[] = array('name' => $szvar, 'value' => $_POST[$szvar]);
                } elseif (isset($_GET[$szvar])) {
                    $vars[] = array('name' => $szvar, 'value' => $_GET[$szvar]);
                }
            }
        }
        // Now reconstruct query string from specified variables
        foreach ($vars as $var) {
            $urlstr = "$urlstr&{$var['name']}={$var['value']}";
        }
        if (strlen($urlstr) > 0) {
            $urlstr = '?' . substr($urlstr, 1);
        }
    }

    // Get default module/page from current ones if necessary
    $module = '';
    $page   = '';
    if (!isset($params['module'])) {
        if (isset($GLOBALS['xoopsModule']) && is_object($GLOBALS['xoopsModule'])) {
            $module = $GLOBALS['xoopsModule']->getVar('dirname');
        }
    } else {
        $module = $params['module'];
    }
    if (!isset($params['page'])) {
        $cur  = $_SERVER['PHP_SELF'];
        $page = substr($cur, strrpos($cur, '/') + 1);
    } else {
        $page = $params['page'];
    }
    // Now, return entire link URL :-)
    if (empty($module)) {
        echo XOOPS_URL . "/{$page}" . $urlstr;
    } else {
        echo XOOPS_URL . "/modules/{$module}/{$page}" . $urlstr;
    }
}
