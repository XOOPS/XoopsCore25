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
 * Class MytsFlash
 */
class MytsFlash extends MyTextSanitizerExtension
{
    /**
     * @param $textarea_id
     *
     * @return array
     */
    public function encode($textarea_id)
    {
        $config     = parent::loadConfig(__DIR__);
        if ($config['enable_flash_entry'] === false) {
            return array();
        }
        $code = "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeFlash(\"{$textarea_id}\",\""
            . htmlspecialchars(_XOOPS_FORM_ENTERFLASHURL, ENT_QUOTES) . "\",\""
            . htmlspecialchars(_XOOPS_FORM_ALT_ENTERHEIGHT, ENT_QUOTES) . "\",\""
            . htmlspecialchars(_XOOPS_FORM_ALT_ENTERWIDTH, ENT_QUOTES) . "\", \""
            . $config['detect_dimension'] . "\");' onmouseover='style.cursor=\"hand\"' title='"
            . _XOOPS_FORM_ALTFLASH . "'><span class='fa fa-fw fa-flash' aria-hidden='true'></span></button>";
        $javascript = <<<EOF
            function xoopsCodeFlash(id, enterFlashPhrase, enterFlashHeightPhrase, enterFlashWidthPhrase, enableDimensionDetect)
            {
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var text = selection;
                } else {
                    var text = prompt(enterFlashPhrase, "");
                }
                var domobj = xoopsGetElementById(id);
                if (text.length > 0) {
                    var text2 = enableDimensionDetect ? "" : prompt(enterFlashWidthPhrase, "");
                    var text3 = enableDimensionDetect ? "" : prompt(enterFlashHeightPhrase, "");
                    var result = "[flash="+text2+","+text3+"]" + text + "[/flash]";
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
        return self::decode($match[5], $match[3], $match[4]);
    }

    /**
     * @param $ts
     *
     * @return bool
     */
    public function load($ts)
    {
        //        $ts->patterns[] = "/\[(swf|flash)=(['\"]?)([^\"']*),([^\"']*)\\2]([^\"]*)\[\/\\1\]/esU";
        //        $ts->replacements[] = __CLASS__ . "::decode( '\\5', '\\3', '\\4' )";

        //mb------------------------------
        $ts->callbackPatterns[] = "/\[(swf|flash)=(['\"]?)([^\"']*),([^\"']*)\\2]([^\"]*)\[\/\\1\]/sU";
        $ts->callbacks[]        = __CLASS__ . '::myCallback';

        //mb------------------------------
        return true;
    }

    /**
     * @param $url
     * @param $width
     * @param $height
     *
     * @return string
     */
    public static function decode($url, $width, $height)
    {
        $config = parent::loadConfig(__DIR__);

        if ((empty($width) || empty($height)) && !empty($config['detect_dimension'])) {
            if (!$dimension = @getimagesize($url)) {
                return "<a href='{$url}' rel='external' title=''>{$url}</a>";
            }
            if (!empty($width)) {
                $height = $dimension[1] * $width / $dimension[0];
            } elseif (!empty($height)) {
                $width = $dimension[0] * $height / $dimension[1];
            } else {
                list($width, $height) = array(
                    $dimension[0],
                    $dimension[1]);
            }
        }

        $rp = "<object width='{$width}' height='{$height}' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0'>";
        $rp .= "<param name='movie' value='{$url}'>";
        $rp .= "<param name='QUALITY' value='high'>";
        $rp .= "<PARAM NAME='bgcolor' VALUE='#FFFFFF'>";
        $rp .= "<param name='wmode' value='transparent'>";
        $rp .= "<embed src='{$url}' width='{$width}' height='{$height}' quality='high' bgcolor='#FFFFFF' wmode='transparent'  pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'></embed>";
        $rp .= '</object>';

        return $rp;
    }
}
