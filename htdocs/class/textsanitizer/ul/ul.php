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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          textsanitizer
 * @since               2.3.0
 * @author              Wishcraft <simon@xoops.org>
 * @deprecated
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class MytsUl
 */
class MytsUl extends MyTextSanitizerExtension
{
    /**
     * @param MyTextSanitizer $myts
     *
     * @return bool
     */
    public function load(MyTextSanitizer $myts)
    {
        $myts->patterns[]     = "/\[ul](.*)\[\/ul\]/sU";
        $myts->replacements[] = '<ul>\\1</ul>';

        return true;
    }
}
