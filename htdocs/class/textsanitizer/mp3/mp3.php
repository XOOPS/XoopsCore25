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
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
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
     * @param string $textarea_id
     * @return array
     */
    public function encode($textarea_id)
    {
        $buttonHtml = "<button type='button' class='btn btn-default' onclick='xoopsCodeMp3(\"{$textarea_id}\");' title='"
                      . _XOOPS_FORM_ALTMP3 . "'>"
                      . "<span class='fa-solid fa-music' aria-hidden='true'></span></button>";

        $javascript = <<<EOF
function xoopsCodeMp3(id) {
    var text = prompt("Enter MP3 URL (e.g., https://example.com/audio.mp3)", xoopsGetSelect(id));
    while (text !== null && !text.trim().match(/^https?:\/\/[\w\-\.]+(\:\d+)?\/.+\.mp3(\?.*)?$/i)) {
        alert("Invalid MP3 URL. The URL must begin with http or https and end with .mp3");
        text = prompt("Enter MP3 URL (e.g., https://example.com/audio.mp3)", text);
    }
    if (text && text.trim().length > 0) {
        xoopsInsertText(document.getElementById(id), "[mp3]" + text.trim() + "[/mp3]");
    }
}
EOF;


        return [$buttonHtml, $javascript];
    }

    /**
     * @return bool
     */
    public function load(MyTextSanitizer $myts)
    {
        $myts->callbackPatterns[] = '/\[mp3\](.*?)\[\/mp3\]/s';
        $myts->callbacks[]        = __CLASS__ . '::decode';
        return true;
    }

    /**
     * @param string|array $url
     * @param string|int   $width
     * @param string|int   $height
     * @return string
     */
    public static function decode($url, $width = 0, $height = 0)
    {
        if (is_array($url)) {
            $url = htmlspecialchars($url[1], ENT_QUOTES, 'UTF-8', false); // Prevent double-encoding
        } else {
            $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8', false); // Prevent double-encoding
        }
        return "<audio controls><source src='{$url}' type='audio/mpeg'>Your browser does not support the audio element.</audio>";
    }
}
