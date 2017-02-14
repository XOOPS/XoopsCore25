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
 * Class MytsSyntaxhighlight
 */
class MytsSyntaxhighlight extends MyTextSanitizerExtension
{
    /**
     * @param $ts
     * @param $source
     * @param $language
     *
     * @return bool|mixed|string
     */
    public function load($ts, $source, $language)
    {
        $config = parent::loadConfig(__DIR__);
        if (empty($config['highlight'])) {
            return "<pre>{$source}</pre>";
        }
        $source = $ts->undoHtmlSpecialChars($source);
        $source = stripslashes($source);
        $source = MytsSyntaxhighlight::php($source);

        return $source;
    }

    /**
     * @param $text
     *
     * @return mixed|string
     */
    public function php($text)
    {
        $text          = trim($text);
        $addedtag_open = 0;
        if (!strpos($text, '<?php') && (substr($text, 0, 5) !== '<?php')) {
            $text          = '<?php ' . $text;
            $addedtag_open = 1;
        }
        $addedtag_close = 0;
        if (!strpos($text, '?>')) {
            $text .= '?>';
            $addedtag_close = 1;
        }
        $oldlevel = error_reporting(0);

        //There is a bug in the highlight function(php < 5.3) that it doesn't render
        //backslashes properly like in \s. So here we replace any backslashes
        $text = str_replace("\\", 'XxxX', $text);

        $buffer = highlight_string($text, true); // Require PHP 4.20+

        //Placing backspaces back again
        $buffer = str_replace('XxxX', "\\", $buffer);

        error_reporting($oldlevel);
        $pos_open = $pos_close = 0;
        if ($addedtag_open) {
            $pos_open = strpos($buffer, '&lt;?php&nbsp;');
        }
        if ($addedtag_close) {
            $pos_close = strrpos($buffer, '?&gt;');
        }

        $str_open  = $addedtag_open ? substr($buffer, 0, $pos_open) : '';
        $str_close = $pos_close ? substr($buffer, $pos_close + 5) : '';

        $length_open  = $addedtag_open ? $pos_open + 14 : 0;
        $length_text  = $pos_close ? $pos_close - $length_open : 0;
        $str_internal = $length_text ? substr($buffer, $length_open, $length_text) : substr($buffer, $length_open);

        $buffer = $str_open . $str_internal . $str_close;

        return $buffer;
    }
}
