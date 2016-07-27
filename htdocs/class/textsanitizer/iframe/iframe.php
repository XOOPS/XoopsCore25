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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          textsanitizer
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class MytsIframe
 */
class MytsIframe extends MyTextSanitizerExtension
{
    /**
     * @param $ts
     *
     * @return bool
     */
    public function load($ts)
    {
        $ts->patterns[]     = "/\[iframe=(['\"]?)([^\"']*)\\1]([^\"]*)\[\/iframe\]/sU";
        $ts->replacements[] = "<iframe src='\\3' width='100%' height='\\2' scrolling='auto' frameborder='yes' marginwidth='0' marginheight='0' noresize></iframe>";

        return true;
    }
}
