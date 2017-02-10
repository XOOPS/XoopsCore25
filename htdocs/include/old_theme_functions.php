<?php
/**
 * XOOPS Deprecated Functions
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
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */

// These are needed when viewing old modules (that don't use Smarty template files) when a theme that use Smarty templates are selected.
// function_exists check is needed for inclusion from the admin side
if (!function_exists('opentable')) {
    /**
     * @param string $width
     */
    function openTable($width = '100%')
    {
        $GLOBALS['xoopsLogger']->addDeprecated("Function '" . __FUNCTION__ . "' in '" . __FILE__ . "' is deprecated, should not be used any more");
        echo '<table width="' . $width . '" cellspacing="0" class="outer"><tr><td class="even">';
    }
}

if (!function_exists('closetable')) {
    function closeTable()
    {
        $GLOBALS['xoopsLogger']->addDeprecated("Function '" . __FUNCTION__ . "' in '" . __FILE__ . "' is deprecated, should not be used any more");
        echo '</td></tr></table>';
    }
}

if (!function_exists('themecenterposts')) {
    /**
     * @param $title
     * @param $content
     */
    function themecenterposts($title, $content)
    {
        $GLOBALS['xoopsLogger']->addDeprecated("Function '" . __FUNCTION__ . "' in '" . __FILE__ . "' is deprecated, should not be used any more");
        echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer"><tr><td class="head">' . $title . '</td></tr><tr><td><br>' . $content . '<br></td></tr></table>';
    }
}
