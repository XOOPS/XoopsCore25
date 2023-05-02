<?php
/**
 * XOOPS securityToken Smarty compiler plug-in
 *
 * @copyright   (c) 2016-2022 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      Richard Griffith <richard@geekwright.com>
 */

/**
 * Inserts a XOOPS security token
 *
 * Examples: <{securityToken}>
 *           <{securityToken name="XOOPS_TOKEN"}>
 *
 * Create and render a XoopsFormHiddenToken element. If no 'name' argument is specified
 * the default value will be used.
 *
 * This is intended to replace token generations done in {php} tags which are removed
 * in Smarty 3 and beyond
 *
 * @param string[] $params optional 'name' parameter for token
 * @param Smarty   $smarty
 *
 * @return void
 */
function smarty_function_securityToken($params, $smarty)
{
    if (!empty($params['name'])) {
        $name = $params['name'];
        $name = trim($name, "' \t\n\r\0");
        echo $GLOBALS['xoopsSecurity']->getTokenHTML($name);
    } else {
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
    }
}
