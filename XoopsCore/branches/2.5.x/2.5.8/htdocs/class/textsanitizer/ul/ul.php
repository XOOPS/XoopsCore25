<?php
/**
 * TextSanitizer extension
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         class
 * @subpackage      textsanitizer
 * @since           2.3.0
 * @author          Wishcraft <simon@xoops.org>
 * @version         $Id$
 * @deprecated
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class MytsUl
 */
class MytsUl extends MyTextSanitizerExtension
{
    /**
     * @param $ts
     *
     * @return bool
     */
    function load(&$ts)
    {
        $ts->patterns[] = "/\[ul](.*)\[\/ul\]/sU";
        $ts->replacements[] = '<ul>\\1</ul>';

        return true;
    }
}
