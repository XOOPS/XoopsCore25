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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         class
 * @subpackage      textsanitizer
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id$
 */

class MytsYoutube extends MyTextSanitizerExtension
{
    /**
     * @param $textarea_id
     *
     * @return array
     */
    function encode($textarea_id)
    {
        $config = parent::loadConfig( __DIR__ );
        $code = "<img src='{$this->image_path}/youtube.gif' alt='" . _XOOPS_FORM_ALTYOUTUBE . "' title='" . _XOOPS_FORM_ALTYOUTUBE . "' '" . "' onclick='xoopsCodeYoutube(\"{$textarea_id}\",\""
            . htmlspecialchars(_XOOPS_FORM_ENTERYOUTUBEURL, ENT_QUOTES) . "\",\""
            . htmlspecialchars(_XOOPS_FORM_ALT_ENTERHEIGHT, ENT_QUOTES)
            . "\",\"" . htmlspecialchars(_XOOPS_FORM_ALT_ENTERWIDTH, ENT_QUOTES) . "\");'  onmouseover='style.cursor=\"hand\"'/>&nbsp;";
        $javascript = <<<EOH
            function xoopsCodeYoutube(id, enterFlashPhrase, enterFlashHeightPhrase, enterFlashWidthPhrase)
            {
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var text = selection;
                } else {
                    var text = prompt(enterFlashPhrase, "");
                }
                var domobj = xoopsGetElementById(id);
                if (text.length > 0) {
                    var text2 = prompt(enterFlashWidthPhrase, "425");
                    var text3 = prompt(enterFlashHeightPhrase, "350");
                    var result = "[youtube="+text2+","+text3+"]" + text + "[/youtube]";
                    xoopsInsertText(domobj, result);
                }
                domobj.focus();
            }
EOH;

        return array($code, $javascript);
    }

    /**
     * @param $match
     *
     * @return string
     */
    static function myCallback($match)
{
    return  self::decode( $match[4], $match[2], $match[3] );
}

    /**
     * @param $ts
     */
    function load(&$ts)
    {
//        $ts->patterns[] = "/\[youtube=(['\"]?)([^\"']*),([^\"']*)\\1]([^\"]*)\[\/youtube\]/esU";
//        $ts->replacements[] = __CLASS__ . "::decode( '\\4', '\\2', '\\3' )";

//mb------------------------------
        $ts->callbackPatterns[] = "/\[youtube=(['\"]?)([^\"']*),([^\"']*)\\1]([^\"]*)\[\/youtube\]/sU";
        $ts->callbacks[] = __CLASS__ . "::myCallback";
//mb------------------------------
    }

    /**
     * @param $url
     * @param $width
     * @param $height
     *
     * @return string
     */
    static function decode($url, $width, $height)
    {
        if (!preg_match("/^http:\/\/(www\.)?youtube\.com\/watch\?v=(.*)/i", $url, $matches)) {
            trigger_error("Not matched: {$url} {$width} {$height}", E_USER_WARNING);

            return "";
        }
        $src = "http://www.youtube.com/v/" . $matches[2];
        if (empty($width) || empty($height)) {
            if (!$dimension = @getimagesize($src) ) {
                return "";
            }
            if (!empty($width)) {
                $height = $dimension[1] * $width /  $dimension[0];
            } elseif (!empty($height)) {
                $width = $dimension[0] * $height /  $dimension[1];
            } else {
                list($width, $height) = array($dimension[0], $dimension[1]);
            }
        }
        $code = "<object width='{$width}' height='{$height}'><param name='movie' value='{$src}'></param>" .
                "<param name='wmode' value='transparent'></param>" .
                "<embed src='{$src}' type='application/x-shockwave-flash' wmode='transparent' width='425' height='350'></embed>" .
                "</object>";

        return $code;
    }
}
