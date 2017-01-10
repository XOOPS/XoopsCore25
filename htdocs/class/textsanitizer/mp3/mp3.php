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
 * @copyright       (c) 2000-2017 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          textsanitizer
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class MytsMp3
 */
class MytsMp3 extends MyTextSanitizerExtension
{
    /**
     * @param $textarea_id
     *
     * @return array
     */
    public function encode($textarea_id)
    {
        $code = "<button type='button' class='btn btn-default' onclick='xoopsCodeMp3(\"{$textarea_id}\",\""
            . htmlspecialchars(_XOOPS_FORM_ENTERMP3URL, ENT_QUOTES)
            . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALTMP3
            . "'><span class='fa fa-fw fa-music' aria-hidden='true'></span></button>";

        $javascript = <<<EOF
            function xoopsCodeMp3(id, enterMp3Phrase)
            {
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var text = selection;
                } else {
                    var text = prompt(enterMp3Phrase, "");
                }
                var domobj = xoopsGetElementById(id);
                if (text.length > 0) {
                    var result = "[mp3]" + text + "[/mp3]";
                    xoopsInsertText(domobj, result);
                }
                domobj.focus();
            }
EOF;

        return array(
            $code,
            $javascript);
    }

    /**
     * @param $match
     *
     * @return string
     */
    public static function myCallback($match)
    {
        return self::decode($match[1]);
    }

    /**
     * @param $ts
     *
     * @return bool
     */
    public function load($ts)
    {
        //        $ts->patterns[] = "/\[mp3\](.*?)\[\/mp3\]/es";
        //        $ts->replacements[] = __CLASS__ . "::decode( '\\1' )";
        //mb------------------------------
        $ts->callbackPatterns[] = "/\[mp3\](.*?)\[\/mp3\]/s";
        $ts->callbacks[]        = __CLASS__ . '::myCallback';

        //mb------------------------------
        return true;
    }

    /**
     * @param $url
     *
     * @return string
     */
    public static function decode($url, $width, $height)
    {
        $rp = "<embed flashvars=\"playerID=1&amp;bg=0xf8f8f8&amp;leftbg=0x3786b3&amp;lefticon=0x78bee3&amp;rightbg=0x3786b3&amp;rightbghover=0x78bee3&amp;righticon=0x78bee3&amp;righticonhover=0x3786b3&amp;text=0x666666&amp;slider=0x3786b3&amp;track=0xcccccc&amp;border=0x666666&amp;loader=0x78bee3&amp;loop=no&amp;soundFile={$url}\" quality='high' menu='false' wmode='transparent' pluginspage='http://www.macromedia.com/go/getflashplayer' src='" . XOOPS_URL . "/images/form/player.swf'  width=290 height=24 type='application/x-shockwave-flash'></embed>";

        return $rp;
    }
}
