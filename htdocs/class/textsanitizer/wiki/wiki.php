<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * TextSanitizer extension
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
 * Class MytsWiki
 */
class MytsWiki extends MyTextSanitizerExtension
{
    /**
     * @param $textarea_id
     *
     * @return array
     */
    public function encode($textarea_id)
    {
        $config     = parent::loadConfig(__DIR__);
        $code = "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeWiki(\"{$textarea_id}\",\""
            . htmlspecialchars(_XOOPS_FORM_ENTERWIKITERM, ENT_QUOTES)
            . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALTWIKI
            . "'><span class='fa fa-fw fa-globe' aria-hidden='true'></span></button>";

        $javascript = <<<EOH
            function xoopsCodeWiki(id, enterWikiPhrase)
            {
                if (enterWikiPhrase == null) {
                    enterWikiPhrase = "Enter the word to be linked to Wiki:";
                }
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var text = selection;
                } else {
                    var text = prompt(enterWikiPhrase, "");
                }
                var domobj = xoopsGetElementById(id);
                if (text != null && text != "") {
                    var result = "[[" + text + "]]";
                    xoopsInsertText(domobj, result);
                }
                domobj.focus();
            }
EOH;

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
        return self::decode($match[1],0 ,0);
    }

    /**
     * @param $ts
     */
    public function load($ts)
    {
        //        $ts->patterns[] = "/\[\[([^\]]*)\]\]/esU";
        //        $ts->replacements[] = __CLASS__ . "::decode( '\\1' )";
        //mb------------------------------
        $ts->callbackPatterns[] = "/\[\[([^\]]*)\]\]/sU";
        $ts->callbacks[]        = __CLASS__ . '::myCallback';
        //mb------------------------------
    }

    /**
     * @param $text
     *
     * @return string
     */
    public static function decode($text, $width, $height)
    {
        $config = parent::loadConfig(__DIR__);
        if (empty($text) || empty($config['link'])) {
            return $text;
        }
        $charset = !empty($config['charset']) ? $config['charset'] : 'UTF-8';
        xoops_load('XoopsLocal');
        $ret = "<a href='" . sprintf($config['link'], urlencode(XoopsLocal::convert_encoding($text, $charset))) . "' rel='external' title=''>{$text}</a>";

        return $ret;
    }
}
