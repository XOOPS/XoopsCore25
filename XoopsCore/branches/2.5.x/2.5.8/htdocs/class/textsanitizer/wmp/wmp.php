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
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class MytsWmp
 */
class MytsWmp extends MyTextSanitizerExtension
{
    /**
     * @param $textarea_id
     *
     * @return array
     */
    function encode($textarea_id)
    {
        $config = parent::loadConfig(dirname(__FILE__));
        $code = "<img src='{$this->image_path}/wmp.gif' alt='" . _XOOPS_FORM_ALTWMP . "' title='" . _XOOPS_FORM_ALTWMP . "' '" . "' onclick='xoopsCodeWmp(\"{$textarea_id}\",\""
            . htmlspecialchars(_XOOPS_FORM_ENTERWMPURL, ENT_QUOTES) . "\",\""
            . htmlspecialchars(_XOOPS_FORM_ALT_ENTERHEIGHT, ENT_QUOTES) . "\",\""
            . htmlspecialchars(_XOOPS_FORM_ALT_ENTERWIDTH, ENT_QUOTES) . "\");'  onmouseover='style.cursor=\"hand\"'/>&nbsp;";
        $javascript = <<<EOH
            function xoopsCodeWmp(id, enterWmpPhrase, enterWmpHeightPhrase, enterWmpWidthPhrase)
            {
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var text = selection;
                } else {
                    var text = prompt(enterWmpPhrase, "");
                }
                var domobj = xoopsGetElementById(id);
                if (text.length > 0) {
                    var text2 = prompt(enterWmpWidthPhrase, "480");
                    var text3 = prompt(enterWmpHeightPhrase, "330");
                    var result = "[wmp="+text2+","+text3+"]" + text + "[/wmp]";
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
     */
    function load(&$ts)
    {
        $ts->patterns[] = "/\[wmp=(['\"]?)([^\"']*),([^\"']*)\\1]([^\"]*)\[\/wmp\]/sU";
        $rp = "<object classid=\"clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6\" id=\"WindowsMediaPlayer\" width=\"\\2\" height=\"\\3\">\n";
        $rp .= "<param name=\"URL\" value=\"\\4\">\n";
        $rp .= "<param name=\"AutoStart\" value=\"0\">\n";
        $rp .= "<embed autostart=\"0\" src=\"\\4\" type=\"video/x-ms-wmv\" width=\"\\2\" height=\"\\3\" controls=\"ImageWindow\" console=\"cons\"> </embed>";
        $rp .= "</object>\n";
        $ts->replacements[] = $rp;
    }
}
