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
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          textsanitizer
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');
global $xoopsConfig;
include_once $GLOBALS['xoops']->path('language/' . $xoopsConfig['language'] . '/misc.php');

/**
 * Class MytsImage
 */
class MytsImage extends MyTextSanitizerExtension
{
    /**
     * @param MyTextSanitizer $myts
     *
     * @return bool
     */
    public function load(MyTextSanitizer $myts)
    {
        static $jsLoaded;

        $config         = parent::loadConfig(__DIR__);
        $myts->patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1 width=(['\"]?)(\d*)\\3]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $myts->patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $myts->patterns[] = "/\[img width=(['\"]?)(\d*)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $myts->patterns[] = "/\[img]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";

        $myts->patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1 id=(['\"]?)(\d*)\\3]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $myts->patterns[] = "/\[img id=(['\"]?)(\d*)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";

        if (empty($myts->config['allowimage'])) {
            $myts->replacements[] = '<a href="\\5" rel="external">\\5</a>';
            $myts->replacements[] = '<a href="\\3" rel="external">\\3</a>';
            $myts->replacements[] = '<a href="\\3" rel="external">\\3</a>';
            $myts->replacements[] = '<a href="\\1" rel="external">\\1</a>';

            $myts->replacements[] = '<a href="' . XOOPS_URL . '/image.php?id=\\4" rel="external" title="\\5">\\5</a>';
            $myts->replacements[] = '<a href="' . XOOPS_URL . '/image.php?id=\\2" rel="external" title="\\3">\\3</a>';
        } else {
            if (!empty($config['resize']) && empty($config['clickable']) && !empty($config['max_width']) && !empty($GLOBALS['xoTheme'])) {
                if (!$jsLoaded) {
                    $jsLoaded = true;
                    $GLOBALS['xoTheme']->addScript('/class/textsanitizer/image/image.js', array(
                        'type' => 'text/javascript'));
                }
                $myts->replacements[] = "<img src='\\5' class='\\2' alt='" . _MSC_RESIZED_IMAGE . "' border='0' onload=\"JavaScript:if(this.width>\\4)this.width=\\4\" />";
                $myts->replacements[] = "<img src='\\3' class='\\2' alt='" . _MSC_RESIZED_IMAGE . "' border='0'" . ($config['resize'] ? "onload=\"javascript:resizeImage(this, " . $config['max_width'] . ")\"" : '') . '/>';
                $myts->replacements[] = "<img src='\\3' alt='" . _MSC_RESIZED_IMAGE . "' border='0' onload=\"if(this.width>\\2)this.width=\\2\" /><br>";
                $myts->replacements[] = "<img src='\\1' alt='" . _MSC_RESIZED_IMAGE . "' border='0'" . ($config['resize'] ? " onload=\"javascript:resizeImage(this, " . $config['max_width'] . ")\"" : '') . '/>';
            } elseif (!empty($config['clickable']) && !empty($config['max_width']) && !empty($GLOBALS['xoTheme'])) {
                if (!$jsLoaded) {
                    $jsLoaded = true;
                    $GLOBALS['xoTheme']->addScript('/class/textsanitizer/image/image.js', array(
                        'type' => 'text/javascript'));
                }
                $myts->replacements[] = "<a href='javascript:loadImage(\"\\5\");'><img src='\\5' class='\\2' alt='" . _MSC_CLICK_TO_OPEN_IMAGE . "' border='0' onload=\"if(this.width>\\4)this.width=\\4\" /></a>";
                $myts->replacements[] = "<a href='javascript:loadImage(\"\\3\");'><img src='\\3' class='\\2' alt='" . _MSC_CLICK_TO_OPEN_IMAGE . "' border='0' " . ($config['resize'] ? "onload=\"javascript:resizeImage(this, " . $config['max_width'] . ")\"" : '') . '/></a>';
                $myts->replacements[] = "<a href='javascript:loadImage(\"\\3\");'><img src='\\3' alt='" . _MSC_CLICK_TO_OPEN_IMAGE . "' border='0' onload=\"if(this.width>\\2)this.width=\\2\" /></a><br>";
                $myts->replacements[] = "<a href='javascript:loadImage(\"\\1\");'><img src='\\1' alt='" . _MSC_CLICK_TO_OPEN_IMAGE . "' border='0' title='" . _MSC_CLICK_TO_OPEN_IMAGE . "'" . ($config['resize'] ? " onload=\"javascript:resizeImage(this, " . $config['max_width'] . ")\"" : '') . '/></a>';
            } else {
                $myts->replacements[] = "<img src='\\5' class='\\2' border='0' alt='" . _MSC_ORIGINAL_IMAGE . "' onload=\"JavaScript:if(this.width>\\4) this.width=\\4\" />";
                $myts->replacements[] = "<img src='\\3' class='\\2' border='0' alt='" . _MSC_ORIGINAL_IMAGE . "' " . ($config['resize'] ? "onload=\"javascript:resizeImage(this, " . $config['max_width'] . ")\"" : '') . '/>';
                $myts->replacements[] = "<img src='\\3' border='0' alt='" . _MSC_ORIGINAL_IMAGE . "' onload=\"JavaScript:if(this.width>\\2) this.width=\\2\" />";
                $myts->replacements[] = "<img src='\\1' border='0' alt='" . _MSC_ORIGINAL_IMAGE . "' " . ($config['resize'] ? " onload=\"javascript:resizeImage(this, " . $config['max_width'] . ")\"" : '') . '/>';
            }
            $myts->replacements[] = '<img src="' . XOOPS_URL . '/image.php?id=\\4" class="\\2" title="\\5" />';
            $myts->replacements[] = '<img src="' . XOOPS_URL . '/image.php?id=\\2" title="\\3" />';
        }

        return true;
    }
}
