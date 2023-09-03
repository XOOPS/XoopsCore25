<?php
/**
 * XOOPS year Smarty plug-in -- returns the current year
 *
 * @copyright   XOOPS Project (http://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      Richard Griffith <richard@geekwright.com>
 */

/**
 * Insert the current year
 *
 * @param $params
 * @param $smarty
 * @return null
 */
function smarty_function_year($params, &$smarty)
{
    $time = new DateTime();
    echo $time->format('Y');
}
