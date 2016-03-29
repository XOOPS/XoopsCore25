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
 * Class MytsMms
 */
class MytsMms extends MyTextSanitizerExtension
{
    /**
     * @param $textarea_id
     *
     * @return array
     */
    public function encode($textarea_id)
    {
        $config     = parent::loadConfig(__DIR__);
        $code       = "<img src='{$this->image_path}/mmssrc.gif' alt='" . _XOOPS_FORM_ALTMMS . "' title='" . _XOOPS_FORM_ALTMMS . "' '" . "' onclick='xoopsCodeMms(\"{$textarea_id}\",\"" . htmlspecialchars(_XOOPS_FORM_ENTERMMSURL, ENT_QUOTES) . "\",\"" . htmlspecialchars(_XOOPS_FORM_ALT_ENTERHEIGHT, ENT_QUOTES) . "\",\"" . htmlspecialchars(_XOOPS_FORM_ALT_ENTERWIDTH, ENT_QUOTES) . "\");'  onmouseover='style.cursor=\"hand\"'/>&nbsp;";
        $javascript = <<<EOH
            function xoopsCodeMms(id,enterMmsPhrase, enterMmsHeightPhrase, enterMmsWidthPhrase)
            {
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var selection="mms://"+selection;
                    var text = selection;
                } else {
                    var text = prompt(enterMmsPhrase+"       mms or http", "mms://");
                }
                var domobj = xoopsGetElementById(id);
                if (text.length > 0 && text != "mms://") {
                    var text2 = prompt(enterMmsWidthPhrase, "480");
                    var text3 = prompt(enterMmsHeightPhrase, "330");
                    var result = "[mms="+text2+","+text3+"]" + text + "[/mms]";
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
     * @param $ts
     *
     * @return bool
     */
    public function load(&$ts)
    {
        $ts->patterns[] = "/\[mms=(['\"]?)([^\"']*),([^\"']*)\\1]([^\"]*)\[\/mms\]/sU";
        $rp             = "<OBJECT id=videowindow1 height='\\3' width='\\2' classid='CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6'>";
        $rp .= "<PARAM NAME=\"URL\" VALUE=\"\\4\">";
        $rp .= "<PARAM NAME=\"rate\" VALUE=\"1\">";
        $rp .= "<PARAM NAME=\"balance\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"currentPosition\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"defaultFrame\" VALUE=\"\">";
        $rp .= "<PARAM NAME=\"playCount\" VALUE=\"1\">";
        $rp .= "<PARAM NAME=\"autoStart\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"currentMarker\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"invokeURLs\" VALUE=\"-1\">";
        $rp .= "<PARAM NAME=\"baseURL\" VALUE=\"\">";
        $rp .= "<PARAM NAME=\"volume\" VALUE=\"50\">";
        $rp .= "<PARAM NAME=\"mute\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"uiMode\" VALUE=\"full\">";
        $rp .= "<PARAM NAME=\"stretchToFit\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"windowlessVideo\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"enabled\" VALUE=\"-1\">";
        $rp .= "<PARAM NAME=\"enableContextMenu\" VALUE=\"-1\">";
        $rp .= "<PARAM NAME=\"fullScreen\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"SAMIStyle\" VALUE=\"\">";
        $rp .= "<PARAM NAME=\"SAMILang\" VALUE=\"\">";
        $rp .= "<PARAM NAME=\"SAMIFilename\" VALUE=\"\">";
        $rp .= "<PARAM NAME=\"captioningID\" VALUE=\"\">";
        $rp .= "<PARAM NAME=\"enableErrorDialogs\" VALUE=\"0\">";
        $rp .= "<PARAM NAME=\"_cx\" VALUE=\"12700\">";
        $rp .= "<PARAM NAME=\"_cy\" VALUE=\"8731\">";
        $rp .= '</OBJECT>';
        $ts->replacements[] = $rp;

        return true;
    }
}
